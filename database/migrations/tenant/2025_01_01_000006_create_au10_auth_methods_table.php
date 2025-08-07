<?php

/**
 * Copyright EXOR Group Ltd 2025
 * Version 1.0.0.0
 * APEX Pro Laravel Autentica Authentication System
 * Description: Migration for Au10_auth_methods table - stores authentication method configurations for users with flexible JSON config storage
 * File Location: apex/autentica/database/migrations/tenant/2025_01_01_000006_create_au10_auth_methods_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the Au10_auth_methods table for storing authentication method configurations.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            Schema::create('au10_auth_methods', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('method', 50)->comment('Authentication method name (password, totp, social, etc.)');
                $table->boolean('enabled')->default(true)->comment('Whether this auth method is enabled for the user');
                $table->json('config')->nullable()->comment('Method-specific configuration data');
                $table->timestamp('last_used_at')->nullable()->comment('Last time this auth method was used');
                $table->timestamps();
                $table->softDeletes();
                $table->string('signature', 128)->comment('SHA512 signature for data integrity');

                // Indexes
                $table->index('user_id');
                $table->index('method');
                $table->index('enabled');
                $table->index(['user_id', 'method']);
                $table->index(['user_id', 'enabled']);
                $table->index('last_used_at');
                $table->index('deleted_at');

                // Unique constraint for user_id + method
                $table->unique(['user_id', 'method', 'deleted_at']);

                // Foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            Log::info('Migration: au10_auth_methods table created successfully', [
                'file' => '2025_01_01_000006_create_au10_auth_methods_table.php',
                'method' => 'up'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000006_create_au10_auth_methods_table.php - up() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000006_create_au10_auth_methods_table.php',
                'method' => 'up',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Drops the Au10_auth_methods table.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            Schema::dropIfExists('au10_auth_methods');

            Log::info('Migration: au10_auth_methods table dropped successfully', [
                'file' => '2025_01_01_000006_create_au10_auth_methods_table.php',
                'method' => 'down'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000006_create_au10_auth_methods_table.php - down() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000006_create_au10_auth_methods_table.php',
                'method' => 'down',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
};
