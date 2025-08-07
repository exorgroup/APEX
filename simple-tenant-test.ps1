# Simple Tenant Test Script - Fixed Variable Escaping
$TenantName = "foo"
$UserId = 17

Set-Location "C:\Work Folder\APEX Framework\APEX"

Write-Host "=== Testing Tenant Context Switch ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use App\Models\User;

echo 'Switching to tenant: $TenantName' . PHP_EOL;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
if (!`$tenant) {
    echo 'ERROR: Tenant not found!' . PHP_EOL;
    exit(1);
}

Tenancy::initialize(`$tenant);
echo 'Tenant initialized successfully' . PHP_EOL;

`$user = User::find($UserId);
if (!`$user) {
    echo 'ERROR: User not found!' . PHP_EOL;
} else {
    echo 'User found: ' . `$user->name . ' (ID: ' . `$user->id . ')' . PHP_EOL;
}
"

Write-Host "=== Testing TOTP Service ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Apex\Autentica\Pro\Services\TOTPService;
use App\Models\User;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$service = new TOTPService();
`$user = User::find($UserId);

try {
    `$secret = `$service->generateSecret(`$user);
    echo 'TOTP Secret Generated: ' . `$secret . PHP_EOL;
} catch (Exception `$e) {
    echo 'TOTP Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Testing MFA Backup Service ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Apex\Autentica\Pro\Services\MfaBackupService;
use App\Models\User;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$service = new MfaBackupService();
`$user = User::find($UserId);

try {
    `$codes = `$service->generateBackupCodes(`$user, 3);
    echo 'Generated ' . count(`$codes) . ' backup codes' . PHP_EOL;
    foreach (`$codes as `$i => `$code) {
        echo 'Code ' . (`$i + 1) . ': ' . `$code . PHP_EOL;
    }
} catch (Exception `$e) {
    echo 'Backup Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Testing Auth Token Service ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Apex\Autentica\Pro\Services\AuthTokenService;
use App\Models\User;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$service = new AuthTokenService();
`$user = User::find($UserId);

try {
    `$tokenData = `$service->createToken(`$user, 'remember', 24);
    echo 'Token Created - ID: ' . `$tokenData['token_id'] . PHP_EOL;
    echo 'Token Preview: ' . substr(`$tokenData['token'], 0, 10) . '...' . PHP_EOL;
} catch (Exception `$e) {
    echo 'Token Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Checking Database Tables ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\DB;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

echo 'Database: ' . DB::connection()->getDatabaseName() . PHP_EOL;

`$tables = ['au10_mfa_configs', 'au10_mfa_backup_codes', 'au10_auth_tokens'];

foreach (`$tables as `$table) {
    try {
        `$count = DB::table(`$table)->count();
        echo `$table . ': ' . `$count . ' records' . PHP_EOL;
    } catch (Exception `$e) {
        echo `$table . ': TABLE MISSING' . PHP_EOL;
    }
}
"

Write-Host "=== Test Complete ===" -ForegroundColor Green