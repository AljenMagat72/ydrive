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
    Schema::create('ride_price_snapshots', function (Blueprint $table) {
      $table->id();

      // Autofleet IDs
      $table->uuid('ride_id')->unique();
      $table->uuid('driver_id')->nullable()->index();
      $table->uuid('price_calculation_id')->index();

      $table->uuid('business_model_id')->nullable()->index();
      $table->uuid('pricing_policy_id')->nullable();

      // Price info
      $table->string('currency', 10)->nullable();
      $table->string('calculation_reason')->nullable();

      $table->decimal('base_price', 12, 4)->default(0);
      $table->decimal('surge_price', 12, 4)->default(0);
      $table->decimal('total_price', 12, 4)->default(0);

      $table->decimal('total_driver_earnings', 12, 4)->default(0);

      $table->json('items')->nullable();

      $table->string('payout_status')->default('to_be_settled')->index();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('ride_price_snapshots');
  }
};
