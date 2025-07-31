<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add APEX Audit test routes
        'apex/audit/test/*',
        'apex/audit/admin/*',
        'apex/audit/language/set/*',
        'apex/audit/history/rollback/*',
        'apex/audit/middleware-test/*',
        // Add debug routes
        'debug-audit-create',
        'api-test/*',
    ];
}
