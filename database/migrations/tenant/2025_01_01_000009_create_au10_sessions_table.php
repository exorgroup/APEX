<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Autentica Authentication System
 * Description: Migration to create Au10_sessions table for managing user sessions.
 * URL: database/migrations/tenant/2025_01_01_000009_create_au10_sessions_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Au10_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('session_id', 255)->unique();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('device_id', 255)->nullable()->index();
            $table->json('location')->nullable();
            $table->timestamp('last_activity')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->string('signature', 128);

            // Indexes for performance
            $table->index(['user_id', 'last_activity']);
            $table->index(['session_id', 'deleted_at']);

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Au10_sessions');
    }
};
