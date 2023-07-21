<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('talles', function(Blueprint $table) {
      $table->foreign('talle_tipos_id')->references('id')->on('talle_tipos');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('talles', function(Blueprint $table) {
      $table->dropForeign(['talle_tipos_id']);
    });
  }
};
