<?php

namespace ExorGroup\ApexPro\Widgets;

use ExorGroup\Apex\Widgets\BaseWidget;

abstract class BaseProWidget extends BaseWidget
{
    /**
     * Render the widget with license checking
     */
    public function render(array $params = []): string
    {
        // Check license before any rendering
        if (!$this->isLicenseValid()) {
            return $this->renderLicenseError();
        }

        return parent::render($params);
    }

    /**
     * Check if the license is valid
     */
    protected function isLicenseValid(): bool
    {
        try {
            return app('apex.pro.license')->validate();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Render license error message
     */
    protected function renderLicenseError(): string
    {
        if (config('app.debug', false)) {
            return '<div class="apex-pro-license-error" style="
                background: #fee2e2; 
                color: #dc2626; 
                padding: 1rem; 
                border-radius: 0.5rem; 
                border: 1px solid #fecaca;
                font-family: monospace;
                font-size: 0.875rem;
                margin: 0.5rem 0;
            ">
                <strong>APEX Pro License Error:</strong> ' . class_basename(static::class) . ' requires a valid APEX Pro license.
                <br><small>This message only appears in debug mode.</small>
            </div>';
        }

        // In production, return empty or a generic placeholder
        return '<div class="apex-pro-placeholder" style="
            background: #f3f4f6; 
            border: 2px dashed #d1d5db; 
            border-radius: 0.5rem; 
            padding: 2rem; 
            text-align: center; 
            color: #6b7280;
            font-size: 0.875rem;
        ">
            Content not available
        </div>';
    }
}
