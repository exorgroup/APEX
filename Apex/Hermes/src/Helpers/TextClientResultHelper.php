<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Helper class for handling CM TextClientResult responses with additional utility methods
 * 
 * File location: apex/hermes/src/Helpers/TextClientResultHelper.php
 */

namespace Apex\Hermes\Helpers;

use CMText\TextClientResult;
use Illuminate\Support\Facades\Log;

class TextClientResultHelper
{
    /**
     * Get the result as an array from a TextClientResult
     * 
     * @param TextClientResult $result
     * @return array
     */
    public static function getResultArray(TextClientResult $result): array
    {
        try {
            return [
                'details' => $result->details,
                'status_message' => $result->statusMessage,
                'status_code' => $result->statusCode,
                'success' => self::isSuccess($result),
                'accepted_count' => self::getAcceptedCount($result),
                'rejected_count' => self::getRejectedCount($result),
                'total_parts' => self::getTotalParts($result),
            ];
        } catch (\Exception $e) {
            Log::info('Error in TextClientResultHelper::getResultArray - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return [
                'details' => [],
                'status_message' => 'Error processing result',
                'status_code' => 500,
                'success' => false,
                'accepted_count' => 0,
                'rejected_count' => 0,
                'total_parts' => 0,
            ];
        }
    }

    /**
     * Check if the request was successful
     * 
     * @param TextClientResult $result
     * @return bool
     */
    public static function isSuccess(TextClientResult $result): bool
    {
        try {
            return $result->statusCode >= 200 && $result->statusCode < 300;
        } catch (\Exception $e) {
            Log::info('Error in TextClientResultHelper::isSuccess - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get count of accepted messages
     * 
     * @param TextClientResult $result
     * @return int
     */
    public static function getAcceptedCount(TextClientResult $result): int
    {
        try {
            $count = 0;
            if (is_array($result->details) || is_object($result->details)) {
                foreach ($result->details as $detail) {
                    // Handle both array and object access
                    $status = is_array($detail) ? ($detail['status'] ?? null) : ($detail->status ?? null);
                    if ($status === 'Accepted') {
                        $count++;
                    }
                }
            }
            return $count;
        } catch (\Exception $e) {
            Log::info('Error in TextClientResultHelper::getAcceptedCount - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count of rejected messages
     * 
     * @param TextClientResult $result
     * @return int
     */
    public static function getRejectedCount(TextClientResult $result): int
    {
        try {
            $count = 0;
            if (is_array($result->details) || is_object($result->details)) {
                foreach ($result->details as $detail) {
                    // Handle both array and object access
                    $status = is_array($detail) ? ($detail['status'] ?? null) : ($detail->status ?? null);
                    if ($status === 'Rejected') {
                        $count++;
                    }
                }
            }
            return $count;
        } catch (\Exception $e) {
            Log::info('Error in TextClientResultHelper::getRejectedCount - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total number of message parts sent
     * 
     * @param TextClientResult $result
     * @return int
     */
    public static function getTotalParts(TextClientResult $result): int
    {
        try {
            $parts = 0;
            if (is_array($result->details) || is_object($result->details)) {
                foreach ($result->details as $detail) {
                    // Handle both array and object access
                    $detailParts = is_array($detail) ? ($detail['parts'] ?? 0) : ($detail->parts ?? 0);
                    $parts += (int)$detailParts;
                }
            }
            return $parts;
        } catch (\Exception $e) {
            Log::info('Error in TextClientResultHelper::getTotalParts - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get rejected message details
     * 
     * @param TextClientResult $result
     * @return array
     */
    public static function getRejectedDetails(TextClientResult $result): array
    {
        try {
            $rejected = [];
            if (is_array($result->details) || is_object($result->details)) {
                foreach ($result->details as $detail) {
                    // Handle both array and object access
                    $status = is_array($detail) ? ($detail['status'] ?? null) : ($detail->status ?? null);
                    if ($status === 'Rejected') {
                        if (is_array($detail)) {
                            $rejected[] = [
                                'to' => $detail['to'] ?? 'Unknown',
                                'reason' => $detail['details'] ?? 'No reason provided',
                                'reference' => $detail['reference'] ?? null,
                            ];
                        } else {
                            $rejected[] = [
                                'to' => $detail->to ?? 'Unknown',
                                'reason' => $detail->details ?? 'No reason provided',
                                'reference' => $detail->reference ?? null,
                            ];
                        }
                    }
                }
            }
            return $rejected;
        } catch (\Exception $e) {
            Log::info('Error in TextClientResultHelper::getRejectedDetails - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get accepted message details
     * 
     * @param TextClientResult $result
     * @return array
     */
    public static function getAcceptedDetails(TextClientResult $result): array
    {
        try {
            $accepted = [];
            if (is_array($result->details) || is_object($result->details)) {
                foreach ($result->details as $detail) {
                    // Handle both array and object access
                    $status = is_array($detail) ? ($detail['status'] ?? null) : ($detail->status ?? null);
                    if ($status === 'Accepted') {
                        if (is_array($detail)) {
                            $accepted[] = [
                                'to' => $detail['to'] ?? 'Unknown',
                                'parts' => $detail['parts'] ?? 1,
                                'reference' => $detail['reference'] ?? null,
                            ];
                        } else {
                            $accepted[] = [
                                'to' => $detail->to ?? 'Unknown',
                                'parts' => $detail->parts ?? 1,
                                'reference' => $detail->reference ?? null,
                            ];
                        }
                    }
                }
            }
            return $accepted;
        } catch (\Exception $e) {
            Log::info('Error in TextClientResultHelper::getAcceptedDetails - File: ' . __FILE__ . ' - Error: ' . $e->getMessage());
            return [];
        }
    }
}
