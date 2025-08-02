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
use Illuminate\Support\Facades\Log;
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
            try {
                // Direct database query to avoid service issues
                $history = DB::connection('tenant')->table('apex_history')
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();

                $summary = [
                    'total_records' => DB::connection('tenant')->table('apex_history')->count(),
                    'by_action' => DB::connection('tenant')->table('apex_history')
                        ->groupBy('action_type')
                        ->selectRaw('action_type, count(*) as count')
                        ->pluck('count', 'action_type'),
                ];

                return response()->json([
                    'success' => true,
                    'data' => $history,
                    'summary' => $summary,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'trace' => array_slice($e->getTrace(), 0, 5),
                ], 500);
            }
        });

        // Language routes
        Route::get('/language/current', function () {
            $langService = app(\App\Apex\Audit\Services\ApexAuditLanguageService::class);

            return response()->json([
                'current_language' => $langService->getCurrentLanguage(),
                'supported_languages' => $langService->getSupportedLanguages(),
                'detection_method' => config('apex.audit.language.detection_method'),
            ]);
        });

        Route::post('/language/set/{language}', function ($language) {
            $langService = app(\App\Apex\Audit\Services\ApexAuditLanguageService::class);

            if (!$langService->isLanguageSupported($language)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Language not supported',
                    'supported' => array_keys($langService->getSupportedLanguages()),
                ], 400);
            }

            $langService->setLanguage($language);

            return response()->json([
                'success' => true,
                'language' => $language,
                'message' => 'Language changed successfully',
            ]);
        });

        // Rollback route
        Route::post('/history/rollback/{id}', function ($id) {
            try {
                // Set request config for services
                app()->instance('apex.audit.request.config', []);

                // Get the history record first
                $history = DB::connection('tenant')->table('apex_history')->find($id);
                if (!$history) {
                    return response()->json(['error' => 'History record not found'], 404);
                }

                // Check if it can be rolled back
                if (!$history->can_rollback) {
                    return response()->json(['error' => 'This action cannot be rolled back'], 400);
                }

                if ($history->rolled_back_at) {
                    return response()->json(['error' => 'This action has already been rolled back'], 400);
                }

                $rollbackService = app(\App\Apex\Audit\Services\RollbackService::class);
                $result = $rollbackService->rollback($id);

                return response()->json([
                    'success' => true,
                    'message' => 'Rollback completed successfully',
                    'result' => $result,
                ]);
            } catch (\App\Apex\Audit\Exceptions\RollbackException $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getUserMessage(),
                    'code' => $e->getCode(),
                    'details' => $e->toArray(),
                ], 400);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                ], 500);
            }
        });


        Route::get('/debug/history/{id}', function ($id) {
            $history = DB::connection('tenant')->table('apex_history')->find($id);
            if (!$history) {
                return response()->json(['error' => 'History record not found'], 404);
            }

            $rollbackData = $history->rollback_data ? json_decode($history->rollback_data, true) : null;

            return response()->json([
                'history_record' => $history,
                'rollback_data_parsed' => $rollbackData,
                'validation_checks' => [
                    'has_rollback_data' => !is_null($rollbackData),
                    'rollback_data_has_action' => isset($rollbackData['action']),
                    'rollback_data_has_values' => isset($rollbackData['values']),
                    'model_type_valid' => !empty($history->model_type) && $history->model_type !== '1',
                    'can_rollback' => (bool) $history->can_rollback,
                ]
            ]);
        });

        // Create test rollback record
        Route::post('/debug/create-rollback-test', function () {
            $historyId = DB::connection('tenant')->table('apex_history')->insertGetId([
                'model_type' => 'App\\Models\\Car',
                'model_id' => '999',
                'action_type' => 'update',
                'description' => 'Test rollback record',
                'rollback_data' => json_encode([
                    'action' => 'restore_values',
                    'values' => ['make' => 'Toyota', 'model' => 'Camry'],
                    'changed_fields' => ['make', 'model']
                ]),
                'can_rollback' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'history_id' => $historyId,
                'message' => 'Test rollback record created'
            ]);
        });

        Route::post('/debug/attempt-rollback/{id}', function ($id) {
            app()->instance('apex.audit.request.config', []);
            $rollbackService = app(\App\Apex\Audit\Services\RollbackService::class);

            $result = $rollbackService->attemptRollback($id);

            return response()->json($result, $result['success'] ? 200 : 400);
        });

        Route::post('/debug/connection-test/{id}', function ($id) {
            try {
                // Log all connection details
                Log::info('=== ROLLBACK CONNECTION DEBUG ===', [
                    'history_id' => $id,
                    'default_connection' => config('database.default'),
                    'audit_connection_config' => config('apex.audit.audit.connection'),
                    'current_database' => DB::connection()->getDatabaseName(),
                    'tenant_id' => tenant('id'),
                    'tenant_db' => tenant('tenancy_db_name'),
                ]);

                // Test ApexHistory model connection
                $historyModel = new \App\Apex\Audit\Models\ApexHistory();
                Log::info('ApexHistory Model Connection:', [
                    'connection_name' => $historyModel->getConnectionName(),
                    'database_name' => DB::connection($historyModel->getConnectionName())->getDatabaseName(),
                ]);

                // Test direct query
                $history = DB::connection('tenant')->table('apex_history')->find($id);
                Log::info('Direct Query Result:', [
                    'found' => !is_null($history),
                    'model_type' => $history->model_type ?? 'null',
                    'can_rollback' => $history->can_rollback ?? 'null',
                ]);

                // Test ApexHistory::find
                $historyEloquent = \App\Apex\Audit\Models\ApexHistory::find($id);
                Log::info('Eloquent Query Result:', [
                    'found' => !is_null($historyEloquent),
                    'connection_used' => $historyEloquent ? $historyEloquent->getConnectionName() : 'null',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connection details logged to laravel.log',
                    'current_db' => DB::connection()->getDatabaseName(),
                    'history_connection' => $historyModel->getConnectionName(),
                    'found_history' => !is_null($history),
                ]);
            } catch (\Exception $e) {
                Log::error('Connection test failed:', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        });

        // Enhanced rollback test
        Route::post('/debug/rollback/{id}', function ($id) {
            try {
                app()->instance('apex.audit.request.config', []);
                $rollbackService = app(\App\Apex\Audit\Services\RollbackService::class);
                $result = $rollbackService->rollback($id);

                return response()->json([
                    'success' => true,
                    'message' => 'Rollback completed',
                    'result' => $result
                ]);
            } catch (\App\Apex\Audit\Exceptions\RollbackException $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getUserMessage(),
                    'details' => $e->toArray(),
                ], 400);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => array_slice($e->getTrace(), 0, 3)
                ], 500);
            }
        });

        // Debug specific history record
        Route::get('/api/apex/audit/debug/history/{id}', function ($id) {
            try {
                $history = DB::connection('tenant')->table('apex_history')->find($id);
                if (!$history) {
                    return response()->json(['error' => 'History record not found'], 404);
                }

                $rollbackData = $history->rollback_data ? json_decode($history->rollback_data, true) : null;
                $fieldChanges = $history->field_changes ? json_decode($history->field_changes, true) : null;

                return response()->json([
                    'history_record' => $history,
                    'rollback_data_parsed' => $rollbackData,
                    'field_changes_parsed' => $fieldChanges,
                    'can_rollback' => (bool) $history->can_rollback,
                    'is_rolled_back' => !is_null($history->rolled_back_at),
                    'validation_checks' => [
                        'has_rollback_data' => !is_null($rollbackData),
                        'rollback_data_has_action' => isset($rollbackData['action']),
                        'rollback_data_has_values' => isset($rollbackData['values']),
                        'model_type_valid' => !empty($history->model_type) && $history->model_type !== '1',
                        'model_id_valid' => !empty($history->model_id) && $history->model_id !== '1',
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        })->withoutMiddleware(['web']);

        // Test rollback data creation
        Route::post('/api/apex/audit/debug/create-rollback-test', function () {
            try {
                // Create a test history record with proper rollback data
                $historyId = DB::connection('tenant')->table('apex_history')->insertGetId([
                    'audit_id' => null,
                    'model_type' => 'App\\Models\\Car', // Proper model class name
                    'model_id' => '999', // Test model ID
                    'action_type' => 'update',
                    'field_changes' => json_encode([
                        'make' => ['from' => 'Toyota', 'to' => 'Honda'],
                        'model' => ['from' => 'Camry', 'to' => 'Civic']
                    ]),
                    'description' => 'Test record for rollback debugging',
                    'rollback_data' => json_encode([
                        'action' => 'restore_values',
                        'values' => [
                            'make' => 'Toyota',
                            'model' => 'Camry'
                        ],
                        'changed_fields' => ['make', 'model']
                    ]),
                    'can_rollback' => true,
                    'rolled_back_at' => null,
                    'rolled_back_by' => null,
                    'user_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Test rollback record created',
                    'history_id' => $historyId,
                    'test_data' => [
                        'model_type' => 'App\\Models\\Car',
                        'model_id' => '999',
                        'action_type' => 'update',
                        'has_rollback_data' => true,
                        'can_rollback' => true
                    ]
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        })->withoutMiddleware(['web']);

        // Enhanced rollback test with detailed error reporting
        Route::post('/api/apex/audit/debug/rollback/{id}', function ($id) {
            try {
                // First, let's validate the record step by step
                $history = DB::connection('tenant')->table('apex_history')->find($id);
                if (!$history) {
                    return response()->json(['error' => 'History record not found'], 404);
                }

                $validation = [
                    'record_exists' => true,
                    'can_rollback_flag' => (bool) $history->can_rollback,
                    'not_already_rolled_back' => is_null($history->rolled_back_at),
                    'has_rollback_data' => !is_null($history->rollback_data),
                    'rollback_enabled_globally' => config('apex.audit.history.allow_rollback', true),
                ];

                // Parse rollback data
                $rollbackData = null;
                if ($history->rollback_data) {
                    $rollbackData = json_decode($history->rollback_data, true);
                    $validation['rollback_data_valid_json'] = $rollbackData !== null;
                    $validation['rollback_data_has_action'] = isset($rollbackData['action']);
                    $validation['rollback_data_has_values'] = isset($rollbackData['values']);
                } else {
                    $validation['rollback_data_valid_json'] = false;
                    $validation['rollback_data_has_action'] = false;
                    $validation['rollback_data_has_values'] = false;
                }

                // Check if all validations pass
                $allValidationsPassed = !in_array(false, $validation);

                if (!$allValidationsPassed) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Rollback validation failed',
                        'validation_results' => $validation,
                        'history_record' => $history,
                        'rollback_data' => $rollbackData
                    ], 400);
                }

                // If validations pass, try the actual rollback
                $rollbackService = app(\App\Apex\Audit\Services\RollbackService::class);

                // Set request config for services (required by some services)
                app()->instance('apex.audit.request.config', []);

                $result = $rollbackService->rollback($id);

                return response()->json([
                    'success' => true,
                    'message' => 'Rollback completed successfully',
                    'result' => $result,
                    'validation_results' => $validation
                ]);
            } catch (\App\Apex\Audit\Exceptions\RollbackException $e) {
                return response()->json([
                    'success' => false,
                    'error_type' => 'RollbackException',
                    'error' => $e->getUserMessage(),
                    'technical_message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'details' => $e->toArray(),
                    'context' => $e->getContext(),
                ], 400);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error_type' => get_class($e),
                    'error' => $e->getMessage(),
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine(),
                    'trace' => array_slice($e->getTrace(), 0, 5)
                ], 500);
            }
        })->withoutMiddleware(['web']);

        // Fix existing history records with missing rollback data
        Route::post('/api/apex/audit/debug/fix-rollback-data', function () {
            try {
                $fixed = 0;
                $errors = [];

                // Get history records that can_rollback but have no rollback_data
                $records = DB::connection('tenant')->table('apex_history')
                    ->where('can_rollback', true)
                    ->whereNull('rollback_data')
                    ->get();

                foreach ($records as $record) {
                    try {
                        $rollbackData = null;

                        switch ($record->action_type) {
                            case 'update':
                                // Try to construct rollback data from field_changes
                                if ($record->field_changes) {
                                    $fieldChanges = json_decode($record->field_changes, true);
                                    if ($fieldChanges) {
                                        $oldValues = [];
                                        $changedFields = [];

                                        foreach ($fieldChanges as $field => $change) {
                                            if (isset($change['from'])) {
                                                $oldValues[$field] = $change['from'];
                                                $changedFields[] = $field;
                                            }
                                        }

                                        if (!empty($oldValues)) {
                                            $rollbackData = [
                                                'action' => 'restore_values',
                                                'values' => $oldValues,
                                                'changed_fields' => $changedFields
                                            ];
                                        }
                                    }
                                }
                                break;

                            case 'delete':
                                // For deletes, we'd need the original record data
                                // This is harder to reconstruct without the audit record
                                $rollbackData = [
                                    'action' => 'restore_record',
                                    'values' => [] // Would need original data
                                ];
                                break;
                        }

                        if ($rollbackData) {
                            DB::connection('tenant')->table('apex_history')
                                ->where('id', $record->id)
                                ->update([
                                    'rollback_data' => json_encode($rollbackData),
                                    'updated_at' => now()
                                ]);
                            $fixed++;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Record {$record->id}: " . $e->getMessage();
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => "Fixed rollback data for {$fixed} records",
                    'total_checked' => count($records),
                    'fixed_count' => $fixed,
                    'errors' => $errors
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        })->withoutMiddleware(['web']);


        // Signature verification
        Route::post('/admin/verify-signatures', function () {
            try {
                $sample = request()->get('sample', 10);

                // Only check records created through the service (have proper structure)
                $audits = DB::connection('tenant')->table('apex_audit')
                    ->whereNotNull('audit_uuid')
                    ->whereNotNull('signature')
                    ->where('signature', '!=', 'test_signature')
                    ->where('signature', '!=', 'test')
                    ->orderBy('created_at', 'desc')
                    ->limit($sample)
                    ->get();

                $results = [
                    'total_checked' => count($audits),
                    'valid' => 0,
                    'invalid' => 0,
                    'errors' => [],
                ];

                $signatureService = app(\App\Apex\Audit\Services\AuditSignatureService::class);

                foreach ($audits as $audit) {
                    try {
                        // Convert to array for signature verification
                        $auditData = [
                            'audit_uuid' => $audit->audit_uuid,
                            'event_type' => $audit->event_type,
                            'action_type' => $audit->action_type,
                            'model_type' => $audit->model_type,
                            'model_id' => $audit->model_id,
                            'user_id' => $audit->user_id,
                            'old_values' => json_decode($audit->old_values, true),
                            'new_values' => json_decode($audit->new_values, true),
                            'created_at' => $audit->created_at,
                        ];

                        $expectedSignature = $signatureService->generateSignature($auditData);

                        if (hash_equals($expectedSignature, $audit->signature)) {
                            $results['valid']++;
                        } else {
                            $results['invalid']++;
                            $results['errors'][] = "Invalid signature for audit ID {$audit->id}";
                        }
                    } catch (\Exception $e) {
                        $results['errors'][] = "Error checking audit ID {$audit->id}: " . $e->getMessage();
                    }
                }

                return response()->json($results);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                ], 500);
            }
        });

        // Restore all test routes
        Route::post('/test/audit-service-debug', function () {
            $results = [];

            try {
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);
                $results['service_exists'] = true;
                $results['service_class'] = get_class($auditService);
            } catch (\Exception $e) {
                $results['service_error'] = $e->getMessage();
            }

            try {
                app()->instance('apex.audit.request.config', []);
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);

                $result = $auditService->logCustomAction([
                    'event_type' => 'custom',
                    'action_type' => 'debug_test_' . time(),
                    'model_type' => 'TestModel',
                    'model_id' => '999',
                    'table_name' => 'tests',
                    'additional_data' => ['test' => true],
                ]);

                $results['logCustomAction'] = [
                    'success' => true,
                    'result' => $result,
                ];
            } catch (\Throwable $e) {
                $results['logCustomAction'] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }

            $results['database_check'] = [
                'audit_count' => DB::connection('tenant')->table('apex_audit')->count(),
                'history_count' => DB::connection('tenant')->table('apex_history')->count(),
                'last_audit' => DB::connection('tenant')->table('apex_audit')
                    ->orderBy('id', 'desc')
                    ->first(),
            ];

            $results['service_methods'] = get_class_methods($auditService);

            return response()->json($results);
        });

        // Check rollback status
        Route::get('/history/rollback-status', function () {
            $history = DB::connection('tenant')->table('apex_history')
                ->select('id', 'action_type', 'can_rollback', 'rolled_back_at', 'model_type', 'model_id')
                ->get();

            return response()->json([
                'history_records' => $history,
                'rollback_enabled' => config('apex.audit.history.allow_rollback'),
            ]);
        });

        Route::post('/test/audit-service-internal', function () {
            $results = [];

            try {
                $auditService = app(\App\Apex\Audit\Services\AuditService::class);
                $signatureService = app(\App\Apex\Audit\Services\AuditSignatureService::class);

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

                $testData['signature'] = $signatureService->generateSignature($testData);

                $directInsertId = DB::connection('tenant')->table('apex_audit')->insertGetId($testData);
                $results['direct_insert'] = [
                    'success' => true,
                    'id' => $directInsertId,
                ];

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

                $results['queue_config'] = [
                    'enabled' => config('apex.audit.audit.queue.enabled'),
                    'connection' => config('apex.audit.audit.queue.connection'),
                    'queue_name' => config('apex.audit.audit.queue.queue'),
                ];

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