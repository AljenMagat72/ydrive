<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->uuid('uuid')
                ->default(DB::raw('gen_random_uuid()'))
                ->unique();
        });

        Schema::table('driver_schedules', function (Blueprint $table) {
            $table->uuid('uuid')
                ->default(DB::raw('gen_random_uuid()'))
                ->unique();
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->uuid('uuid')
                ->default(DB::raw('gen_random_uuid()'))
                ->unique();
        });
    }
};
