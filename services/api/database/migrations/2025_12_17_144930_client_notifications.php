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
    Schema::create('client_notifications', function (Blueprint $table) {
      $table->id();
      $table->uuid('client_id');
      $table->uuid('ride_id');
      $table->uuid('driver_id')->nullable();
      $table->string('notification_type', 50);
      $table->timestamps();

      $table->index(
        ['client_id', 'notification_type', 'ride_id', 'driver_id'],
        'client_notifications_idx'
      );
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
