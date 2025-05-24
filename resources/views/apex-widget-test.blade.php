<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'APEX Widget Test' }}</title>
    
    <!-- Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- APEX Event Bus -->
    <script>
        // Include the APEX Event Bus inline for testing
        window.ApexEvents = {
            publish: function(event, data) {
                console.log('APEX Event Published:', event, data);
            },
            subscribe: function(event, callback) {
                console.log('APEX Event Subscribed:', event);
                return function() {}; // unsubscribe function
            }
        };
    </script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-center mb-8 text-gray-800">{{ $pageTitle ?? 'APEX Widget Test Page' }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                !!apex-infoTub:{"id":"image-left", "image":"https://cdn-icons-png.flaticon.com/512/174/174857.png", "text":"9.3k", "caption":"Image Left", "imageAlign":"left", "textAlign":"left", "captionAlign":"left", "borderColor":"#0066cc", "borderWidth":"2px"}!!
            </div>
            
            <div>
                !!apex-infoTub:{"id":"image-center", "image":"https://cdn-icons-png.flaticon.com/512/1077/1077114.png", "text":"15.2k", "caption":"Image Center", "imageAlign":"center", "textAlign":"center", "captionAlign":"center", "borderColor":"#10b981", "borderWidth":"2px"}!!
            </div>
        
            <div>
                !!apex-infoTub:{"id":"image-right", "image":"https://cdn-icons-png.flaticon.com/512/1006/1006771.png", "text":"342", "caption":"Image Right", "imageAlign":"right", "textAlign":"right", "captionAlign":"right", "borderColor":"#f59e0b", "borderWidth":"2px"}!!
            </div>
        </div>
        
        {{-- Mixed Alignment Test --}}
        <h3 class="text-xl font-bold mb-4">Mixed Alignment Test</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                !!apex-infoTub:{"id":"mixed-1", "image":"https://cdn-icons-png.flaticon.com/512/2111/2111463.png", "text":"750", "caption":"Mixed Styles", "imageAlign":"left", "textAlign":"center", "captionAlign":"right", "borderColor":"#8b5cf6", "borderWidth":"2px", "size":"50px"}!!
            </div>
            
            <div>
                !!apex-infoTub:{"id":"mixed-2", "image":"https://cdn-icons-png.flaticon.com/512/3135/3135715.png", "text":"1.2M", "caption":"Another Mix", "imageAlign":"right", "textAlign":"left", "captionAlign":"center", "borderColor":"#ef4444", "borderWidth":"2px", "size":"45px"}!!
            </div>
        </div>
        
        {{-- Bottom Position Test --}}
        <h3 class="text-xl font-bold mb-4">Bottom Position Image Test</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                !!apex-infoTub:{"id":"bottom-left", "image":"https://cdn-icons-png.flaticon.com/512/733/733547.png", "text":"567", "caption":"Bottom Left", "position":"bottom", "imageAlign":"left", "textAlign":"left", "captionAlign":"left", "borderColor":"#06b6d4", "borderWidth":"1px"}!!
            </div>
            
            <div>
                !!apex-infoTub:{"id":"bottom-center", "image":"https://cdn-icons-png.flaticon.com/512/733/733558.png", "text":"890", "caption":"Bottom Center", "position":"bottom", "imageAlign":"center", "textAlign":"center", "captionAlign":"center", "borderColor":"#84cc16", "borderWidth":"1px"}!!
            </div>
        
            <div>
                !!apex-infoTub:{"id":"bottom-right", "image":"https://cdn-icons-png.flaticon.com/512/733/733579.png", "text":"1.5k", "caption":"Bottom Right", "position":"bottom", "imageAlign":"right", "textAlign":"right", "captionAlign":"right", "borderColor":"#f97316", "borderWidth":"1px"}!!
            </div>
        </div>
        
        <!-- Test 1: Direct Template String -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Test 1: Direct Template String !!</h2>
            <div class="bg-white p-6 rounded shadow-md">
                <p class="mb-4">This is a test of the APEX template processing system.</p>
                !!apex-testWidget:{"id":"test-widget-1", "title":"Test Widget 1", "content":"This is a direct template test. ss", "showDebug":true}!!
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Test 1: Direct Template String JSON apexWidget</h2>
            <div class="bg-white p-6 rounded shadow-md">
                <p class="mb-4">This is a test of the APEX template processing system.</p>
                @php
                    $params = [
                        "id" => "test-widget-1-at",
                        "title" => "Test Widget 1 (from @)",
                        "content" => "This is a direct template test from the @ directive.",
                        "showDebug" => true
                    ];
                @endphp
                {{-- CRITICAL CHANGE HERE: Ensure the second argument is already a JSON string --}}
                @apexWidget('testWidget', '{!! json_encode($params) !!}')
                {{-- OR (cleaner, but relies on Blade's default escaping): --}}
                {{-- @apexWidget('testWidget', "{{ json_encode($params) }}") --}}
            </div>
        </div>

        <!-- Test 2: Another Direct Template String -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibont mb-4">Test 2: Another Direct Template</h2>
            <div class="bg-white p-6 rounded shadow-md">
                <p class="mb-4">This is another direct template test.</p>
                !!apex-testWidget:{"id":"test-widget-2", "title":"Test Widget 2", "content":"This is another direct template test.", "cssClass":"border-2 border-blue-500"}!!
            </div>
        </div>

        <!-- Test 3: PHP Variable Template -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Test 3: PHP Variable Template</h2>
            @php
                $template = '<div class="bg-white p-6 rounded shadow-md">
                    <p class="mb-4">This is another test using a PHP variable.</p>
                    !!apex-testWidget:{"id":"test-widget-3", "title":"Test Widget 3", "content":"This is a variable template test.", "showDebug":true}!!
                </div>';
                
                echo $template;
            @endphp
        </div>

        <!-- Test 4: Content in View with Dynamic Data -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Test 4: Content in View with Controller Data</h2>
            <div class="bg-white p-6 rounded shadow-md">
                <p class="mb-4">This test uses data passed from the controller:</p>
                !!apex-testWidget:{"id":"test-widget-4", "title":"{{ $testData['dynamic_title'] ?? 'Fallback Title' }}", "content":"{{ $testData['dynamic_content'] ?? 'Fallback content' }}"}!!
            </div>
        </div>

        <!-- Test 5: Widget with Special Characters -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Test 5: Special Characters</h2>
            <div class="bg-white p-6 rounded shadow-md">
                <p class="mb-4">Testing with special characters and HTML entities:</p>
                !!apex-testWidget:{"id":"test-widget-5", "title":"Special & Characters", "content":"Content with <strong>HTML</strong> tags, quotes \" and symbols &amp; entities"}!!
            </div>
        </div>

        <!-- Test 6: Multiple Widgets for ID Uniqueness -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Test 6: Multiple Widgets</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded shadow-md">
                    !!apex-testWidget:{"id":"test-widget-6a", "title":"Widget A", "content":"First widget in a pair"}!!
                </div>
                <div class="bg-white p-6 rounded shadow-md">
                    !!apex-testWidget:{"id":"test-widget-6b", "title":"Widget B", "content":"Second widget in a pair"}!!
                </div>
            </div>
        </div>

        <!-- Test 7: Widget without ID (should auto-generate) -->
        <div class="mb-6">
            <h2 class="text-2xl font-semibold mb-4">Test 7: Auto-Generated ID</h2>
            <div class="bg-white p-6 rounded shadow-md">
                <p class="mb-4">This widget has no explicit ID (should auto-generate):</p>
                !!apex-testWidget:{"title":"Auto-ID Widget", "content":"This widget should get an auto-generated ID", "showDebug":true}!!
            </div>
        </div>

        <!-- Debug Information -->
        <div class="mt-8 p-4 bg-gray-800 text-white rounded">
            <h3 class="text-lg font-semibold mb-2">Debug Information</h3>
            <p><strong>Environment:</strong> {{ app()->environment() }}</p>
            <p><strong>Debug Mode:</strong> {{ config('app.debug') ? 'Enabled' : 'Disabled' }}</p>
            <p><strong>APEX Processing:</strong> Check browser developer tools and Laravel logs for processing details</p>
        </div>
    </div>

    <!-- JavaScript for testing -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('APEX Widget Test Page Loaded');
            
            // Count rendered widgets
            const widgets = document.querySelectorAll('.apex-test-widget');
            console.log('Total APEX test widgets found:', widgets.length);
            
            // Log widget IDs
            widgets.forEach((widget, index) => {
                console.log(`Widget ${index + 1} ID:`, widget.id);
            });
        });
    </script>
</body>
</html>