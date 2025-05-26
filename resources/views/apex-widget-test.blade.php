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

        Basic Breadcrumb
        !!apex-breadcrumb:{
            "items":[
                {"label":"Home", "url":"/"},
                {"label":"Products", "url":"/products"},
                {"label":"Electronics", "url":"/products/electronics"},
                {"label":"Smartphones"}
            ],
            "delimiter": " > ",
            "style": 1
        }!!

        No Shadow and No Border 
        !!apex-breadcrumb:{
            "items":[
                {"label":"Dashboard", "url":"/dashboard"},
                {"label":"Users", "url":"/users"},
                {"label":"Profile Settings"}
            ],
            "style": 2,
            "showBorder": false,
            "showShadow": false,
            "backgroundColor": "transparent",
            "gap": "12px"
        }!!

        Arrow Style Navigation
        !!apex-breadcrumb:{
            "items":[
                {"label":"Cart", "url":"/cart"},
                {"label":"Billing", "url":"/checkout/billing"},
                {"label":"Shipping", "url":"/checkout/shipping"},
                {"label":"Payment"}
            ],
            "style": 3,
            "showShadow": true,
            "borderRadius": "8px"
        }!!

        Fully Customized Design
        !!apex-breadcrumb:{
            "items":[
                {"label":"MONSTER HUNTER", "url":"/games/monster-hunter"},
                {"label":"FINAL FANTASY", "url":"/games/final-fantasy"},
                {"label":"DOOM", "url":"/games/doom"},
                {"label":"ZOMBIE HUNTER", "url":"/games/zombie-hunter"},
                {"label":"WARCRAFT"}
            ],
            "style": 5,
            "showBorder": true,
            "showShadow": true,
            "borderWidth": "2px",
            "borderColor": "#6366f1",
            "backgroundColor": "#f8fafc",
            "textColor": "#1e293b",
            "linkColor": "#3730a3",
            "hoverColor": "#1e1b4b",
            "activeColor": "#6366f1",
            "fontSize": "13px",
            "fontWeight": "600",
            "padding": "6px",
            "borderRadius": "30px",
            "gap": "4px",
            "height": "50px",
            "cssClass": "custom-game-breadcrumb"
        }!!


        title 
        !!apex-pageTitle:{
            "title":"Overview",
            "breadcrumbs":[
                {"label":"Home", "url":"/"},
                {"label":"User Profile"}
            ],
            "breadcrumbClass":"text-purple-600 font-bold"
        }!!


        !!apex-pageTitle:{
            "title":"Overview",
            "breadcrumbs":[
                {"label":"Home", "url":"/"},
                {"label":"User Profile"}
            ],
            "delimiter":"|",
            "shadow":true,
            "border":true,
            "titleClass":"text-blue-900",
            "breadcrumbClass":"text-gray-600"            
        }!!

        !!apex-pageTitle:{
            "title":"User Management",
            "breadcrumbs":[
                {"label":"Admin", "url":"/admin"},
                {"label":"Users", "url":"/admin/users"},
                {"label":"Edit User"}
            ],
            "delimiter":"/",
            "breadcrumbClass":"text-gray-500 text-sm uppercase tracking-wide"
        }!!

        !!apex-pageTitle:{
            "title":"API Documentation",
            "breadcrumbs":[
                {"label":"Docs", "url":"/docs"},
                {"label":"API", "url":"/docs/api"},
                {"label":"Authentication"}
            ],
            "delimiter":"❯",
            "titleClass":"text-indigo-900 font-black text-4xl",
            "breadcrumbClass":"text-indigo-600 font-mono text-sm bg-indigo-50 px-2 py-1 rounded",
            "backgroundColor":"#ffffff",
            "shadow":true,
            "borderRadius":"12px"
        }!!

        !!apex-pageTitle:{
            "title":"Shopping Cart",
            "breadcrumbs":[
                {"label":"🏠 Home", "url":"/"},
                {"label":"🛍️ Shop", "url":"/shop"},
                {"label":"🛒 Cart"}
            ],
            "delimiter":"▶",
            "titleClass":"text-green-800 font-extrabold text-2xl",
            "breadcrumbClass":"text-green-600 font-medium bg-green-100 px-3 py-1 rounded-full",
            "backgroundColor":"#f0fdf4",
            "border":true,
            "borderColor":"#16a34a",
            "borderWidth":"2px"
        }!!

        !!apex-pageTitle:{
            "title":"Settings",
            "breadcrumbs":[
                {"label":"Home", "url":"/"},
                {"label":"Account", "url":"/account"},
                {"label":"Privacy"}
            ],
            "delimiter":"|",
            "titleClass":"text-slate-800 font-light text-3xl",
            "breadcrumbClass":"bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-semibold hover:bg-gray-300"
            "backgroundColor":"transparent",
            "shadow":false,
            "border":false
        }!!

Status BAr
!!apex-statusBar:{
    "title": "Using Storage 6854.45 MB of 8 GB",
    "height": "8px",
    "gap": "2px",
    "unit": "MB",
    "showLegend": true,
    "legendPosition": "bottom",
    "shadow": true,
    "borderWidth": "1px",
    "borderColor": "#e5e7eb",
    "values": [
        {
            "label": "Regular",
            "value": 915,
            "color": "#f59e0b"
        },
        {
            "label": "System", 
            "value": 415,
            "color": "#3b82f6"
        },
        {
            "label": "Shared",
            "value": 201,
            "color": "#10b981"
        },
        {
            "label": "Free",
            "value": 612,
            "color": "#e5e7eb"
        }
    ]
}!!

Without Legend:

!!apex-statusBar:{
    "title": "Memory Usage",
    "height": "10px",
    "gap": "1px",
    "unit": "GB",
    "showLegend": false,
    "shadow": true,
    "values": [
        {
            "label": "Used",
            "value": 5.2,
            "color": "#ef4444"
        },
        {
            "label": "Available",
            "value": 10.8,
            "color": "#22c55e"
        }
    ]
}!!

With Custom Border and No Shadow:

!!apex-statusBar:{
    "title": "Project Progress",
    "height": "12px",
    "gap": "0px",
    "unit": "%",
    "showLegend": true,
    "legendPosition": "right",
    "shadow": false,
    "borderWidth": "2px",
    "borderColor": "#3b82f6",
    "values": [
        {
            "label": "Completed",
            "value": 75,
            "color": "#059669"
        },
        {
            "label": "Remaining",
            "value": 25,
            "color": "#e5e7eb"
        }
    ]
}!!

        Advanced Example with Custom Styling:

        !!apex-statusBar:{
            "title": "Project Progress Overview",
            "titleClass": "text-lg font-semibold text-gray-800",
            "height": "12px",
            "gap": "1px",
            "borderRadius": "6px",
            "showLegend": true,
            "legendPosition": "bottom",
            "unit": "%",
            "values": [
                {
                    "label": "Completed",
                    "value": 65,
                    "color": "#059669",
                    "textClass": "font-medium text-green-700"
                },
                {
                    "label": "In Progress",
                    "value": 25,
                    "color": "#dc2626",
                    "textClass": "font-medium text-red-700"
                },
                {
                    "label": "Remaining",
                    "value": 10,
                    "color": "#e5e7eb",
                    "textClass": "text-gray-500"
                }
            ]
        }!!

        Memory Usage Example:

        !!apex-statusBar:{
            "title": "Memory Usage",
            "height": "6px",
            "gap": "0px",
            "unit": "GB",
            "legendPosition": "right",
            "values": [
                {
                    "label": "Used",
                    "value": 5.2,
                    "color": "#ef4444"
                },
                {
                    "label": "Cached",
                    "value": 1.8,
                    "color": "#f97316"
                },
                {
                    "label": "Available",
                    "value": 9.0,
                    "color": "#22c55e"
                }
            ]
        }!!






        !!apex-progressTub:{"text":"78%", "caption":"ACTIVE USERS", "gaugeValue":78}!!

        !!apex-progressTub:{
            "text":"25,782",
            "caption":"ACTIVE USERS",
            "gaugeValue":78,
            "gaugeType":"radial",
            "gaugeColor":"#f59e0b",
            "gaugePosition":"bottom",
            "showPercentage":true,
            "badges":[
                {
                    "value":"-1%",
                    "class":"badge-negative"
                },
                {
                    "image":"data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iMTIiIHZpZXdCb3g9IjAgMCAxMiAxMiIgZmlsbD0ibm9uZSI+PHBhdGggZD0iTTYgMkw5IDhIM1oiIGZpbGw9IiNkYzI2MjYiLz48L3N2Zz4=",
                    "alt":"Down arrow"
                }
            ],
            "gaugeLabel":"Performance",
            "gaugeLabelPosition":"below"
        }!!

        !!apex-progressTub:{
            "text":"25,782",
            "caption":"top ACTIVE USERS",
            "gaugeValue":78,
            "gaugeType":"radial",
            "gaugeColor":"#f59e0b",
            "gaugePosition":"top",
            "showPercentage":true,
            "badges":[
                {
                    "value":"-1%",
                    "class":"badge-negative"
                },
                {
                    "image":"data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iMTIiIHZpZXdCb3g9IjAgMCAxMiAxMiIgZmlsbD0ibm9uZSI+PHBhdGggZD0iTTYgMkw5IDhIM1oiIGZpbGw9IiNkYzI2MjYiLz48L3N2Zz4=",
                    "alt":"Down arrow"
                }
            ],
            "gaugeLabel":"Performance",
            "gaugeLabelPosition":"below"
        }!!

        Radial Gauge 
!!apex-progressTub:{"gaugeType":"radial", "gaugeValue":75}!!

Full Circle Gauge 
!!apex-progressTub:{"gaugeType":"round", "gaugeValue":80}!!

Linear Progress Bar 
!!apex-progressTub:{"textAlign":"left","text":"25,782","gaugeType":"linear", "gaugeValue":65}!!

<!-- Large radial gauge -->
!!apex-progressTub:{
    "text":"25,782",
    "caption":"ACTIVE USERS", 
    "gaugeValue":78,
    "gaugeWidth":"180px",
    "gaugeType":"radial"
}!!

<!-- Linear gauge with custom width -->
!!apex-progressTub:{
    "text":"Progress",
    "gaugeType":"linear",
    "gaugeWidth":"250px",
    "gaugeHeight":"12px",
    "gaugeValue":65
}!!

        Basic vertical layout
        !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "Sunset Flight", "titleUrl": "/flights/premium", "line1": "3 hours to destination", "line2": "$299.99", "line3": "Premium class available"}!!

        Larger vertical card
        !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "Evening Adventure", "titleUrl": "/travel/packages", "line1": "Departure: 6:30 PM", "line2": "$450.00", "line3": "Window seat included", "width": "350px", "imageSize": "250px", "borderRadius": "16px"}!!

        Custom styling
        !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "Cloud Nine Experience", "titleUrl": "#", "line1": "Next departure in 2 hours", "line2": "$599.99", "line1Class": "flight-info", "line2Class": "flight-price premium", "backgroundColor": "#f0f8ff", "shadow": true}!!

        Horizontal Layout Examples
        Basic horizontal layout
        !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "Sky High Journey", "titleUrl": "/book-flight", "line1": "Duration: 4h 30m", "line2": "$199.50", "line3": "Direct flight", "layout": "horizontal", "width": "450px", "height": "160px"}!!

        Horizontal with custom image size
        !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "First Class Flight", "titleUrl": "/flights/first-class", "line1": "Luxury seating", "line2": "$1,299.00", "line3": "Gourmet meals included", "layout": "horizontal", "width": "500px", "imageWidth": "180px", "imageHeight": "140px", "backgroundColor": "#fff8dc"}!!

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "Economy Class", "titleUrl": "/flights/economy", "line1": "Standard seating", "line2": "$189.99", "line3": "Meals available", "width": "280px"}!!
            
            !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "Business Class", "titleUrl": "/flights/business", "line1": "Extra legroom", "line2": "$599.99", "line3": "Priority boarding", "width": "280px", "line2Class": "text-blue-600 font-bold"}!!
            
            !!apex-imageTub:{"image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=", "title": "First Class", "titleUrl": "/flights/first", "line1": "Luxury experience", "line2": "$1,499.99", "line3": "5-star service", "width": "280px", "backgroundColor": "#f9f9f9", "line2Class": "text-green-600 font-bold text-lg"}!!
        </div>

        Multiline
        !!apex-imageTub:{
            "image": "https://media.istockphoto.com/id/155439315/photo/passenger-airplane-flying-above-clouds-during-sunset.jpg?s=612x612&w=0&k=20&c=LJWadbs3B-jSGJBVy9s0f8gZMHi2NvWFXa3VJ2lFcL0=",
            "title": "Beautiful Sunset Flight",
            "titleUrl": "/flights/premium",
            "line1": "3 hours to destination",
            "line2": "$299.99",
            "line3": "Premium class available",
            "width": "350px",
            "imageSize": "250px"
        }!!

        <div class="skill-indicators">
            <div class="skill-item">
                <span>PHP:</span>
                !!apex-rating:{"total":5, "filled":4.5, "type":"square", "filledColor":"#8b5cf6"}!!
            </div>
            <div class="skill-item">
                <span>JavaScript:</span>
                !!apex-rating:{"total":5, "filled":3.8, "type":"square", "filledColor":"#f59e0b"}!!
            </div>
        </div>

        <div class="quality-score">
            <h4>Code Quality</h4>
            !!apex-rating:{"id":"hello","total":5, "filled":4.6, "type":"pill", "height":11, "filledColor":"#06b6d4", "orientation":"vertical"}!!
        </div>

        <div class="quality-score2">
            <h4>Code Quality</h4>
            !!apex-rating:{"id":"hello2","total":5, "filled":4.6, "type":"pill", "height":48, "filledColor":"#06b6d4"}!!
        </div>

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