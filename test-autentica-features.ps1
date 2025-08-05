# Autentica PowerShell Test Scripts
# Save as: test-autentica-features.ps1
# Usage: .\test-autentica-features.ps1 -tenant foo

param(
    [Parameter(Mandatory=$true)]
    [string]$tenant
)

Write-Host "=== Autentica Feature Tests for Tenant: $tenant ===" -ForegroundColor Green
Write-Host ""

# Test 1: Check if a user exists and is in a group
Write-Host "1. USER AND GROUP MEMBERSHIP TESTS" -ForegroundColor Yellow
Write-Host "===================================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
`$user = App\Models\User::where('email', 'autentica.test@example.com')->first();
if (`$user) {
    echo 'User found: ' . `$user->name . ' (ID: ' . `$user->id . ')' . PHP_EOL;
    echo 'Groups: ' . implode(', ', `$user->getGroupNames()) . PHP_EOL;
    echo 'Is in Test Administrators: ' . (`$user->belongsToGroup('Test Administrators') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Is in Test Editors: ' . (`$user->belongsToGroup('Test Editors') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Is in Non-existent Group: ' . (`$user->belongsToGroup('Non-existent') ? 'YES' : 'NO') . PHP_EOL;
} else {
    echo 'User not found' . PHP_EOL;
}
"

Write-Host ""

# Test 2: Check user permissions
Write-Host "2. USER PERMISSION TESTS" -ForegroundColor Yellow
Write-Host "========================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
`$user = App\Models\User::where('email', 'autentica.test@example.com')->first();
if (`$user) {
    echo 'Checking user permissions:' . PHP_EOL;
    echo 'Can create users: ' . (`$user->hasPermission('users', 'create') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can read users: ' . (`$user->hasPermission('users', 'read') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can update users: ' . (`$user->hasPermission('users', 'update') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can delete users: ' . (`$user->hasPermission('users', 'delete') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can print users: ' . (`$user->hasPermission('users', 'print') ? 'YES' : 'NO') . PHP_EOL;
    echo PHP_EOL;
    echo 'Has ANY permission on posts (read OR update): ' . (`$user->hasAnyPermission('posts', ['read', 'update']) ? 'YES' : 'NO') . PHP_EOL;
    echo 'Has ALL permissions on posts (read AND update): ' . (`$user->hasAllPermissions('posts', ['read', 'update']) ? 'YES' : 'NO') . PHP_EOL;
}
"

Write-Host ""

# Test 3: Check group permissions
Write-Host "3. GROUP PERMISSION TESTS" -ForegroundColor Yellow
Write-Host "=========================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
`$group = Apex\Autentica\Core\Models\Group::where('name', 'Test Administrators')->first();
if (`$group) {
    echo 'Checking Test Administrators group permissions:' . PHP_EOL;
    echo 'Can read users: ' . (`$group->hasPermission('users', 'read') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can delete users: ' . (`$group->hasPermission('users', 'delete') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can create reports: ' . (`$group->hasPermission('reports', 'create') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can print reports: ' . (`$group->hasPermission('reports', 'print') ? 'YES' : 'NO') . PHP_EOL;
}
"

Write-Host ""

# Test 4: List all permissions for a user
Write-Host "4. LIST ALL USER PERMISSIONS" -ForegroundColor Yellow
Write-Host "============================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
`$user = App\Models\User::where('email', 'autentica.test@example.com')->first();
if (`$user) {
    `$permissions = `$user->getCachedPermissions();
    echo 'All permissions for user:' . PHP_EOL;
    foreach (`$permissions as `$resource => `$perms) {
        echo PHP_EOL . `$resource . ':' . PHP_EOL;
        foreach (`$perms as `$action => `$allowed) {
            if (strpos(`$action, 'can_') === 0 && `$allowed) {
                echo '  - ' . str_replace('can_', '', `$action) . PHP_EOL;
            }
        }
        if (!empty(`$perms['custom_permissions'])) {
            echo '  - custom: ' . `$perms['custom_permissions'] . PHP_EOL;
        }
    }
}
"

Write-Host ""

# Test 5: Security and login information
Write-Host "5. SECURITY AND LOGIN INFORMATION" -ForegroundColor Yellow
Write-Host "=================================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
`$user = App\Models\User::where('email', 'autentica.test@example.com')->first();
if (`$user) {
    echo 'Account locked: ' . (`$user->isAccountLocked() ? 'YES' : 'NO') . PHP_EOL;
    echo 'Failed login attempts (last 15 min): ' . `$user->getFailedLoginCount(15) . PHP_EOL;
    echo PHP_EOL . 'Recent security events:' . PHP_EOL;
    `$events = `$user->getRecentSecurityEvents(5);
    foreach (`$events as `$event) {
        echo '  - ' . `$event->event_type . ' at ' . `$event->created_at->format('H:i:s') . PHP_EOL;
    }
}
"

Write-Host ""

# Test 6: Create a new user and assign permissions
Write-Host "6. CREATE USER AND ASSIGN PERMISSIONS" -ForegroundColor Yellow
Write-Host "=====================================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
use Apex\Autentica\Core\Services\AuthorizationService;

// Create a new user
`$newUser = App\Models\User::create([
    'name' => 'Test Developer',
    'email' => 'developer@example.com',
    'password' => bcrypt('DevPass123!')
]);

echo 'Created user: ' . `$newUser->email . PHP_EOL;

// Add to Editors group
`$editorsGroup = Apex\Autentica\Core\Models\Group::where('name', 'Test Editors')->first();
if (`$editorsGroup) {
    `$newUser->joinGroup(`$editorsGroup);
    echo 'Added to Test Editors group' . PHP_EOL;
}

// Grant specific permissions
`$authz = new AuthorizationService();
`$authz->grant(`$newUser, 'posts', ['create', 'read', 'update']);
echo 'Granted posts permissions' . PHP_EOL;

// Verify permissions
echo PHP_EOL . 'Verifying permissions:' . PHP_EOL;
echo 'Can create posts: ' . (`$newUser->hasPermission('posts', 'create') ? 'YES' : 'NO') . PHP_EOL;
echo 'Can delete posts: ' . (`$newUser->hasPermission('posts', 'delete') ? 'YES' : 'NO') . PHP_EOL;
"

Write-Host ""

# Test 7: Test permission inheritance
Write-Host "7. PERMISSION INHERITANCE TEST" -ForegroundColor Yellow
Write-Host "==============================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
`$user = App\Models\User::where('email', 'developer@example.com')->first();
if (`$user) {
    echo 'User direct permissions on posts: create, read, update' . PHP_EOL;
    echo 'User is in Test Editors group' . PHP_EOL . PHP_EOL;
    
    // Show what permissions come from where
    `$directPerms = `$user->permissions()->with('systemResource')->get();
    echo 'Direct permissions:' . PHP_EOL;
    foreach (`$directPerms as `$perm) {
        if (`$perm->systemResource) {
            echo '  - ' . `$perm->systemResource->identifier . ': ';
            `$actions = [];
            foreach (['create', 'read', 'update', 'delete', 'print', 'history'] as `$action) {
                if (`$perm->{'can_' . `$action}) `$actions[] = `$action;
            }
            echo implode(', ', `$actions) . PHP_EOL;
        }
    }
}
"

Write-Host ""

# Test 8: Bulk permission operations
Write-Host "8. BULK PERMISSION OPERATIONS" -ForegroundColor Yellow
Write-Host "=============================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
use Apex\Autentica\Core\Services\AuthorizationService;

`$authz = new AuthorizationService();

// Copy permissions from one user to another
`$sourceUser = App\Models\User::where('email', 'autentica.test@example.com')->first();
`$targetUser = App\Models\User::where('email', 'developer@example.com')->first();

if (`$sourceUser && `$targetUser) {
    `$count = `$authz->copyUserPermissions(`$sourceUser, `$targetUser, ['users']);
    echo 'Copied ' . `$count . ' permissions from autentica.test@example.com to developer@example.com' . PHP_EOL;
    
    // Verify
    echo PHP_EOL . 'Target user now has:' . PHP_EOL;
    echo 'Can create users: ' . (`$targetUser->hasPermission('users', 'create') ? 'YES' : 'NO') . PHP_EOL;
    echo 'Can delete users: ' . (`$targetUser->hasPermission('users', 'delete') ? 'YES' : 'NO') . PHP_EOL;
}
"

Write-Host ""

# Test 9: Clean up test data
Write-Host "9. CLEANUP TEST DATA" -ForegroundColor Yellow
Write-Host "====================" -ForegroundColor Yellow

php artisan tinker --execute="
tenancy()->initialize('$tenant');
`$user = App\Models\User::where('email', 'developer@example.com')->first();
if (`$user) {
    `$user->forceDelete();
    echo 'Deleted test user: developer@example.com' . PHP_EOL;
}
"

Write-Host ""
Write-Host "=== All tests completed ===" -ForegroundColor Green