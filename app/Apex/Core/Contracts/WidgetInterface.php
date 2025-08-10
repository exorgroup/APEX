<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel PrimeVue Components
 * Description: Interface defining the contract for all APEX widgets
 * File location: app/Apex/Core/Contracts/WidgetInterface.php
 */

namespace App\Apex\Core\Contracts;

interface WidgetInterface
{
    /**
     * Get the widget type identifier
     */
    public function getType(): string;

    /**
     * Get the widget ID
     */
    public function getId(): string;

    /**
     * Get the widget configuration schema
     */
    public function getSchema(): array;

    /**
     * Transform the widget data for frontend consumption
     */
    public function transform(array $config): array;
}
