<?php

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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
            $table->softDeletes();

            $table->uuid()->unique();

            $table->string('first_name');
            $table->string('last_name');

            $table->string('phone_number')->index();
            $table->string('email')->index();

            $table->string('avatar_url')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('device_type')->nullable();
            $table->timestamp('last_login_at')->nullable();

            $table->boolean('is_active');

            $table->string('autofleet_client_id')->nullable();
            $table->string('zoho_rider_id')->nullable();
            $table->string('chatwoot_contact_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
