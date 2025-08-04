<?php

/**
 * Copyright EXOR Group ltd 2025
 * Version 1.0.0.0
 * APEX Laravel Hermes Messaging
 * Description: Migration to create the hermes_api_keys table for storing API credentials
 * 
 * File location: apex/hermes/database/migrations/2025_01_01_000001_create_hermes_api_keys_table.php
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHermesApiKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('hermes')->create('hermes_api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key', 64)->unique();
            $table->text('secret')->comment('Encrypted secret - needs text for encrypted data');
            $table->enum('provider', ['cm', 'messente']);
            $table->text('provider_api_key')->comment('Encrypted API key for the provider');
            $table->text('provider_api_secret')->nullable()->comment('Encrypted API secret for the provider if required');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->string('signature', 128);

            // Indexes
            $table->index('provider');
            $table->index('active');
            $table->index(['provider', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('hermes')->dropIfExists('hermes_api_keys');
    }
}
