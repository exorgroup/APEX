<?php
// app/Apex/Core/Widget/BaseWidget.php

namespace App\Apex\Core\Widget;

use App\Apex\Core\Contracts\WidgetInterface;
use Illuminate\Support\Str;

abstract class BaseWidget implements WidgetInterface
{
    protected array $config;
    protected string $id;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->id = $config['id'] ?? $this->generateId();
    }

    /**
     * Generate a unique widget ID
     */
    protected function generateId(): string
    {
        return $this->getType() . '_' . Str::random(8);
    }

    /**
     * Get the widget ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Base transformation - can be overridden by child classes
     */
    public function transform(array $config): array
    {
        return array_merge([
            'id' => $this->id,
            'type' => $this->getType(),
        ], $config);
    }
}
