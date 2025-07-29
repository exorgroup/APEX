<?php

/*
Copyright EXOR Group ltd 2025 
Version 1.0.0.0 
APEX Laravel Auditing
Description: Migration for creating the apex_history table for user-facing audit trail display with rollback capabilities and clean field change tracking.
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apex_history', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Link to audit record
            $table->unsignedBigInteger('audit_id')->nullable()->index();

            // Model information
            $table->string('model_type')->index();
            $table->string('model_id')->index();
            $table->enum('action_type', ['create', 'update', 'delete', 'restore'])->index();

            // Change tracking
            $table->json('field_changes')->nullable();
            $table->text('description');

            // Rollback functionality
            $table->json('rollback_data')->nullable();
            $table->boolean('can_rollback')->default(false)->index();
            $table->timestamp('rolled_back_at')->nullable()->index();
            $table->unsignedBigInteger('rolled_back_by')->nullable();

            // User information
            $table->unsignedBigInteger('user_id')->nullable()->index();

            // Timestamps
            $table->timestamps();

            // Composite indexes for performance
            $table->index(['model_type', 'model_id', 'created_at'], 'apex_history_model_date_idx');
            $table->index(['user_id', 'created_at'], 'apex_history_user_date_idx');
            $table->index(['can_rollback', 'rolled_back_at'], 'apex_history_rollback_idx');
            $table->index(['action_type', 'created_at'], 'apex_history_action_date_idx');
            $table->index(['audit_id'], 'apex_history_audit_idx');

            // Foreign key constraints (commented out for flexibility)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('rolled_back_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apex_history');
    }
};
