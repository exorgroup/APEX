<?php

namespace App\Http\Controllers\Traits\PrimeVueTest;

trait DatePickerTestTrait
{
    protected function getDatePickerWidgets(): array
    {
        return [
            [
                'type' => 'datepicker',
                'value' => null,
                'placeholder' => 'Select a date',
                'dateFormat' => 'dd/mm/yy',
                'showIcon' => true,
                'inline' => false,
                'selectionMode' => 'single',
                'label' => 'Event Date'
            ],
            [
                'type' => 'datepicker',
                'value' => null,
                'placeholder' => 'Select date range',
                'dateFormat' => 'dd/mm/yy',
                'showIcon' => true,
                'selectionMode' => 'range',
                'label' => 'Date Range'
            ]
        ];
    }
}
