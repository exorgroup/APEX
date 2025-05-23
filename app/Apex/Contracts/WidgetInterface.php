<?php

namespace App\Apex\Contracts;

interface WidgetInterface
{
    /**
     * Render the widget with the given parameters
     *
     * @param array $params Widget parameters
     * @return string Rendered HTML content
     */
    public function render(array $params = []): string;

    /**
     * Get default parameters for the widget
     *
     * @return array Default parameters
     */
    public function getDefaults(): array;

    /**
     * Validate widget parameters
     *
     * @param array $params Parameters to validate
     * @return array Validation errors (empty if valid)
     */
    public function validateParams(array $params): array;

    /**
     * Get the widget's event configuration
     *
     * @return array Event configuration
     */
    public function getEventConfig(): array;

    /**
     * Check if this widget uses events
     *
     * @return bool
     */
    public function usesEvents(): bool;
}
