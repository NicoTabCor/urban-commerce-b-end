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
      $table->renameColumn('talle_tipo_id', 'talle_tipos_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('talles', function(Blueprint $table) {
      $table->renameColumn('talle_tipos_id', 'talle_tipo_id');
    });
  }
};
