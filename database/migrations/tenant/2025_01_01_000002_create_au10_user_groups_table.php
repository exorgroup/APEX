<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Autentica Authentication System
 * Description: Migration to create Au10_user_groups pivot table for user-group relationships.
 * URL: database/migrations/tenant/2025_01_01_000002_create_au10_user_groups_table.php
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
        Schema::create('Au10_user_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('group_id')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->string('signature', 128);

            // Composite unique index to prevent duplicate memberships
            $table->unique(['user_id', 'group_id', 'deleted_at'], 'au10_user_group_unique');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('Au10_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Au10_user_groups');
    }
};
