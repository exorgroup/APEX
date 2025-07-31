<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Apex\Audit\Services\AuditService;
use App\Apex\Audit\Models\ApexAudit;
use App\Apex\Audit\Models\ApexHistory;

class AuditDebugController extends Controller
{
    public function testCreate()
    {
        try {
            // Test 1: Direct model creation
            $audit = new ApexAudit();
            $audit->audit_uuid = Str::uuid();
            $audit->event_type = 'custom';
            $audit->action_type = 'debug_test';
            $audit->model_type = 'TestModel';
            $audit->model_id = '999';
            $audit->table_name = 'test_table';
            $audit->user_id = null;
            $audit->session_id = session()->getId();
            $audit->ip_address = request()->ip();
            $audit->user_agent = request()->userAgent();
            $audit->signature = 'test_signature';
            $audit->created_at = now();

            $saved = $audit->save();

            // Test 2: Using AuditService
            $auditService = app(AuditService::class);
            $serviceResult = null;
            $serviceError = null;

            try {
                $auditService->logCustomAction([
                    'event_type' => 'custom',
                    'action_type' => 'service_test',
                    'model_type' => 'TestModel',
                    'model_id' => '888',
                    'table_name' => 'test_table',
                    'additional_data' => ['test' => true],
                ]);
                $serviceResult = 'success';
            } catch (\Exception $e) {
                $serviceError = $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine();
            }

            // Check counts
            $auditCount = DB::connection('tenant')->table('apex_audit')->count();
            $historyCount = DB::connection('tenant')->table('apex_history')->count();

            return response()->json([
                'direct_save' => [
                    'saved' => $saved,
                    'id' => $audit->id,
                    'connection' => $audit->getConnectionName(),
                ],
                'service_test' => [
                    'result' => $serviceResult,
                    'error' => $serviceError,
                ],
                'counts' => [
                    'audit' => $auditCount,
                    'history' => $historyCount,
                ],
                'config' => [
                    'audit_enabled' => config('apex.audit.audit.enabled'),
                    'history_enabled' => config('apex.audit.history.enabled'),
                    'signature_enabled' => config('apex.audit.audit.signature.enabled'),
                    'secret_key_set' => !empty(config('apex.audit.audit.signature.secret_key')),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
