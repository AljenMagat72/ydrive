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
    Schema::table('driver_schedules', function (Blueprint $table) {
      $table->dropColumn('date');
      $table->dropColumn('starts_at');
      $table->dropColumn('ends_at');

      $table->datetime('starts_at')->default(now());
      $table->datetime('ends_at')->default(now());
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    //
  }
};
