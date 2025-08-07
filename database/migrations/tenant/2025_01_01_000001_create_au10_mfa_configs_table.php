<?php

/**
 * Copyright EXOR Group Ltd 2025
 * Version 1.0.0.0
 * APEX Pro Laravel Autentica Authentication System
 * Description: Migration for Au10_mfa_configs table - stores multi-factor authentication configurations for users including TOTP, SMS, and email methods
 * File Location: apex/autentica/database/migrations/tenant/2025_01_01_000001_create_au10_mfa_configs_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the Au10_mfa_configs table for storing multi-factor authentication configurations.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            Schema::create('au10_mfa_configs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->enum('method', ['totp', 'sms', 'email'])->comment('MFA method type');
                $table->text('secret')->nullable()->comment('Encrypted secret for TOTP or other auth methods');
                $table->string('phone', 50)->nullable()->comment('Phone number for SMS authentication');
                $table->timestamp('verified_at')->nullable()->comment('Timestamp when MFA was verified');
                $table->timestamps();
                $table->softDeletes();
                $table->string('signature', 128)->comment('SHA512 signature for data integrity');

                // Indexes
                $table->index('user_id');
                $table->index('method');
                $table->index(['user_id', 'method']);
                $table->index('deleted_at');

                // Foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            Log::info('Migration: au10_mfa_configs table created successfully', [
                'file' => '2025_01_01_000001_create_au10_mfa_configs_table.php',
                'method' => 'up'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000001_create_au10_mfa_configs_table.php - up() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000001_create_au10_mfa_configs_table.php',
                'method' => 'up',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Drops the Au10_mfa_configs table.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            Schema::dropIfExists('au10_mfa_configs');

            Log::info('Migration: au10_mfa_configs table dropped successfully', [
                'file' => '2025_01_01_000001_create_au10_mfa_configs_table.php',
                'method' => 'down'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000001_create_au10_mfa_configs_table.php - down() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000001_create_au10_mfa_configs_table.php',
                'method' => 'down',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
};
