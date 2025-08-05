<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Autentica Authentication System
 * Description: Migration to create Au10_permissions table for managing access rights.
 * URL: database/migrations/tenant/2025_01_01_000004_create_au10_permissions_table.php
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
        Schema::create('Au10_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('permissionable_type', 255);
            $table->unsignedBigInteger('permissionable_id');
            $table->unsignedBigInteger('system_resource_id');
            $table->boolean('can_create')->default(false);
            $table->boolean('can_read')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->boolean('can_print')->default(false);
            $table->boolean('can_history')->default(false);
            $table->text('custom_permissions')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('signature', 128);

            // Indexes for performance
            $table->index(['permissionable_type', 'permissionable_id'], 'au10_permissionable_index');
            $table->index('system_resource_id');
            $table->unique(['permissionable_type', 'permissionable_id', 'system_resource_id', 'deleted_at'], 'au10_unique_permission');

            // Foreign key constraint
            $table->foreign('system_resource_id')->references('id')->on('Au10_system_resources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Au10_permissions');
    }
};
