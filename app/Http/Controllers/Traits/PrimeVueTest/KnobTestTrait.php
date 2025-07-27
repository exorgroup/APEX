<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait KnobTestTrait
{
    protected function getKnobWidgets(): array
    {
        return [
            [
                'type' => 'knob',
                'value' => 20,
                'min' => 0,
                'max' => 40,
                'step' => 1,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#ef4444',
                'rangeColor' => '#fecaca',
                'valueTemplate' => '{value}°C',
                'label' => 'Temperature',
                'readonly' => false
            ],
            [
                'type' => 'knob',
                'value' => 50,
                'min' => 0,
                'max' => 100,
                'step' => 10,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#3b82f6',
                'rangeColor' => '#dbeafe',
                'valueTemplate' => '{value}%',
                'label' => 'Volume',
                'readonly' => false
            ],
            [
                'type' => 'knob',
                'value' => 75,
                'min' => 0,
                'max' => 100,
                'size' => 150,
                'strokeWidth' => 14,
                'showValue' => true,
                'valueColor' => '#10b981',
                'rangeColor' => '#d1fae5',
                'valueTemplate' => '{value}%',
                'label' => 'Progress',
                'readonly' => true
            ]
        ];
    }
}
