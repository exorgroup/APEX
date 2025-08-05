# Hermes - APEX Laravel Messaging Service

A powerful, provider-agnostic messaging service for Laravel applications that acts as a unified API gateway for SMS and messaging providers. Hermes provides a clean, consistent interface for sending messages while handling authentication, validation, and provider-specific quirks.

## Features

- 🚀 **Provider Agnostic** - Currently supports CM Telecom with easy extensibility for additional providers
- 🔐 **Secure API Authentication** - API key-based authentication with optional secret validation
- 📱 **SMS Capabilities** - Send single or bulk SMS messages with delivery tracking
- 📊 **Multi-part Message Control** - Automatic validation and control over long message handling
- 🌍 **Multi-tenant Ready** - Designed to work with Laravel's multi-tenancy packages
- 💾 **Separate Database** - Uses its own database for complete isolation
- 📝 **Comprehensive Logging** - Built-in audit trail and error logging
- 🔄 **Provider Failover** - Architecture supports automatic failover between providers

## Requirements

- PHP 8.0+
- Laravel 9.0+
- MySQL 5.7+ or compatible
- Composer
- CM Telecom account (for SMS functionality)

## Installation

### 1. Database Setup

Create a dedicated database for Hermes:

```sql
CREATE DATABASE `apex-hermes` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Environment Configuration

Add the following to your `.env` file:

```dotenv
# Hermes Database Connection
HERMES_DB_CONNECTION=hermes
HERMES_DB_HOST=127.0.0.1
HERMES_DB_PORT=3306
HERMES_DB_DATABASE=apex-hermes
HERMES_DB_USERNAME=root
HERMES_DB_PASSWORD=your_password

# Hermes Configuration
HERMES_DEFAULT_PROVIDER=cm
HERMES_ENCRYPT_KEYS=true
HERMES_CM_MARKUP=15
HERMES_RATE_LIMIT_ENABLED=true
HERMES_RATE_LIMIT_PER_MINUTE=60
```

### 3. Database Configuration

Add the Hermes connection to `config/database.php`:

```php
'connections' => [
    // ... existing connections ...
    
    'hermes' => [
        'driver' => 'mysql',
        'host' => env('HERMES_DB_HOST', '127.0.0.1'),
        'port' => env('HERMES_DB_PORT', '3306'),
        'database' => env('HERMES_DB_DATABASE', 'apex-hermes'),
        'username' => env('HERMES_DB_USERNAME', 'root'),
        'password' => env('HERMES_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
    ],
],
```

### 4. Composer Configuration

Update your `composer.json` to include the Apex namespace:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Apex\\Hermes\\": "apex/hermes/src/"
    }
}
```

Then run:

```bash
composer dump-autoload
```

### 5. Service Provider Registration

Add to `config/app.php` providers array:

```php
'providers' => [
    // ...
    Apex\Hermes\HermesServiceProvider::class,
],
```

### 6. Install Dependencies

```bash
composer require cmdotcom/text-sdk-php
```

### 7. Run Migrations

```bash
php artisan migrate --database=hermes --path=apex/hermes/database/migrations
```

### 8. Generate API Keys

Generate your first API key:

```bash
php artisan hermes:generate-api-key cm --api-key=YOUR_CM_API_KEY
```

This will output:

```
=== HERMES API CREDENTIALS ===
API Key: xxxxxxxxxxxxxxxxxxxx...
API Secret: xxxxxxxxxxxxxxxxxxxx...
```

> [!IMPORTANT]
> Save these credentials securely! The API secret cannot be retrieved later.

## Usage

### Basic SMS Sending

```php
use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'X-API-Key' => 'your-hermes-api-key',
    'X-API-Secret' => 'your-hermes-api-secret',
    'Accept' => 'application/json'
])->post('https://your-domain.com/api/v1/sms/send', [
    'message_text' => 'Hello World!',
    'sender' => 'MyApp',
    'recipient_phone_number' => '+35699999999',
    'reference' => 'order-123',
    'allow_multi_part' => false
]);

if ($response->successful()) {
    $result = $response->json();
    echo "Messages sent: " . $result['result']['accepted_count'];
}
```

### Sending to Multiple Recipients

```php
$response = Http::withHeaders([
    'X-API-Key' => 'your-hermes-api-key',
    'X-API-Secret' => 'your-hermes-api-secret'
])->post('https://your-domain.com/api/v1/sms/send', [
    'message_text' => 'Bulk notification message',
    'sender' => 'MyApp',
    'recipient_phone_number' => ['+35699999999', '+35688888888', '+35677777777'],
    'allow_multi_part' => true
]);
```

### Using in Laravel Application

<details>
<summary>Create a service class</summary>

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.hermes.api_key');
        $this->apiSecret = config('services.hermes.api_secret');
        $this->baseUrl = config('services.hermes.base_url');
    }

    public function send(string $message, string $to, string $from = null): array
    {
        $response = Http::withHeaders([
            'X-API-Key' => $this->apiKey,
            'X-API-Secret' => $this->apiSecret,
            'Accept' => 'application/json'
        ])->post($this->baseUrl . '/api/v1/sms/send', [
            'message_text' => $message,
            'sender' => $from ?? config('services.hermes.default_sender'),
            'recipient_phone_number' => $to,
            'allow_multi_part' => true
        ]);

        if (!$response->successful()) {
            throw new \Exception('SMS sending failed: ' . $response->body());
        }

        return $response->json();
    }
}
```

</details>

### Rich SMS (WhatsApp) - Future Feature

```php
$response = Http::withHeaders([
    'X-API-Key' => 'your-hermes-api-key',
    'X-API-Secret' => 'your-hermes-api-secret'
])->post('https://your-domain.com/api/v1/sms/send-rich', [
    'message_text' => 'Check out this image!',
    'sender' => 'MyApp',
    'recipient_phone_number' => '+35699999999',
    'channel' => 'WHATSAPP',
    'hybrid_app_key' => 'your-whatsapp-key',
    'media' => [
        'url' => 'https://example.com/image.jpg',
        'type' => 'image/jpeg',
        'caption' => 'Product image'
    ]
]);
```

## API Reference

### Send SMS

<kbd>POST</kbd> `/api/v1/sms/send`

**Headers:**
| Header | Type | Required | Description |
| --- | --- | --- | --- |
| `X-API-Key` | string | ✅ | Your Hermes API key |
| `X-API-Secret` | string | ⚪ | Your Hermes API secret (recommended) |
| `Content-Type` | string | ✅ | application/json |
| `Accept` | string | ✅ | application/json |

**Body Parameters:**
| Parameter | Type | Required | Description |
| --- | --- | --- | --- |
| `message_text` | string | ✅ | The message content |
| `sender` | string | ✅ | Sender ID (max 11 characters) |
| `recipient_phone_number` | string\|array | ✅ | Recipient(s) phone number(s) |
| `reference` | string | ⚪ | Your reference for tracking |
| `allow_multi_part` | boolean | ⚪ | Allow long messages (default: true) |

**Response:**
```json
{
    "success": true,
    "result": {
        "details": [...],
        "status_message": "Created 1 message(s)",
        "status_code": 201,
        "accepted_count": 1,
        "rejected_count": 0,
        "total_parts": 1
    },
    "status_code": 201,
    "status_message": "Created 1 message(s)"
}
```

### Get Message Status

<kbd>GET</kbd> `/api/v1/sms/status?reference=your-reference`

### Send Rich SMS

<kbd>POST</kbd> `/api/v1/sms/send-rich`

## Error Handling

The API returns appropriate HTTP status codes:

| Status Code | Description |
| --- | --- |
| `201` | Message created successfully |
| `400` | Bad request (validation errors, message too long) |
| `401` | Unauthorized (invalid API key) |
| `422` | Unprocessable entity (validation errors) |
| `500` | Internal server error |

<details>
<summary>Example error response</summary>

```json
{
    "success": false,
    "message": "Message requires multiple parts to be sent and it is not allowed to",
    "details": {
        "message_length": 180,
        "max_allowed": 160,
        "contains_unicode": false
    }
}
```

</details>

## Testing

Use the provided PowerShell test script:

```powershell
.\test-hello-world.ps1
```

Or use curl:

```bash
curl -X POST "http://localhost:8000/api/v1/sms/send" \
  -H "X-API-Key: your-api-key" \
  -H "X-API-Secret: your-api-secret" \
  -H "Content-Type: application/json" \
  -d '{
    "message_text": "Test message",
    "sender": "Test",
    "recipient_phone_number": "+35699999999",
    "allow_multi_part": false
  }'
```

## Security Features

- [x] **API Key Authentication** - All requests require valid API credentials
- [x] **Database Encryption** - API keys and secrets are encrypted in the database
- [x] **Request Validation** - Comprehensive input validation
- [x] **Rate Limiting** - Configurable rate limits per API key
- [x] **Audit Logging** - All API access and messages are logged
- [x] **Multi-tenant Isolation** - Complete separation of data

## Configuration Options

The following options can be configured in the provider configuration files:

- **Markup Percentage** - Add markup to provider costs
- **Rate Limiting** - Requests per minute/hour
- **Logging** - Enable/disable detailed logging
- **Encryption** - Toggle API key encryption
- **Provider Selection** - Default provider and failover rules

## Extending Hermes

### Adding New Providers

1. Create a new provider class implementing `MessageProvider` interface
2. Add provider configuration to `config/providers.php`
3. Update the `MessageRouter` to include the new provider

<details>
<summary>Example provider implementation</summary>

```php
namespace Apex\Hermes\Providers\Twilio;

use Apex\Hermes\Providers\Contracts\MessageProvider;

class TwilioProvider implements MessageProvider
{
    // Implement required methods
}
```

</details>

## Troubleshooting

### Common Issues

> [!NOTE]
> **"The HTTP status code '0' is not valid"**
> 
> This is normal for CM Telecom, the system handles it automatically

> [!WARNING]
> **"Base table or view not found"**
> - Ensure migrations have run on the correct database
> - Check database connection settings

> [!TIP]
> **"Invalid API key format"**
> - Hermes accepts both 64-character keys and UUID format
> - Ensure no extra spaces in the API key

### Debug Mode

Enable detailed logging in `.env`:

```dotenv
HERMES_LOGGING_ENABLED=true
HERMES_LOG_MESSAGE_CONTENT=true
```

Check logs at:
- `storage/logs/hermes/hermes-*.log`
- `storage/logs/laravel.log`

## License

This software is proprietary and confidential.

---

**Copyright © 2025 EXOR Group Ltd. All rights reserved.**

This software and associated documentation files (the "Software") are the property of EXOR Group Ltd. The Software is provided under license and may only be used or copied in accordance with the terms of the license agreement. No part of this Software may be reproduced, distributed, or transmitted in any form or by any means without the prior written permission of EXOR Group Ltd.

For licensing information, please contact: duncan.dimech@exorgroup.com