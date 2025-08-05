<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Autentica Authentication System
 * Description: Migration to create Au10_system_resources table for managing protected resources.
 * URL: database/migrations/tenant/2025_01_01_000003_create_au10_system_resources_table.php
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
        Schema::create('Au10_system_resources', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('name', 100);
            $table->string('identifier', 255)->unique();
            $table->enum('type', ['model', 'function', 'module']);
            $table->text('description')->nullable();
            $table->integer('menu_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->string('signature', 128);

            // Indexes for performance
            $table->index(['type', 'deleted_at']);
            $table->index(['parent_id', 'menu_order']);
            $table->index('identifier');

            // Foreign key for parent relationship
            $table->foreign('parent_id')->references('id')->on('Au10_system_resources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Au10_system_resources');
    }
};
