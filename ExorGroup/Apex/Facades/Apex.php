<?php

namespace ExorGroup\Apex\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * APEX Facade
 * 
 * @method static string process(string $content)
 * @method static array getProcessedWidgets()
 * @method static void clearProcessedWidgets()
 * @method static \Exorgroup\Apex\WidgetRegistry getRegistry()
 * @method static \Exorgroup\Apex\Support\WidgetDependencyManager getDependencyManager()
 * @method static \Exorgroup\Apex\Support\WidgetGroupManager getGroupManager()
 * @method static \Exorgroup\Apex\Support\WidgetConnectionManager getConnectionManager()
 * 
 * @see \Exorgroup\Apex\TemplateProcessor
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
