# Test All PRO Tables
$TenantName = "foo"
$UserId = 17

Set-Location "C:\Work Folder\APEX Framework\APEX"

Write-Host "=== Testing MFA Config Service ===" -ForegroundColor Cyan

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
    `$mfaConfig = `$service->enableTOTP(`$user, `$secret);
    echo 'MFA Config Created - ID: ' . `$mfaConfig->id . PHP_EOL;
    echo 'Method: ' . `$mfaConfig->method . PHP_EOL;
} catch (Exception `$e) {
    echo 'MFA Config Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Testing Social Account Service ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Apex\Autentica\Pro\Services\OAuth2Service;
use Apex\Autentica\Pro\Models\SocialAccount;
use App\Models\User;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$service = new OAuth2Service();
`$user = User::find($UserId);

try {
    `$tokenData = ['access_token' => 'test_token_123', 'expires_in' => 3600];
    `$userData = ['id' => 'google_user_123', 'email' => 'test@gmail.com'];
    
    `$socialAccount = `$service->linkSocialAccount(`$user, 'google', `$tokenData, `$userData);
    echo 'Social Account Created - ID: ' . `$socialAccount->id . PHP_EOL;
    echo 'Provider: ' . `$socialAccount->provider . PHP_EOL;
} catch (Exception `$e) {
    echo 'Social Account Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Testing Trusted Device Service ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Apex\Autentica\Pro\Services\DeviceManagementService;
use App\Models\User;
use Illuminate\Http\Request;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$service = new DeviceManagementService();
`$user = User::find($UserId);

try {
    `$request = new Request();
    `$request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0');
    `$request->server->set('REMOTE_ADDR', '192.168.1.100');
    
    `$device = `$service->registerTrustedDevice(`$user, `$request, 'Test Device');
    echo 'Trusted Device Created - ID: ' . `$device->id . PHP_EOL;
    echo 'Device Name: ' . `$device->device_name . PHP_EOL;
} catch (Exception `$e) {
    echo 'Trusted Device Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Testing Session Service ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Apex\Autentica\Pro\Services\SessionManager;
use App\Models\User;
use Illuminate\Http\Request;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$service = new SessionManager();
`$user = User::find($UserId);

try {
    `$request = new Request();
    `$request->headers->set('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0');
    `$request->server->set('REMOTE_ADDR', '192.168.1.100');
    
    session()->start();
    
    `$session = `$service->createSession(`$user, `$request);
    echo 'Session Created - ID: ' . `$session->id . PHP_EOL;
    echo 'Session ID: ' . substr(`$session->session_id, 0, 10) . '...' . PHP_EOL;
} catch (Exception `$e) {
    echo 'Session Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Testing Auth Method Service ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Apex\Autentica\Pro\Models\AuthMethod;
use App\Models\User;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$user = User::find($UserId);

try {
    `$authMethod = AuthMethod::create([
        'user_id' => `$user->id,
        'method' => 'password',
        'enabled' => true,
        'config' => ['strength' => 'strong'],
        'last_used_at' => now()
    ]);
    
    echo 'Auth Method Created - ID: ' . `$authMethod->id . PHP_EOL;
    echo 'Method: ' . `$authMethod->method . PHP_EOL;
} catch (Exception `$e) {
    echo 'Auth Method Error: ' . `$e->getMessage() . PHP_EOL;
}
"

Write-Host "=== Final Table Count Check ===" -ForegroundColor Cyan

php artisan tinker --execute="
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Support\Facades\DB;

`$tenant = \App\Models\Tenant::where('id', '$TenantName')->first();
Tenancy::initialize(`$tenant);

`$tables = [
    'au10_mfa_configs',
    'au10_social_accounts', 
    'au10_mfa_backup_codes',
    'au10_trusted_devices',
    'au10_sessions',
    'au10_auth_methods',
    'au10_auth_tokens'
];

foreach (`$tables as `$table) {
    try {
        `$count = DB::table(`$table)->count();
        echo `$table . ': ' . `$count . ' records' . PHP_EOL;
    } catch (Exception `$e) {
        echo `$table . ': ERROR' . PHP_EOL;
    }
}
"

Write-Host "=== All Table Tests Complete ===" -ForegroundColor Green