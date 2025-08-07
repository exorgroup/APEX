<?php

/**
 * Copyright EXOR Group Ltd 2025
 * Version 1.0.0.0
 * APEX Pro Laravel Autentica Authentication System
 * Description: Migration for Au10_mfa_backup_codes table - stores backup codes for multi-factor authentication recovery
 * File Location: apex/autentica/database/migrations/tenant/2025_01_01_000003_create_au10_mfa_backup_codes_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the Au10_mfa_backup_codes table for storing MFA backup recovery codes.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            Schema::create('au10_mfa_backup_codes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('code', 255)->comment('Backup code for MFA recovery');
                $table->timestamp('used_at')->nullable()->comment('Timestamp when backup code was used');
                $table->timestamps();
                $table->softDeletes();
                $table->string('signature', 128)->comment('SHA512 signature for data integrity');

                // Indexes
                $table->index('user_id');
                $table->index('code');
                $table->index('used_at');
                $table->index(['user_id', 'used_at']);
                $table->index('deleted_at');

                // Unique constraint for code
                $table->unique(['code', 'deleted_at']);

                // Foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            Log::info('Migration: au10_mfa_backup_codes table created successfully', [
                'file' => '2025_01_01_000003_create_au10_mfa_backup_codes_table.php',
                'method' => 'up'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000003_create_au10_mfa_backup_codes_table.php - up() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000003_create_au10_mfa_backup_codes_table.php',
                'method' => 'up',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Drops the Au10_mfa_backup_codes table.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            Schema::dropIfExists('au10_mfa_backup_codes');

            Log::info('Migration: au10_mfa_backup_codes table dropped successfully', [
                'file' => '2025_01_01_000003_create_au10_mfa_backup_codes_table.php',
                'method' => 'down'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000003_create_au10_mfa_backup_codes_table.php - down() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000003_create_au10_mfa_backup_codes_table.php',
                'method' => 'down',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
};
