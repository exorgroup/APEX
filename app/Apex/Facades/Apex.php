<?php

namespace App\Apex\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * APEX Facade
 * 
 * @method static string process(string $content)
 * @method static array getProcessedWidgets()
 * @method static void clearProcessedWidgets()
 * @method static \App\Apex\WidgetRegistry getRegistry()
 * @method static \App\Apex\Support\WidgetDependencyManager getDependencyManager()
 * @method static \App\Apex\Support\WidgetGroupManager getGroupManager()
 * @method static \App\Apex\Support\WidgetConnectionManager getConnectionManager()
 * 
 * @see \App\Apex\TemplateProcessor
 */
class Apex extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'apex';
    }
}
