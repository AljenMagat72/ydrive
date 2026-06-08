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
    Schema::create('ride_additional_charges', function (Blueprint $table) {
      $table->id();

      $table->uuid('autofleet_charge_id')->index();
      $table->uuid('price_calculation_id')->index();
      $table->uuid('business_model_id')->nullable()->index();

      $table->decimal('amount', 12, 4);
      $table->string('charge_for');
      $table->string('description')->nullable();

      $table->timestamp('autofleet_created_at')->nullable();
      $table->timestamp('autofleet_updated_at')->nullable();
      $table->timestamp('autofleet_deleted_at')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('ride_additional_charges');
  }
};
