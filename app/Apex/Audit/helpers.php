<?php

/*
Copyright EXOR Group ltd 2025 
Version 1.0.0.0 
APEX Laravel Auditing
Description: Helper function for APEX Audit translations. Provides a global function for easy access to APEX Audit language translations throughout the application.
*/

if (!function_exists('apex_trans')) {
    /**
     * Translate APEX Audit text.
     */
    function apex_trans(string $key, array $parameters = [], ?string $language = null): string
    {
        try {
            return app(\App\Apex\Audit\Services\ApexAuditLanguageService::class)
                ->trans("audit.{$key}", $parameters, $language);
        } catch (\Exception $e) {
            return $key;
        }
    }
}

if (!function_exists('apex_lang')) {
    /**
     * Get current APEX Audit language.
     */
    function apex_lang(): string
    {
        try {
            return app(\App\Apex\Audit\Services\ApexAuditLanguageService::class)
                ->getCurrentLanguage();
        } catch (\Exception $e) {
            return 'en';
        }
    }
}

if (!function_exists('apex_supported_languages')) {
    /**
     * Get supported APEX Audit languages.
     */
    function apex_supported_languages(): array
    {
        try {
            return app(\App\Apex\Audit\Services\ApexAuditLanguageService::class)
                ->getSupportedLanguages();
        } catch (\Exception $e) {
            return ['en' => 'English'];
        }
    }
}

if (!function_exists('apex_set_language')) {
    /**
     * Set APEX Audit language.
     */
    function apex_set_language(string $language): void
    {
        try {
            app(\App\Apex\Audit\Services\ApexAuditLanguageService::class)
                ->setLanguage($language);
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}

if (!function_exists('apex_format_date')) {
    /**
     * Format date according to APEX Audit language settings.
     */
    function apex_format_date(\DateTime $date, string $format = 'medium', ?string $language = null): string
    {
        try {
            return app(\App\Apex\Audit\Services\ApexAuditLanguageService::class)
                ->formatDate($date, $format, $language);
        } catch (\Exception $e) {
            return $date->format('Y-m-d H:i:s');
        }
    }
}

if (!function_exists('apex_format_number')) {
    /**
     * Format number according to APEX Audit language settings.
     */
    function apex_format_number(float $number, int $decimals = 0, ?string $language = null): string
    {
        try {
            return app(\App\Apex\Audit\Services\ApexAuditLanguageService::class)
                ->formatNumber($number, $decimals, $language);
        } catch (\Exception $e) {
            return number_format($number, $decimals);
        }
    }
}
