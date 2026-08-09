<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedTinyInteger('accepted_offers')
                ->default(0)
                ->after('minimum_scheduled_hours');

            $table->unsignedTinyInteger('expired_offers')
                ->default(0)
                ->after('accepted_offers');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['accepted_offers', 'expired_offers']);
        });
    }
};
