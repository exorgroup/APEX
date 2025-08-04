<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Main API controller for handling SMS and Rich SMS message requests through the Hermes messaging service
 * 
 * File location: apex/hermes/src/Api/Controllers/MessageController.php
 */

namespace Apex\Hermes\Api\Controllers;

use Apex\Hermes\Providers\CMTelecom\CMProvider;
use Apex\Hermes\Helpers\TextClientResultHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    /**
     * @var CMProvider
     */
    protected $cmProvider;

    /**
     * MessageController constructor.
     */
    public function __construct()
    {
        $this->cmProvider = new CMProvider();
    }

    /**
     * Send a standard SMS message
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {post} /api/v1/sms/send Send SMS
     * @apiParam {String} message_text The message content to send
     * @apiParam {String} sender The sender name or number
     * @apiParam {String|Array} recipient_phone_number Single number or array of recipient phone numbers
     * @apiParam {String} [reference] Optional reference for tracking
     * @apiParam {Boolean} [allow_multi_part=true] Whether to allow messages longer than 160 characters
     */
    public function sendSMS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message_text' => 'required|string',
            'sender' => 'required|string|max:11',
            'recipient_phone_number' => 'required',
            'reference' => 'nullable|string|max:255',
            'allow_multi_part' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get validated data
            $messageText = $request->input('message_text');
            $sender = $request->input('sender');
            $recipients = $request->input('recipient_phone_number');
            $reference = $request->input('reference', null);
            $allowMultiPart = $request->input('allow_multi_part', true);

            // Ensure recipients is an array
            if (!is_array($recipients)) {
                $recipients = [$recipients];
            }

            // Check message length if multi-part is not allowed
            if (!$allowMultiPart) {
                $messageLength = strlen($messageText);
                $containsUnicode = preg_match('/[^\x00-\x7F]/', $messageText);
                $maxLength = $containsUnicode ? 70 : 160;

                if ($messageLength > $maxLength) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Message requires multiple parts to be sent and it is not allowed to',
                        'details' => [
                            'message_length' => $messageLength,
                            'max_allowed' => $maxLength,
                            'contains_unicode' => $containsUnicode
                        ]
                    ], 400);
                }
            }

            // Send message through CM provider
            $result = $this->cmProvider->sendMessage(
                $messageText,
                $sender,
                $recipients,
                $reference
            );

            return response()->json([
                'success' => true,
                'result' => TextClientResultHelper::getResultArray($result),
                'status_code' => $result->statusCode,
                'status_message' => $result->statusMessage
            ], $result->statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a rich SMS/WhatsApp message with media
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {post} /api/v1/sms/send-rich Send Rich SMS
     * @apiParam {String} message_text The message content to send
     * @apiParam {String} sender The sender name or number
     * @apiParam {String|Array} recipient_phone_number Single number or array of recipient phone numbers
     * @apiParam {String} [reference] Optional reference for tracking
     * @apiParam {String} channel The channel to use (SMS or WHATSAPP)
     * @apiParam {String} [hybrid_app_key] Required for WhatsApp messages
     * @apiParam {Object} [media] Media object with url, type, and caption
     */
    public function sendRichSMS(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message_text' => 'required|string',
            'sender' => 'required|string|max:11',
            'recipient_phone_number' => 'required',
            'reference' => 'nullable|string|max:255',
            'channel' => 'required|in:SMS,WHATSAPP',
            'hybrid_app_key' => 'nullable|string',
            'media' => 'nullable|array',
            'media.url' => 'required_with:media|url',
            'media.type' => 'required_with:media|string',
            'media.caption' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Validate WhatsApp specific requirements
        if ($request->input('channel') === 'WHATSAPP' && !$request->has('hybrid_app_key')) {
            return response()->json([
                'success' => false,
                'message' => 'hybrid_app_key is required for WhatsApp messages'
            ], 422);
        }

        try {
            // Get validated data
            $messageText = $request->input('message_text');
            $sender = $request->input('sender');
            $recipients = $request->input('recipient_phone_number');
            $reference = $request->input('reference', null);
            $channel = $request->input('channel');
            $hybridAppKey = $request->input('hybrid_app_key');
            $media = $request->input('media');

            // Ensure recipients is an array
            if (!is_array($recipients)) {
                $recipients = [$recipients];
            }

            // Send rich message through CM provider
            $result = $this->cmProvider->sendRichMessage(
                $messageText,
                $sender,
                $recipients,
                $reference,
                $channel,
                $hybridAppKey,
                $media
            );

            return response()->json([
                'success' => true,
                'result' => TextClientResultHelper::getResultArray($result),
                'status_code' => $result->statusCode,
                'status_message' => $result->statusMessage
            ], $result->statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send rich message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the status of a previously sent message
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @api {get} /api/v1/sms/status Get Message Status
     * @apiParam {String} reference The reference of the message to check
     */
    public function getMessageStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $reference = $request->input('reference');

            // This would typically query a database or call CM's status API
            // For now, returning a mock response
            return response()->json([
                'success' => true,
                'reference' => $reference,
                'status' => 'delivered',
                'message' => 'Message status retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get message status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
