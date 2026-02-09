<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vendor_lists')) {
            Schema::create('vendor_lists', function (Blueprint $table) {
                $table->id();
                $table->string('vendor_id')->unique();
                $table->string('no_opps_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_list');
    }
};
