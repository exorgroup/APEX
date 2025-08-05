<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Autentica Authentication System
 * Description: Migration to create Au10_login_attempts table for tracking login attempts.
 * URL: database/migrations/tenant/2025_01_01_000007_create_au10_login_attempts_table.php
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
        Schema::create('Au10_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->index();
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->boolean('successful')->default(false)->index();
            $table->timestamp('attempted_at')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->string('signature', 128);

            // Composite indexes for performance
            $table->index(['email', 'successful', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
            $table->index(['email', 'attempted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Au10_login_attempts');
    }
};
