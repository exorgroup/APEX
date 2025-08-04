<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Contract interface that all message providers must implement to ensure consistent API across different providers
 * 
 * File location: apex/hermes/src/Providers/Contracts/MessageProvider.php
 */

namespace Apex\Hermes\Providers\Contracts;

use CMText\TextClientResult;

interface MessageProvider
{
    /**
     * Send a standard SMS message
     * 
     * @param string $messageText The message content
     * @param string $sender The sender name or number
     * @param array $recipients Array of recipient phone numbers
     * @param string|null $reference Optional reference for tracking
     * @return TextClientResult
     * @throws \Exception
     */
    public function sendMessage(
        string $messageText,
        string $sender,
        array $recipients,
        ?string $reference = null
    ): TextClientResult;

    /**
     * Send a rich message with media capabilities
     * 
     * @param string $messageText The message content
     * @param string $sender The sender name or number
     * @param array $recipients Array of recipient phone numbers
     * @param string|null $reference Optional reference for tracking
     * @param string $channel The channel to use (SMS, WHATSAPP, etc.)
     * @param string|null $hybridAppKey Application key for certain channels
     * @param array|null $media Media object with url, type, and caption
     * @return TextClientResult
     * @throws \Exception
     */
    public function sendRichMessage(
        string $messageText,
        string $sender,
        array $recipients,
        ?string $reference,
        string $channel,
        ?string $hybridAppKey,
        ?array $media
    ): TextClientResult;

    /**
     * Get the full result from the last operation
     * 
     * @param TextClientResult $result
     * @return array
     */
    public function getResult(TextClientResult $result): array;

    /**
     * Get the status code from the result
     * 
     * @param TextClientResult $result
     * @return int
     */
    public function getStatusCode(TextClientResult $result): int;
}
