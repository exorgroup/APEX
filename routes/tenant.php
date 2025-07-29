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
use Illuminate\Support\Facades\Artisan;
use App\Apex\Audit\Services\AuditService;
use App\Apex\Audit\Services\HistoryService;
use App\Apex\Audit\Services\RollbackService;
use App\Apex\Audit\Services\ApexAuditLanguageService;
use App\Apex\Audit\Exceptions\RollbackException;

/*
|--------------------------------------------------------------------------
| Keep Your Existing Routes Here
|--------------------------------------------------------------------------
|
| Add your existing tenant routes above this comment block.
| The APEX Audit routes below are additional testing routes.
|
*/

// Your existing routes go here...
// Route::get('/', [DashboardController::class, 'index']);
// Route::resource('users', UserController::class);
// etc...

/*
|--------------------------------------------------------------------------
| APEX Audit Testing Routes
|--------------------------------------------------------------------------
|
| Routes for testing and managing APEX Audit functionality.
| These routes provide comprehensive testing endpoints for all audit features.
|
*/

Route::get('/debug-connection', function () {
    return response()->json([
        'default_connection' => config('database.default'),
        'tenant_id' => function_exists('tenant') && tenant() ? tenant('id') : 'no tenant',
        'current_database' => DB::connection()->getDatabaseName(),
        'audit_connection_config' => config('apex.audit.audit.connection'),
        'tenancy_enabled' => config('apex.audit.tenancy.enabled'),
        'tables_in_db' => DB::select('SHOW TABLES'),
    ]);
})->name('debug-connection');

Route::prefix('apex/audit')->name('apex.audit.')->group(function () {

    // Main Dashboard Route
    Route::get('/dashboard', function () {
        $langService = app(\App\Apex\Audit\Services\ApexAuditLanguageService::class);

        return response()->json([
            'success' => true,
            'message' => 'APEX Audit Testing Dashboard',
            'system_status' => [
                'audit_enabled' => config('apex.audit.audit.enabled'),
                'history_enabled' => config('apex.audit.history.enabled'),
                'signatures_enabled' => config('apex.audit.audit.signature.enabled'),
                'queue_enabled' => config('apex.audit.audit.queue.enabled'),
                'current_language' => $langService->getCurrentLanguage(),
                'supported_languages' => count($langService->getSupportedLanguages()),
            ],
            'available_tests' => [
                'basic_logging' => 'Test basic audit logging functionality',
                'ui_actions' => 'Test UI action tracking',
                'history_management' => 'Test history viewing and filtering',
                'rollback_system' => 'Test rollback functionality',
                'language_support' => 'Test multi-language features',
                'signature_verification' => 'Test digital signature integrity',
                'widget_integration' => 'Test APEX widget configurations',
                'middleware_configs' => 'Test route-level configurations',
            ],
            'quick_links' => [
                'create_test_data' => '/apex/audit/test/create-test-data',
                'view_history' => '/apex/audit/history/list',
                'system_stats' => '/apex/audit/admin/statistics',
                'language_test' => '/apex/audit/language/test-translations',
                'run_test_suite' => '/apex/audit/test/run-test-suite',
            ],
            'current_config' => [
                'audit_enabled' => config('apex.audit.audit.enabled'),
                'language' => $langService->getCurrentLanguage(),
                'supported_languages' => $langService->getSupportedLanguages(),
                'detection_method' => config('apex.audit.language.detection_method'),
            ],
        ]);
    })->name('dashboard');

    // Test Data Management Routes
    Route::prefix('test')->name('test.')->group(function () {

        // Create test data for auditing
        Route::post('/create-test-data', function () {
            $auditService = app(AuditService::class);
            $results = [];

            // Ensure we're using the tenant's database connection
            $tenantConnection = config('database.default');

            // Create various test scenarios
            $testScenarios = [
                ['action' => 'create', 'model' => 'Car', 'data' => ['make' => 'Toyota', 'model' => 'Camry', 'year' => 2024]],
                ['action' => 'update', 'model' => 'Car', 'data' => ['price' => 25000, 'color' => 'Blue']],
                ['action' => 'delete', 'model' => 'Car', 'data' => ['id' => 1, 'reason' => 'Test deletion']],
                ['action' => 'restore', 'model' => 'Car', 'data' => ['id' => 1, 'restored_by' => 'admin']],
                ['action' => 'custom', 'model' => 'System', 'data' => ['test_run' => now(), 'type' => 'integration_test']],
            ];

            foreach ($testScenarios as $i => $scenario) {
                $auditService->logCustomAction([
                    'event_type' => $scenario['action'] === 'custom' ? 'custom' : 'model_crud',
                    'action_type' => $scenario['action'],
                    'model_type' => "TestModel{$scenario['model']}",
                    'model_id' => (string) ($i + 1),
                    'table_name' => strtolower($scenario['model']) . 's',
                    'additional_data' => array_merge($scenario['data'], [
                        'test_scenario' => true,
                        'scenario_index' => $i + 1,
                        'created_at' => now()->toISOString(),
                        'language' => apex_lang(),
                        'tenant' => tenant('id') ?? 'unknown',
                        'database_connection' => $tenantConnection,
                    ]),
                    'source_element' => 'test-data-generator',
                ]);

                $results[] = apex_trans('descriptions.performed_action', [
                    'action' => $scenario['action'],
                    'model' => $scenario['model'],
                    'id' => $i + 1
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => apex_trans('success.data_exported'),
                'results' => $results,
                'count' => count($results),
                'language' => apex_lang(),
                'tenant_info' => [
                    'tenant_id' => tenant('id') ?? 'unknown',
                    'database_connection' => $tenantConnection,
                ],
            ]);
        })->name('create-data');

        // Test audit logging directly
        Route::post('/test-audit-log', function (Request $request) {
            $auditService = app(AuditService::class);
            $customData = $request->get('data', []);

            $auditService->logCustomAction([
                'event_type' => 'custom',
                'action_type' => 'manual_test',
                'model_type' => 'TestModel',
                'model_id' => '999',
                'table_name' => 'test_table',
                'additional_data' => array_merge([
                    'test_type' => 'manual_audit_test',
                    'user_input' => $customData,
                    'timestamp' => now()->toISOString(),
                    'language' => apex_lang(),
                    'ip_address' => $request->ip(),
                ], $customData),
                'source_element' => 'manual-test-form',
            ]);

            return response()->json([
                'success' => true,
                'message' => apex_trans('success.audit_created'),
                'language' => apex_lang(),
                'test_data' => $customData,
            ]);
        })->name('log-audit');

        // Test UI action logging
        Route::post('/test-ui-action', function (Request $request) {
            $auditService = app(AuditService::class);
            $actionType = $request->get('action_type', 'button_click');
            $element = $request->get('element', 'test-button');

            $auditService->logUIAction([
                'action_type' => $actionType,
                'source_element' => $element,
                'additional_data' => [
                    'page_context' => 'audit_testing',
                    'user_agent' => $request->userAgent(),
                    'test_mode' => true,
                    'timestamp' => now()->toISOString(),
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'UI action logged successfully',
                'action_type' => $actionType,
                'element' => $element,
                'language' => apex_lang(),
            ]);
        })->name('log-ui-action');

        // Run comprehensive test suite
        Route::post('/run-test-suite', function () {
            $auditService = app(AuditService::class);
            $historyService = app(HistoryService::class);
            $results = [];

            // Test 1: Basic audit logging
            try {
                $auditService->logCustomAction([
                    'event_type' => 'test_suite',
                    'action_type' => 'basic_test',
                    'model_type' => 'TestSuite',
                    'model_id' => '1',
                    'additional_data' => ['test' => 'basic_logging'],
                ]);
                $results['basic_logging'] = ['status' => 'passed', 'message' => 'Basic audit logging works'];
            } catch (\Exception $e) {
                $results['basic_logging'] = ['status' => 'failed', 'message' => $e->getMessage()];
            }

            // Test 2: Language system
            try {
                $currentLang = apex_lang();
                $translation = apex_trans('history.title');
                $results['language_system'] = [
                    'status' => 'passed',
                    'current_language' => $currentLang,
                    'sample_translation' => $translation
                ];
            } catch (\Exception $e) {
                $results['language_system'] = ['status' => 'failed', 'message' => $e->getMessage()];
            }

            // Test 3: History service
            try {
                $summary = $historyService->getHistorySummary();
                $results['history_service'] = [
                    'status' => 'passed',
                    'total_records' => $summary['total_records'] ?? 0
                ];
            } catch (\Exception $e) {
                $results['history_service'] = ['status' => 'failed', 'message' => $e->getMessage()];
            }

            // Test 4: Configuration
            $configTests = [
                'audit_enabled' => config('apex.audit.audit.enabled'),
                'signatures_enabled' => config('apex.audit.audit.signature.enabled'),
                'language_supported' => count(apex_supported_languages()) > 0,
            ];

            $results['configuration'] = [
                'status' => array_reduce($configTests, fn($carry, $test) => $carry && $test, true) ? 'passed' : 'failed',
                'tests' => $configTests
            ];

            return response()->json([
                'success' => true,
                'message' => 'Test suite completed',
                'results' => $results,
                'overall_status' => !in_array('failed', array_column($results, 'status')) ? 'passed' : 'failed',
                'executed_at' => now()->toISOString(),
            ]);
        })->name('run-test-suite');
    });

    // History Management Routes
    Route::prefix('history')->name('history.')->group(function () {

        // Get history for testing
        Route::get('/list', function (Request $request) {
            $historyService = app(HistoryService::class);

            $filters = $request->only(['action_type', 'user_id', 'search', 'date_from', 'date_to']);
            $perPage = $request->get('per_page', 20);

            $history = $historyService->getRecentActivity(30, $perPage, $filters);
            $summary = $historyService->getHistorySummary();

            return response()->json([
                'success' => true,
                'data' => $history,
                'summary' => $summary,
                'filters_applied' => $filters,
                'language' => apex_lang(),
            ]);
        })->name('list');

        // Preview rollback
        Route::get('/rollback/{historyId}/preview', function ($historyId) {
            try {
                $rollbackService = app(RollbackService::class);
                $preview = $rollbackService->previewRollback($historyId);

                return response()->json([
                    'success' => true,
                    'preview' => $preview,
                    'title' => apex_trans('rollback.preview_title'),
                    'description' => apex_trans('rollback.preview_description'),
                ]);
            } catch (RollbackException $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getUserMessage(),
                    'details' => $e->toArray(),
                ], 400);
            }
        })->name('rollback.preview');

        // Test rollback functionality
        Route::post('/rollback/{historyId}', function ($historyId) {
            try {
                $rollbackService = app(RollbackService::class);
                $result = $rollbackService->rollback($historyId);

                return response()->json([
                    'success' => true,
                    'message' => apex_trans('rollback.success'),
                    'rolled_back' => $result,
                ]);
            } catch (RollbackException $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getUserMessage(),
                    'details' => $e->toArray(),
                ], 400);
            }
        })->name('rollback');

        // Export history
        Route::get('/export', function (Request $request) {
            $historyService = app(HistoryService::class);
            $filters = $request->only(['action_type', 'user_id', 'date_from', 'date_to']);

            $data = $historyService->exportHistory(null, null, $filters);

            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => count($data),
                'exported_at' => now()->toISOString(),
                'filters' => $filters,
            ]);
        })->name('export');
    });

    // Language Testing Routes
    Route::prefix('language')->name('language.')->group(function () {

        // Test language detection
        Route::get('/current', function () {
            $langService = app(ApexAuditLanguageService::class);

            return response()->json([
                'current_language' => $langService->getCurrentLanguage(),
                'supported_languages' => $langService->getSupportedLanguages(),
                'detection_method' => config('apex.audit.language.detection_method'),
                'direction' => $langService->getLanguageDirection(),
                'cache_enabled' => config('apex.audit.language.cache.enabled'),
            ]);
        })->name('current');

        // Set language
        Route::post('/set/{language}', function ($language) {
            $langService = app(ApexAuditLanguageService::class);

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
                'message' => 'Language set successfully',
                'language' => $language,
                'test_translations' => [
                    'title' => apex_trans('history.title'),
                    'success' => apex_trans('rollback.success'),
                    'created' => apex_trans('actions.create'),
                ],
            ]);
        })->name('set');

        // Test translations
        Route::get('/test-translations', function () {
            return response()->json([
                'current_language' => apex_lang(),
                'language_name' => app(ApexAuditLanguageService::class)->getSupportedLanguages()[apex_lang()] ?? 'Unknown',
                'sample_translations' => [
                    'actions' => [
                        'create' => apex_trans('actions.create'),
                        'update' => apex_trans('actions.update'),
                        'delete' => apex_trans('actions.delete'),
                        'restore' => apex_trans('actions.restore'),
                    ],
                    'messages' => [
                        'history_title' => apex_trans('history.title'),
                        'rollback_success' => apex_trans('rollback.success'),
                        'no_records' => apex_trans('history.no_records'),
                    ],
                    'dynamic_examples' => [
                        'created_new' => apex_trans('descriptions.created_new', [
                            'model' => 'Car',
                            'id' => '123'
                        ]),
                        'updated_model' => apex_trans('descriptions.updated_model', [
                            'model' => 'User',
                            'id' => '456',
                            'fields' => 'name, email'
                        ]),
                    ],
                ],
            ]);
        })->name('test-translations');
    });

    // Verification and Cleanup Routes
    Route::prefix('admin')->name('admin.')->group(function () {

        // Verify signatures
        Route::post('/verify-signatures', function (Request $request) {
            $sample = $request->get('sample', 10);

            Artisan::call('apex:audit-verify', [
                '--sample' => $sample,
            ]);

            return response()->json([
                'success' => true,
                'output' => Artisan::output(),
                'message' => apex_trans('verification.verification_completed'),
                'sample_size' => $sample,
            ]);
        })->name('verify');

        // Test cleanup (dry run)
        Route::post('/test-cleanup', function (Request $request) {
            $days = $request->get('days', 30);

            Artisan::call('apex:audit-cleanup', [
                '--dry-run' => true,
                '--days' => $days,
            ]);

            return response()->json([
                'success' => true,
                'output' => Artisan::output(),
                'message' => 'Cleanup test completed (dry run)',
                'retention_days' => $days,
            ]);
        })->name('test-cleanup');

        // Get audit statistics
        Route::get('/statistics', function () {
            $historyService = app(HistoryService::class);
            $rollbackService = app(RollbackService::class);

            return response()->json([
                'audit_system' => [
                    'history_summary' => $historyService->getHistorySummary(),
                    'rollback_stats' => $rollbackService->getRollbackStatistics(),
                    'recent_activity' => $historyService->getRecentActivity(7, 5),
                ],
                'configuration' => [
                    'audit_enabled' => config('apex.audit.audit.enabled'),
                    'history_enabled' => config('apex.audit.history.enabled'),
                    'signatures_enabled' => config('apex.audit.audit.signature.enabled'),
                    'queue_enabled' => config('apex.audit.audit.queue.enabled'),
                    'language_method' => config('apex.audit.language.detection_method'),
                    'current_language' => apex_lang(),
                ],
                'performance' => [
                    'cache_enabled' => config('apex.audit.language.cache.enabled'),
                    'compression_enabled' => config('apex.audit.audit.performance.compress_large_data'),
                    'batch_processing' => config('apex.audit.audit.batch.enabled'),
                ],
                'generated_at' => now()->toISOString(),
            ]);
        })->name('statistics');
    });

    // Widget Testing Routes
    Route::prefix('widgets')->name('widgets.')->group(function () {

        // Get history widget configuration
        Route::get('/history/{modelType?}/{modelId?}', function ($modelType = null, $modelId = null) {
            $historyService = app(HistoryService::class);

            $config = $historyService->getWidgetConfig($modelType, $modelId, [
                'title' => apex_trans('widgets.history_widget'),
            ]);

            return response()->json([
                'success' => true,
                'widget_config' => $config,
                'model_type' => $modelType,
                'model_id' => $modelId,
                'language' => apex_lang(),
            ]);
        })->name('history-config');

        // Get audit summary widget data
        Route::get('/summary', function () {
            $historyService = app(HistoryService::class);

            $summary = $historyService->getHistorySummary();
            $timeline = $historyService->getHistoryTimeline(null, null, 30);

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'timeline' => $timeline,
                'widget_title' => apex_trans('widgets.audit_summary'),
                'language' => apex_lang(),
            ]);
        })->name('summary');
    });
});

// Middleware configuration routes for testing
Route::prefix('apex/audit/middleware-test')->name('apex.audit.middleware.')->group(function () {

    // Test different middleware configurations
    Route::middleware(['apex.audit.config:source_context=api,track_fields=name,email'])
        ->post('/api-style', function (Request $request) {
            return response()->json([
                'message' => 'API-style audit configuration applied',
                'config' => app('apex.audit.request.config', []),
                'request_info' => [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'route' => $request->route()?->getName(),
                ],
            ]);
        })->name('api-test');

    Route::middleware([
        'apex.audit.config:source_context=admin',
        'apex.audit.config:enhanced_tracking=true',
        'apex.audit.config:additional_data.admin_level=super'
    ])->post('/admin-style', function (Request $request) {
        return response()->json([
            'message' => 'Admin-style audit configuration applied',
            'config' => app('apex.audit.request.config', []),
            'request_info' => [
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => $request->route()?->getName(),
            ],
        ]);
    })->name('admin-test');

    Route::middleware(['apex.audit.config:disable_audit=true'])
        ->post('/disabled', function (Request $request) {
            return response()->json([
                'message' => 'Audit disabled for this route',
                'config' => app('apex.audit.request.config', []),
                'request_info' => [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'route' => $request->route()?->getName(),
                ],
            ]);
        })->name('disabled-test');

    // Test JSON configuration
    Route::middleware(['apex.audit.config:{"source_context":"json_test","track_all_fields":true}'])
        ->post('/json-config', function (Request $request) {
            return response()->json([
                'message' => 'JSON configuration applied',
                'config' => app('apex.audit.request.config', []),
            ]);
        })->name('json-test');
});

/*
|--------------------------------------------------------------------------
| Quick Testing URLs
|--------------------------------------------------------------------------
|
| Use these URLs to quickly test APEX Audit functionality:
|
| Dashboard:
| GET /apex/audit/dashboard
|
| Create Test Data:
| POST /apex/audit/test/create-test-data
|
| View History:
| GET /apex/audit/history/list
|
| Test Language:
| GET /apex/audit/language/current
| POST /apex/audit/language/set/es
| GET /apex/audit/language/test-translations
|
| System Statistics:
| GET /apex/audit/admin/statistics
|
| Run Full Test Suite:
| POST /apex/audit/test/run-test-suite
|
| Test Middleware:
| POST /apex/audit/middleware-test/api-style
| POST /apex/audit/middleware-test/admin-style
|
| All routes return JSON responses suitable for AJAX calls.
|
*/