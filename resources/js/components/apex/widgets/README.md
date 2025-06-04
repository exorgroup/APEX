# APEX Widget Development Guide

## Creating a New Widget

1. **Backend Widget Class**
   - Create a new class in `app/Apex/Widgets/` that extends `BaseWidget`
   - Implement required methods: `getType()`, `getSchema()`, `transform()`
   - Register in `ApexServiceProvider`

2. **Frontend Widget Component**
   - Create Vue component in `resources/js/components/apex/widgets/`
   - Accept `widgetId` and widget-specific props
   - Register in `WidgetRenderer.vue` component map

3. **Example Widget Structure**
   ```php
   class MyWidget extends BaseWidget
   {
       public function getType(): string
       {
           return 'my-widget';
       }
       
       public function getSchema(): array
       {
           return [
               // JSON schema definition
           ];
       }
       
       public function transform(array $config): array
       {
           // Transform and validate config
           return parent::transform($config);
       }
   }
   ```

   4. **Widget Registration**

   - In ApexServiceProvider
    ```
    $registry->register('my-widget', MyWidget::class);
    ```