<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('drivers', function (Blueprint $table) {
      $table->unsignedBigInteger('vendor_list_id')->nullable()->after('id');

      $table->dropColumn('original_vendor_id');

      $table->foreign('vendor_list_id')
        ->references('id')
        ->on('vendor_lists')
        ->onDelete('set null');
    });
  }

  public function down(): void
  {
    Schema::table('drivers', function (Blueprint $table) {
      $table->dropForeign(['vendor_list_id']);
      $table->dropColumn('vendor_list_id');

      $table->string('original_vendor_id')->nullable();
    });
  }
};
