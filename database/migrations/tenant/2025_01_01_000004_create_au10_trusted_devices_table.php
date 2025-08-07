<?php

/**
 * Copyright EXOR Group Ltd 2025
 * Version 1.0.0.0
 * APEX Pro Laravel Autentica Authentication System
 * Description: Migration for Au10_trusted_devices table - stores trusted device information for enhanced security and device management
 * File Location: apex/autentica/database/migrations/tenant/2025_01_01_000004_create_au10_trusted_devices_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the Au10_trusted_devices table for storing trusted device information.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            Schema::create('au10_trusted_devices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('device_id', 255)->comment('Unique device identifier');
                $table->string('device_name', 255)->nullable()->comment('User-friendly device name');
                $table->string('browser', 100)->nullable()->comment('Browser information');
                $table->string('platform', 100)->nullable()->comment('Operating system/platform');
                $table->string('ip_address', 45)->comment('IP address (IPv4/IPv6 compatible)');
                $table->timestamp('last_used_at')->comment('Last time device was used');
                $table->timestamps();
                $table->softDeletes();
                $table->string('signature', 128)->comment('SHA512 signature for data integrity');

                // Indexes
                $table->index('user_id');
                $table->index('device_id');
                $table->index(['user_id', 'device_id']);
                $table->index('ip_address');
                $table->index('last_used_at');
                $table->index('deleted_at');

                // Unique constraint for user_id + device_id
                $table->unique(['user_id', 'device_id', 'deleted_at']);

                // Foreign key constraint
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });

            Log::info('Migration: au10_trusted_devices table created successfully', [
                'file' => '2025_01_01_000004_create_au10_trusted_devices_table.php',
                'method' => 'up'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000004_create_au10_trusted_devices_table.php - up() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000004_create_au10_trusted_devices_table.php',
                'method' => 'up',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     * Drops the Au10_trusted_devices table.
     *
     * @return void
     */
    public function down(): void
    {
        try {
            Schema::dropIfExists('au10_trusted_devices');

            Log::info('Migration: au10_trusted_devices table dropped successfully', [
                'file' => '2025_01_01_000004_create_au10_trusted_devices_table.php',
                'method' => 'down'
            ]);
        } catch (\Exception $e) {
            Log::error('Migration error in 2025_01_01_000004_create_au10_trusted_devices_table.php - down() method: ' . $e->getMessage(), [
                'file' => '2025_01_01_000004_create_au10_trusted_devices_table.php',
                'method' => 'down',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
};
