<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: CM Telecom provider implementation for sending SMS and Rich messages through CM's API
 * 
 * File location: apex/hermes/src/Providers/CMTelecom/CMProvider.php
 */

namespace Apex\Hermes\Providers\CMTelecom;

use Apex\Hermes\Providers\Contracts\MessageProvider;
use Apex\Hermes\Helpers\TextClientResultHelper;
use CMText\TextClient;
use CMText\TextClientResult;
use CMText\Message;
use CMText\RichContent\Messages\MediaMessage;
use CMText\Channels;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CMProvider implements MessageProvider
{
    /**
     * @var TextClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * Initialize the CM provider
     */
    public function __construct()
    {
        try {
            $this->apiKey = $this->getApiKey();
            $this->client = new TextClient($this->apiKey);
        } catch (\Exception $e) {
            Log::info('Error in CMProvider::__construct - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get API key from database
     * 
     * @return string
     * @throws Exception
     */
    protected function getApiKey(): string
    {
        try {
            $apiKey = DB::connection('hermes')
                ->table('hermes_api_keys')
                ->where('provider', 'cm')
                ->where('active', true)
                ->first();

            if (!$apiKey) {
                throw new Exception('No active CM API key found');
            }

            // Decrypt the API key if encryption is enabled
            if (Config::get('hermes.encrypt_keys', true)) {
                return decrypt($apiKey->provider_api_key);
            }

            return $apiKey->provider_api_key;
        } catch (\Exception $e) {
            Log::info('Error in CMProvider::getApiKey - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send a standard SMS message
     * 
     * @param string $messageText The message content
     * @param string $sender The sender name or number
     * @param array $recipients Array of recipient phone numbers
     * @param string|null $reference Optional reference for tracking
     * @return TextClientResult
     * @throws Exception
     */
    public function sendMessage(string $messageText, string $sender, array $recipients, ?string $reference = null): TextClientResult
    {
        try {
            // Use CM SDK to send message
            $result = $this->client->SendMessage(
                $messageText,
                $sender,
                $recipients,
                $reference
            );

            // CM returns status code 0 for success, normalize it
            if ($result->statusCode === 0 && strpos($result->statusMessage, 'Created') !== false) {
                $result->statusCode = 201;
            }

            // Log the result for auditing
            $this->logMessageSent($result, 'SMS', [
                'message_text' => $messageText,
                'sender' => $sender,
                'recipients' => $recipients,
                'reference' => $reference
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logError('Failed to send SMS', $e, [
                'sender' => $sender,
                'recipients' => $recipients
            ]);

            $errorMessage = $e->getMessage();
            if (strpos($errorMessage, 'status code "0"') !== false) {
                $errorMessage = 'Failed to connect to CM API. Check API key and network connection.';
            }

            throw new \Exception($errorMessage);
        }
    }

    /**
     * Send a rich message with media (WhatsApp/RCS)
     * 
     * @param string $messageText The message content
     * @param string $sender The sender name or number
     * @param array $recipients Array of recipient phone numbers
     * @param string|null $reference Optional reference for tracking
     * @param string $channel The channel to use (SMS, WHATSAPP)
     * @param string|null $hybridAppKey Required for WhatsApp
     * @param array|null $media Media object with url, type, caption
     * @return TextClientResult
     * @throws Exception
     */
    public function sendRichMessage(
        string $messageText,
        string $sender,
        array $recipients,
        ?string $reference,
        string $channel,
        ?string $hybridAppKey,
        ?array $media
    ): TextClientResult {
        try {
            // Create message object
            $message = new Message($messageText, $sender, $recipients);

            // Set reference if provided - Message class stores this in the reference property
            if ($reference) {
                $message->reference = $reference;
            }

            // Set channel - check if we need to use specific channel
            if ($channel === 'WHATSAPP') {
                // CM SDK might require specific setup for WhatsApp
                // This might need adjustment based on actual SDK implementation
                $message->allowedChannels = [Channels::WHATSAPP];
            }

            // Add hybrid app key for WhatsApp if provided
            if ($hybridAppKey) {
                // This might be set differently in the actual SDK
                $message->appKey = $hybridAppKey;
            }

            // Add media if provided
            if ($media && isset($media['url']) && isset($media['type'])) {
                $mediaMessage = new MediaMessage(
                    $media['caption'] ?? $messageText,
                    $media['url'],
                    $media['type']
                );
                $message->richContent = $mediaMessage;
            }

            // Send the message
            $messages = [$message];
            $result = $this->client->send($messages);

            // CM returns status code 0 for success, normalize it
            if ($result->statusCode === 0 && strpos($result->statusMessage, 'Created') !== false) {
                $result->statusCode = 201;
            }

            // Log the result
            $this->logMessageSent($result, $channel, [
                'message_text' => $messageText,
                'sender' => $sender,
                'recipients' => $recipients,
                'reference' => $reference,
                'media' => $media
            ]);

            return $result;
        } catch (\Exception $e) {
            $this->logError('Failed to send rich message', $e, [
                'channel' => $channel,
                'sender' => $sender,
                'recipients' => $recipients
            ]);
            throw $e;
        }
    }

    /**
     * Get the result from the last operation
     * 
     * @param TextClientResult $result
     * @return array
     */
    public function getResult(TextClientResult $result): array
    {
        try {
            return TextClientResultHelper::getResultArray($result);
        } catch (\Exception $e) {
            Log::info('Error in CMProvider::getResult - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return [
                'details' => [],
                'status_message' => 'Error getting result',
                'status_code' => 500
            ];
        }
    }

    /**
     * Get the status code from the result
     * 
     * @param TextClientResult $result
     * @return int
     */
    public function getStatusCode(TextClientResult $result): int
    {
        try {
            return $result->statusCode;
        } catch (\Exception $e) {
            Log::info('Error in CMProvider::getStatusCode - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return 500;
        }
    }

    /**
     * Log successful message sending
     * 
     * @param TextClientResult $result
     * @param string $type
     * @param array $context
     */
    protected function logMessageSent(TextClientResult $result, string $type, array $context): void
    {
        // This would typically log to a database or monitoring service
        // For now, using Laravel's logging
        Log::info('Hermes message sent', [
            'provider' => 'cm',
            'type' => $type,
            'status_code' => $result->statusCode,
            'status_message' => $result->statusMessage,
            'details' => $result->details,
            'context' => $context
        ]);
    }

    /**
     * Log errors
     * 
     * @param string $message
     * @param \Exception $exception
     * @param array $context
     */
    protected function logError(string $message, \Exception $exception, array $context): void
    {
        Log::error('Hermes error: ' . $message, [
            'provider' => 'cm',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context
        ]);
    }
}
