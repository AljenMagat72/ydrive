<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedInteger('acceptance_rate')->default(0);
            $table->decimal('minimum_scheduled_hours', 5, 2)->default(0)->after('acceptance_rate');
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['acceptance_rate', 'minimum_scheduled_hours']);
        });
    }
};
