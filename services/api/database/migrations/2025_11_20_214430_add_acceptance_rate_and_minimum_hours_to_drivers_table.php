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
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'weekly_acceptance_rate')) {
                $table->float('weekly_acceptance_rate')->nullable();
            }

            if (!Schema::hasColumn('drivers', 'minimum_scheduled_hours')) {
                $table->float('minimum_scheduled_hours')->nullable();
            }

            if (!Schema::hasColumn('drivers', 'minimum_acceptance_rate')) {
                $table->float('minimum_acceptance_rate')->nullable();
            }

            if (Schema::hasColumn('drivers', 'prevent_delinquency')) {
                $table->dropColumn('prevent_delinquency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn(['weekly_acceptance_rate', 'minimum_scheduled_hours', 'minimum_acceptance_rate']);
            $table->boolean('prevent_delinquency')->default(false);
        });
    }
};
