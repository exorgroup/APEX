<?php

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
