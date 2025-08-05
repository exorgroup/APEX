<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Autentica Authentication System
 * Description: Migration to create Au10_groups table for managing user groups in the authentication system.
 * URL: database/migrations/tenant/2025_01_01_000001_create_au10_groups_table.php
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
        Schema::create('Au10_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('name', 100)->index();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('signature', 128);

            // Indexes for performance
            $table->index(['name', 'deleted_at']);
            $table->index('created_at');

            // Foreign key constraint will be added after all tables are created
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Au10_groups');
    }
};
