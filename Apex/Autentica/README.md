# Autentica - Enterprise Authentication & Authorization for Laravel

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)](LICENSE)

Autentica is a comprehensive authentication and authorization system for Laravel applications, part of the APEX Framework. It provides enterprise-grade security features while maintaining developer-friendly APIs.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Usage](#usage)
  - [Authentication](#authentication)
  - [Authorization](#authorization)
  - [Groups](#groups)
  - [Security Features](#security-features)
- [Testing](#testing)
- [Configuration](#configuration)
- [Multi-tenancy](#multi-tenancy)
- [License](#license)

## Features

### Core Features (Free)
- ✅ **User Authentication** - Secure login/logout with session management
- ✅ **Permission System** - Granular CRUD permissions + custom permissions
- ✅ **User Groups** - Single-level group management
- ✅ **Password Policies** - Configurable password requirements
- ✅ **Account Security** - Login attempt tracking and account lockout
- ✅ **Audit Trail** - SHA512 signatures on all records

### Pro Features
- 🔐 **Two-Factor Authentication** - TOTP, SMS, Email
- 🌐 **Social Authentication** - Google, Microsoft, OAuth2
- 📱 **Device Management** - Trusted device registration
- 🎨 **UI Widgets** - Pre-built authentication components
- 🔄 **Advanced Sessions** - Concurrent session management

### Enterprise Features
- 🏢 **Single Sign-On** - SAML 2.0, LDAP integration
- 🌍 **Geo-fencing** - Location-based access control
- 🔒 **IP Restrictions** - Network-based security
- 📊 **Compliance Reports** - SOC2, GDPR ready
- 🚨 **Risk-Based Auth** - Adaptive authentication

## Requirements

- PHP 8.2 or higher
- Laravel 11.x
- MySQL 8.0+ or PostgreSQL 13+
- Composer 2.x
- (Optional) Redis for caching

## Installation

1. **Add to your Laravel project:**

```bash
# Ensure Autentica is in your composer.json autoload
"autoload": {
    "psr-4": {
        "Apex\\Autentica\\": "apex/autentica/src/"
    }
}

# Update autoloader
composer dump-autoload
```

2. **Register the service provider:**

In `config/app.php`:
```php
'providers' => [
    // ...
    Apex\Autentica\AutenticaServiceProvider::class,
],
```

3. **Update your User model:**

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Apex\Autentica\Core\Composables\Authenticatable as AutenticaAuthenticatable;

class User extends Authenticatable
{
    use AutenticaAuthenticatable;
    // ... rest of your model
}
```

4. **Run migrations:**

```bash
# For multi-tenant applications
php artisan tenants:migrate

# For single-tenant applications
php artisan migrate
```

## Quick Start

```php
use Apex\Autentica\Core\Services\AuthenticationService;
use Apex\Autentica\Core\Services\AuthorizationService;

// Authenticate a user
$auth = new AuthenticationService();
$result = $auth->attempt([
    'email' => 'user@example.com',
    'password' => 'password123'
]);

if ($result['success']) {
    $user = $result['user'];
    
    // Check permissions
    if ($user->hasPermission('posts', 'create')) {
        // User can create posts
    }
}
```

## Usage

### Authentication

**Login:**
```php
$auth = new AuthenticationService();
$result = $auth->attempt([
    'email' => 'user@example.com',
    'password' => 'password123'
], $remember = true);

if (!$result['success']) {
    // Handle failed login
    echo $result['message'];
    echo "Remaining attempts: " . $result['remaining_attempts'];
}
```

**Change Password:**
```php
$result = $auth->changePassword($user, $currentPassword, $newPassword);
```

### Authorization

**Grant Permissions:**
```php
$authz = new AuthorizationService();

// Grant to user
$authz->grant($user, 'posts', ['create', 'read', 'update']);

// Grant to group
$authz->grant($group, 'reports', ['read', 'print']);
```

**Check Permissions:**
```php
// Single permission
if ($user->hasPermission('posts', 'create')) {
    // Can create posts
}

// Multiple permissions (ANY)
if ($user->hasAnyPermission('posts', ['update', 'delete'])) {
    // Can update OR delete
}

// Multiple permissions (ALL)
if ($user->hasAllPermissions('posts', ['read', 'update'])) {
    // Can read AND update
}
```

### Groups

**Manage Groups:**
```php
// Create group
$group = Group::create([
    'name' => 'Editors',
    'description' => 'Content editors'
]);

// Add user to group
$user->joinGroup('Editors');

// Check membership
if ($user->belongsToGroup('Editors')) {
    // User is an editor
}

// Get user's groups
$groups = $user->getGroupNames(); // ['Editors', 'Writers']
```

### Security Features

**Account Security:**
```php
// Check if account is locked
if ($user->isAccountLocked()) {
    $minutes = $user->getUnlockTime();
    echo "Account locked for {$minutes} minutes";
}

// Get failed login attempts
$failedAttempts = $user->getFailedLoginCount(15); // Last 15 minutes

// View security events
$events = $user->getRecentSecurityEvents(10);
```

## Testing

Run the comprehensive test suite:

```bash
# Test all features
php artisan autentica:test tenant-name

# Test with cleanup
php artisan autentica:test tenant-name --cleanup
```

For PowerShell testing scripts:
```powershell
.\test-autentica-features.ps1 -tenant foo
```

## Configuration

Publish configuration files:

```bash
php artisan vendor:publish --tag=autentica-config
```

### Password Policies

Configure in `config/autentica/auth.php`:
```php
'password_policies' => [
    'min_length' => 8,
    'require_uppercase' => true,
    'require_lowercase' => true,
    'require_numbers' => true,
    'require_special_chars' => false,
    'password_history_count' => 5,
    'password_expiry_days' => 90,
],
```

### Security Settings

```php
'security' => [
    'max_login_attempts' => 5,
    'lockout_duration' => 15, // minutes
    'remember_me_duration' => 30, // days
    'session_lifetime' => 120, // minutes
],
```

## Multi-tenancy

Autentica is built for [Tenancy for Laravel](https://tenancyforlaravel.com/):

- All tables are tenant-scoped
- Migrations go in `database/migrations/tenant/`
- Each tenant has isolated authentication data
- Supports tenant-specific configuration

## Directory Structure

```
apex/autentica/
├── config/           # Configuration files
├── resources/
│   └── lang/        # Multi-language support
├── src/
│   ├── Core/        # Core features
│   ├── Pro/         # Pro features
│   ├── Enterprise/  # Enterprise features
│   └── AutenticaServiceProvider.php
└── database/
    └── tenant/
        └── migrations/
```

## Troubleshooting

**Class not found errors:**
```bash
composer dump-autoload
```

**Cache issues:**
```bash
php artisan cache:clear
php artisan config:clear
```

**Permission cache not updating:**
```php
$user->clearPermissionCache();
```

## Contributing

This is proprietary software. For bug reports or feature requests, please contact EXOR Group Ltd.

## License

Copyright © 2025 EXOR Group Ltd. All rights reserved.

This is proprietary software. Unauthorized copying, modification, distribution, or use of this software, via any medium, is strictly prohibited.

## Support

For support, please contact:
- Email: duncan.dimech@exorgroup.com