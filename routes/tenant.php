<?php

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "tenant" middleware group. Now create something great!
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuditDebugController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| IMPORTANT: Wrap ALL routes in tenancy middleware
|--------------------------------------------------------------------------
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Your Application Routes
    |--------------------------------------------------------------------------
    */

    // Add your existing application routes here


    /*
    |--------------------------------------------------------------------------
    | APEX Audit Debug Routes
    |--------------------------------------------------------------------------
    */

    // Basic connection debug
    Route::get('/debug-connection', function () {
        return response()->json([
            'default_connection' => config('database.default'),
            'tenant_id' => function_exists('tenant') && tenant() ? tenant('id') : 'no tenant',
            'current_database' => DB::connection()->getDatabaseName(),
            'audit_connection_config' => config('apex.audit.audit.connection'),
            'tenancy_enabled' => config('apex.audit.tenancy.enabled'),
            'tables_in_db' => DB::select('SHOW TABLES'),
        ]);
    });

    /*
|--------------------------------------------------------------------------
| APEX Audit API Routes (for testing with PowerShell/Postman)
|--------------------------------------------------------------------------
*/

    Route::withoutMiddleware(['web'])->prefix('api/apex/audit')->group(function () {

        // Create test data
        Route::post('/test/create-test-data', function () {
            $auditService = app(\App\Apex\Audit\Services\AuditService::class);
            $results = [];
            $errors = [];

            // Check initial counts
            $initialAuditCount = DB::connection('tenant')->table('apex_audit')->count();
            $initialHistoryCount = DB::connection('tenant')->table('apex_history')->count();

            $testScenarios = [
                ['action' => 'create', 'model' => 'Car', 'data' => ['make' => 'Toyota', 'model' => 'Camry', 'year' => 2024]],
                ['action' => 'update', 'model' => 'Car', 'data' => ['price' => 25000, 'color' => 'Blue']],
                ['action' => 'delete', 'model' => 'Car', 'data' => ['id' => 1, 'reason' => 'Test deletion']],
            ];

            foreach ($testScenarios as $i => $scenario) {
                try {
                    $auditService->logCustomAction([
                        'event_type' => $scenario['action'] === 'custom' ? 'custom' : 'model_crud',
                        'action_type' => $scenario['action'],
                        'model_type' => "TestModel{$scenario['model']}",
                        'model_id' => (string) ($i + 1),
                        'table_name' => strtolower($scenario['model']) . 's',
                        'additional_data' => $scenario['data'],
                        'source_element' => 'api-test',
                    ]);
                    $results[] = "Created {$scenario['action']} audit for {$scenario['model']}";
                } catch (\Exception $e) {
                    $errors[] = [
                        'scenario' => $scenario['action'],
                        'error' => $e->getMessage(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ];
                }
            }

            // Get final counts
            $finalAuditCount = DB::connection('tenant')->table('apex_audit')->count();
            $finalHistoryCount = DB::connection('tenant')->table('apex_history')->count();

            return response()->json([
                'success' => empty($errors),
                'results' => $results,
                'errors' => $errors,
                'counts' => [
                    'initial_audit' => $initialAuditCount,
                    'final_audit' => $finalAuditCount,
                    'audit_created' => $finalAuditCount - $initialAuditCount,
                    'initial_history' => $initialHistoryCount,
                    'final_history' => $finalHistoryCount,
                    'history_created' => $finalHistoryCount - $initialHistoryCount,
                ],
                'config' => [
                    'audit_enabled' => config('apex.audit.audit.enabled'),
                    'history_enabled' => config('apex.audit.history.enabled'),
                    'signature_enabled' => config('apex.audit.audit.signature.enabled'),
                    'secret_key' => config('apex.audit.audit.signature.secret_key') ? 'SET' : 'NOT SET',
                ],
            ]);
        });

        // View history
        Route::get('/history/list', function () {
            $historyService = app(\App\Apex\Audit\Services\HistoryService::class);

            try {
                $history = $historyService->getRecentActivity(30, 20);
                $summary = $historyService->getHistorySummary();

                return response()->json([
                    'success' => true,
                    'data' => $history,
                    'summary' => $summary,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                ], 500);
            }
        });

        // Get statistics
        Route::get('/statistics', function () {
            $auditCount = DB::connection('tenant')->table('apex_audit')->count();
            $historyCount = DB::connection('tenant')->table('apex_history')->count();

            return response()->json([
                'audit_records' => $auditCount,
                'history_records' => $historyCount,
                'tenant' => tenant('id'),
                'database' => DB::connection()->getDatabaseName(),
                'config' => [
                    'audit_enabled' => config('apex.audit.audit.enabled'),
                    'history_enabled' => config('apex.audit.history.enabled'),
                ],
            ]);
        });

        // Debug what's in the tables
        Route::get('/debug/table-contents', function () {
            $audits = DB::connection('tenant')->table('apex_audit')
                ->select('id', 'event_type', 'action_type', 'model_type', 'created_at')
                ->get();

            $histories = DB::connection('tenant')->table('apex_history')
                ->select('id', 'audit_id', 'action_type', 'model_type', 'created_at')
                ->get();

            return response()->json([
                'audit_records' => $audits,
                'history_records' => $histories,
            ]);
        });

        // Add this route to test the AuditService internal methods directly

        Route::post('/test/audit-service-internal', function () {
            $results = [];

            try {
                // Get services
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);
                $signatureService = app(\App\Apex\Audit\Services\AuditSignatureService::class);

                // Prepare test data manually
                $testData = [
                    'audit_uuid' => \Illuminate\Support\Str::uuid()->toString(),
                    'event_type' => 'custom',
                    'action_type' => 'internal_test_' . time(),
                    'model_type' => 'TestModel',
                    'model_id' => '777',
                    'table_name' => 'tests',
                    'user_id' => null,
                    'session_id' => 'test_session',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Test Agent',
                    'created_at' => now()->toDateTimeString(),
                ];

                // Generate signature
                $testData['signature'] = $signatureService->generateSignature($testData);

                // Test direct DB insert
                $directInsertId = DB::connection('tenant')->table('apex_audit')->insertGetId($testData);
                $results['direct_insert'] = [
                    'success' => true,
                    'id' => $directInsertId,
                ];

                // Now test through the service's createAuditRecord method using reflection
                $reflection = new \ReflectionClass($auditService);
                $method = $reflection->getMethod('createAuditRecord');
                $method->setAccessible(true);

                $testData2 = $testData;
                $testData2['audit_uuid'] = \Illuminate\Support\Str::uuid()->toString();
                $testData2['action_type'] = 'service_method_test_' . time();
                $testData2['model_id'] = '666';

                $serviceInsertId = $method->invoke($auditService, $testData2);
                $results['service_method'] = [
                    'success' => true,
                    'id' => $serviceInsertId,
                ];

                // Check if queueing is enabled
                $results['queue_config'] = [
                    'enabled' => config('apex.audit.audit.queue.enabled'),
                    'connection' => config('apex.audit.audit.queue.connection'),
                    'queue_name' => config('apex.audit.audit.queue.queue'),
                ];

                // Final count
                $results['final_count'] = DB::connection('tenant')->table('apex_audit')->count();
            } catch (\Throwable $e) {
                $results['error'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'class' => get_class($e),
                ];
            }

            return response()->json($results);
        });

        Route::get('/test/check-logs', function () {
            $logFile = storage_path('logs/laravel.log');
            $results = [
                'log_file_exists' => file_exists($logFile),
                'apex_audit_errors' => [],
            ];

            if (file_exists($logFile)) {
                // Get last 50 lines that contain "APEX Audit"
                $lines = file($logFile);
                $apexLines = [];

                foreach ($lines as $line) {
                    if (stripos($line, 'APEX Audit') !== false) {
                        // Parse the log entry
                        if (preg_match('/\[([\d-\s:]+)\].*APEX Audit: (.+)/', $line, $matches)) {
                            $apexLines[] = [
                                'time' => $matches[1],
                                'message' => trim($matches[2]),
                            ];
                        }
                    }
                }

                // Get last 10 APEX Audit entries
                $results['apex_audit_errors'] = array_slice($apexLines, -10);
            }

            // Also test logCustomAction with detailed error capture
            try {
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);

                // Temporarily modify log level to capture everything
                config(['logging.channels.single.level' => 'debug']);

                $auditService->logCustomAction([
                    'event_type' => 'custom',
                    'action_type' => 'log_test_' . time(),
                    'model_type' => 'TestModel',
                    'model_id' => '555',
                    'table_name' => 'tests',
                    'additional_data' => ['test' => true],
                ]);

                $results['logCustomAction_test'] = 'completed';
            } catch (\Exception $e) {
                $results['logCustomAction_error'] = $e->getMessage();
            }

            return response()->json($results);
        });

        // Add this route to test the prepareCustomAuditData method directly

        Route::post('/test/prepare-audit-data', function () {
            $results = [];

            try {
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);

                // Test data
                $testData = [
                    'event_type' => 'custom',
                    'action_type' => 'prepare_test_' . time(),
                    'model_type' => 'TestModel',
                    'model_id' => '444',
                    'table_name' => 'tests',
                    'additional_data' => ['test' => true, 'timestamp' => now()],
                ];

                // Use reflection to call the protected method
                $reflection = new \ReflectionClass($auditService);
                $method = $reflection->getMethod('prepareCustomAuditData');
                $method->setAccessible(true);

                $preparedData = $method->invoke($auditService, $testData);
                $results['prepared_data'] = $preparedData;

                // Check what's in the request config
                $results['request_config'] = app('apex.audit.request.config', []);

                // Check session status
                $results['session'] = [
                    'exists' => session()->isStarted(),
                    'id' => session()->getId(),
                ];

                // Now try the full logCustomAction with the prepared data
                $createMethod = $reflection->getMethod('createAuditRecord');
                $createMethod->setAccessible(true);

                $auditId = $createMethod->invoke($auditService, $preparedData);
                $results['audit_created'] = [
                    'success' => true,
                    'id' => $auditId,
                ];
            } catch (\Throwable $e) {
                $results['error'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => array_slice($e->getTrace(), 0, 3),
                ];
            }

            // Final count
            $results['final_count'] = DB::connection('tenant')->table('apex_audit')->count();

            return response()->json($results);
        });

        // Add this fixed version that handles missing request config

        Route::post('/test/prepare-audit-data-fixed', function () {
            $results = [];

            try {
                // Set the request config manually since we're not using the middleware
                app()->instance('apex.audit.request.config', []);

                $auditService = app(\App\Apex\Audit\Services\AuditService::class);

                // Test data
                $testData = [
                    'event_type' => 'custom',
                    'action_type' => 'fixed_test_' . time(),
                    'model_type' => 'TestModel',
                    'model_id' => '333',
                    'table_name' => 'tests',
                    'additional_data' => ['test' => true, 'timestamp' => now()->toISOString()],
                ];

                // Use reflection to call the protected method
                $reflection = new \ReflectionClass($auditService);
                $method = $reflection->getMethod('prepareCustomAuditData');
                $method->setAccessible(true);

                $preparedData = $method->invoke($auditService, $testData);
                $results['prepared_data'] = $preparedData;

                // Now create the audit record
                $createMethod = $reflection->getMethod('createAuditRecord');
                $createMethod->setAccessible(true);

                $auditId = $createMethod->invoke($auditService, $preparedData);
                $results['audit_created'] = [
                    'success' => true,
                    'id' => $auditId,
                ];
            } catch (\Throwable $e) {
                $results['error'] = [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                ];
            }

            // Final count
            $results['final_count'] = DB::connection('tenant')->table('apex_audit')->count();

            return response()->json($results);
        });

        // Also fix the main test route
        Route::post('/test/create-test-data-fixed', function () {
            // Set the request config to avoid the error
            app()->instance('apex.audit.request.config', []);

            $auditService = app(\App\Apex\Audit\Services\AuditService::class);
            $results = [];
            $errors = [];

            $initialCount = DB::connection('tenant')->table('apex_audit')->count();

            $testScenarios = [
                ['action' => 'create', 'model' => 'Car', 'data' => ['make' => 'Toyota', 'model' => 'Camry', 'year' => 2024]],
                ['action' => 'update', 'model' => 'Car', 'data' => ['price' => 25000, 'color' => 'Blue']],
                ['action' => 'delete', 'model' => 'Car', 'data' => ['id' => 1, 'reason' => 'Test deletion']],
            ];

            foreach ($testScenarios as $i => $scenario) {
                try {
                    $auditService->logCustomAction([
                        'event_type' => $scenario['action'] === 'custom' ? 'custom' : 'model_crud',
                        'action_type' => $scenario['action'],
                        'model_type' => "TestModel{$scenario['model']}",
                        'model_id' => (string) ($i + 1),
                        'table_name' => strtolower($scenario['model']) . 's',
                        'additional_data' => $scenario['data'],
                        'source_element' => 'api-test-fixed',
                    ]);
                    $results[] = "Created {$scenario['action']} audit for {$scenario['model']}";
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                }
            }

            $finalCount = DB::connection('tenant')->table('apex_audit')->count();

            return response()->json([
                'success' => empty($errors),
                'results' => $results,
                'errors' => $errors,
                'created' => $finalCount - $initialCount,
                'final_count' => $finalCount,
            ]);
        });

        // Test with real Car model operations
        Route::post('/test/real-model-audit', function () {
            // Set the request config
            app()->instance('apex.audit.request.config', []);

            $results = [];
            $auditService = app(\App\Apex\Audit\Services\AuditService::class);

            try {
                // Initial counts
                $initialAudit = DB::connection('tenant')->table('apex_audit')->count();
                $initialHistory = DB::connection('tenant')->table('apex_history')->count();

                // Test 1: Create a real Car model
                $car = new \App\Models\Car();
                $car->make = 'Honda';
                $car->model = 'Civic';
                $car->year = 2024;
                $car->color = 'Red';
                $car->vin = 'VIN' . time();
                $car->price = 28000;
                $car->status = 'available';
                $car->save();

                $results['create'] = [
                    'success' => true,
                    'car_id' => $car->id,
                ];

                // Test 2: Update the car
                $car->price = 27500;
                $car->color = 'Blue';
                $car->save();

                $results['update'] = [
                    'success' => true,
                    'changes' => ['price' => 27500, 'color' => 'Blue'],
                ];

                // Test 3: Delete the car
                $car->delete();

                $results['delete'] = [
                    'success' => true,
                    'deleted_id' => $car->id,
                ];

                // Final counts
                $finalAudit = DB::connection('tenant')->table('apex_audit')->count();
                $finalHistory = DB::connection('tenant')->table('apex_history')->count();

                $results['counts'] = [
                    'audit_created' => $finalAudit - $initialAudit,
                    'history_created' => $finalHistory - $initialHistory,
                    'final_audit' => $finalAudit,
                    'final_history' => $finalHistory,
                ];

                // Check if observer is attached
                $results['observer_check'] = [
                    'has_auditable_trait' => in_array(\App\Apex\Audit\Traits\ApexAuditable::class, class_uses(\App\Models\Car::class)),
                    'observer_registered' => class_exists(\App\Apex\Audit\Observers\ApexAuditObserver::class),
                ];
            } catch (\Exception $e) {
                $results['error'] = [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                ];
            }

            return response()->json($results);
        });

        // Test why history isn't created for custom actions
        Route::post('/test/custom-with-history', function () {
            app()->instance('apex.audit.request.config', []);

            $auditService = app(\App\Apex\Audit\Services\AuditService::class);

            // Check the shouldCreateHistory logic
            $reflection = new \ReflectionClass($auditService);
            $method = $reflection->getMethod('shouldCreateHistory');
            $method->setAccessible(true);

            $testData = [
                'action_type' => 'create',
                'model' => null, // No actual model for custom actions
            ];

            $shouldCreate = $method->invoke($auditService, $testData);

            return response()->json([
                'should_create_history' => $shouldCreate,
                'reason' => 'Custom actions without real models do not create history records',
                'history_enabled' => config('apex.audit.history.enabled'),
            ]);
        });

        // Test AuditService directly
        Route::post('/test/audit-service-debug', function () {
            $results = [];

            // Test 1: Check if AuditService exists and configuration
            try {
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);
                $results['service_exists'] = true;
                $results['service_class'] = get_class($auditService);
            } catch (\Exception $e) {
                $results['service_error'] = $e->getMessage();
            }

            // Test 2: Try direct logCustomAction with full error capture
            try {
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);

                // Check if logCustomAction method exists
                if (!method_exists($auditService, 'logCustomAction')) {
                    throw new \Exception('logCustomAction method does not exist on AuditService');
                }

                // Call the method
                $result = $auditService->logCustomAction([
                    'event_type' => 'custom',
                    'action_type' => 'debug_test_' . time(),
                    'model_type' => 'TestModel',
                    'model_id' => '999',
                    'table_name' => 'tests',
                    'additional_data' => ['test' => true, 'timestamp' => now()->toISOString()],
                ]);

                $results['logCustomAction'] = [
                    'success' => true,
                    'result' => $result,
                ];
            } catch (\Throwable $e) {
                $results['logCustomAction'] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => array_slice($e->getTrace(), 0, 5), // First 5 trace items
                ];
            }

            // Test 3: Check database after attempt
            $results['database_check'] = [
                'audit_count' => DB::connection('tenant')->table('apex_audit')->count(),
                'history_count' => DB::connection('tenant')->table('apex_history')->count(),
                'last_audit' => DB::connection('tenant')->table('apex_audit')
                    ->orderBy('id', 'desc')
                    ->first(),
            ];

            // Test 4: Check if we can access AuditService methods
            if (isset($auditService)) {
                $results['service_methods'] = get_class_methods($auditService);
            }

            return response()->json($results);
        });
    });

    // Audit specific debug
    Route::get('/debug-audit', function () {
        $auditModel = new \App\Apex\Audit\Models\ApexAudit();
        $historyModel = new \App\Apex\Audit\Models\ApexHistory();

        return response()->json([
            'audit_connection' => $auditModel->getConnectionName(),
            'history_connection' => $historyModel->getConnectionName(),
            'audit_table' => $auditModel->getTable(),
            'history_table' => $historyModel->getTable(),
            'current_db_connection' => config('database.default'),
            'tenant_info' => [
                'id' => tenant('id'),
                'db_name' => tenant('tenancy_db_name'),
            ],
            'audit_count' => DB::connection($auditModel->getConnectionName())->table('apex_audit')->count(),
            'history_count' => DB::connection($historyModel->getConnectionName())->table('apex_history')->count(),
        ]);
    });

    // Test audit creation using controller
    Route::post('/debug-audit-create', [AuditDebugController::class, 'testCreate']);

    // API route that bypasses session middleware completely
    Route::withoutMiddleware(['web'])->group(function () {
        Route::post('/api/test-audit', function () {
            try {
                // Direct database insertion to bypass all Laravel logic
                $id = DB::connection('tenant')->table('apex_audit')->insertGetId([
                    'audit_uuid' => (string) \Illuminate\Support\Str::uuid(),
                    'event_type' => 'custom',
                    'action_type' => 'direct_db_test',
                    'model_type' => 'TestModel',
                    'model_id' => '1',
                    'table_name' => 'tests',
                    'signature' => 'test',
                    'created_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'method' => 'direct_db_insert',
                    'id' => $id,
                    'count' => DB::connection('tenant')->table('apex_audit')->count(),
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                ], 500);
            }
        });

        Route::post('/api/test-model', function () {
            try {
                $audit = new \App\Apex\Audit\Models\ApexAudit();
                $audit->audit_uuid = \Illuminate\Support\Str::uuid();
                $audit->event_type = 'custom';
                $audit->action_type = 'model_test';
                $audit->model_type = 'TestModel';
                $audit->model_id = '2';
                $audit->table_name = 'tests';
                $audit->signature = 'test';
                $audit->created_at = now();

                $saved = $audit->save();

                return response()->json([
                    'success' => $saved,
                    'method' => 'eloquent_model',
                    'id' => $audit->id,
                    'connection' => $audit->getConnectionName(),
                    'count' => DB::connection('tenant')->table('apex_audit')->count(),
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 500);
            }
        });
    });

    // Simple test without CSRF
    Route::post('/test-audit-simple', function () {
        try {
            $audit = new \App\Apex\Audit\Models\ApexAudit();
            $audit->audit_uuid = \Illuminate\Support\Str::uuid();
            $audit->event_type = 'custom';
            $audit->action_type = 'simple_test';
            $audit->model_type = 'TestModel';
            $audit->model_id = '1';
            $audit->table_name = 'tests';
            $audit->signature = 'test';
            $audit->created_at = now();

            $saved = $audit->save();

            return response()->json([
                'success' => $saved,
                'id' => $audit->id,
                'connection' => $audit->getConnectionName(),
                'count' => DB::connection('tenant')->table('apex_audit')->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    })->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});

/*
|--------------------------------------------------------------------------
| Quick Testing URLs
|--------------------------------------------------------------------------
|
| Test the audit system:
|
| GET  /debug-connection     - Check tenant connection
| GET  /debug-audit         - Check audit tables
| POST /debug-audit-create  - Test audit creation (with controller)
| POST /test-audit-simple   - Simple audit test (no CSRF)
|
*/