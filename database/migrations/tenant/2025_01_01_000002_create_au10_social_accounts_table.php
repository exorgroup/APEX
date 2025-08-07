<?php

/**
 * Copyright EXOR Group Ltd 2025
 * Version 1.0.0.0
 * APEX Pro Laravel Autentica Authentication System
 * Description: Migration for Au10_social_accounts table - stores social authentication provider accounts (Google, Microsoft, etc.) with encrypted tokens
 * File Location: apex/autentica/database/migrations/tenant/2025_01_01_000002_create_au10_social_accounts_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the Au10_social_accounts table for storing social authentication provider accounts.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            Schema::create('au10_social_accounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('provider', 50)->comment('Social provider name (google, microsoft, etc.)');
                $table->string('provider_user_id', 255)->comment('User ID from the social provider');
                $table->text('access_token')->nullable()->comment('Encrypted access token from provider');
                $table->text('refresh_token')->nullable()->comment('Encrypted refresh token from provider');
                $table->timestamp('expires_at')->nullable()->comment('Token expiration timestamp');
                $table->timestamps();
                $table->softDeletes();
                $table->string('signature', 128)->comment('SHA512 signature for data integrity');

                // Indexes
                $table->index('user_id');
                $table->index('provider');
                $table->index(['provider', 'provider_user_id']);
                $table->index(['user_id', 'provider']);
                $table->index('deleted_at');
                $table->index('expires_at');

                // Unique constraint for provider + provider_user_id
                $table->unique(['provider', 'provider_user_id', 'deleted_at']);

                // Foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            Log::info('Migration: au10_social_accounts table created successfully', [
                'file' => '2025_01_01_000002_create_au10_social_accounts_table.php',
                'method' => 'up'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000002_create_au10_social_accounts_table.php - up() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000002_create_au10_social_accounts_table.php',
                'method' => 'up',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Drops the Au10_social_accounts table.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            Schema::dropIfExists('au10_social_accounts');

            Log::info('Migration: au10_social_accounts table dropped successfully', [
                'file' => '2025_01_01_000002_create_au10_social_accounts_table.php',
                'method' => 'down'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000002_create_au10_social_accounts_table.php - down() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000002_create_au10_social_accounts_table.php',
                'method' => 'down',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
};
