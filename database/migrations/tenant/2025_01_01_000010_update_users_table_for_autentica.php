<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Autentica Authentication System
 * Description: Migration to update the users table with Autentica required fields.
 * URL: database/migrations/tenant/2025_01_01_000010_update_users_table_for_autentica.php
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
        Schema::table('users', function (Blueprint $table) {
            // Add soft deletes if not already present
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }

            // Add signature column if not already present
            if (!Schema::hasColumn('users', 'signature')) {
                $table->string('signature', 128)->nullable();
            }

            // Get existing indexes
            $existingIndexes = collect(Schema::getIndexes('users'))->pluck('name')->toArray();

            // Add indexes for performance if they don't exist
            if (!in_array('users_email_deleted_at_index', $existingIndexes)) {
                $table->index(['email', 'deleted_at'], 'users_email_deleted_at_index');
            }

            if (!in_array('users_created_at_index', $existingIndexes)) {
                $table->index('created_at', 'users_created_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Get existing indexes
            $existingIndexes = collect(Schema::getIndexes('users'))->pluck('name')->toArray();

            // Remove indexes if they exist
            if (in_array('users_email_deleted_at_index', $existingIndexes)) {
                $table->dropIndex('users_email_deleted_at_index');
            }

            if (in_array('users_created_at_index', $existingIndexes)) {
                $table->dropIndex('users_created_at_index');
            }

            // Remove columns if they exist
            if (Schema::hasColumn('users', 'signature')) {
                $table->dropColumn('signature');
            }

            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
