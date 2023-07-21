<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('productos', function (Blueprint $table) {
      $table->foreign('marcas_id')->references('id')->on('marcas')->onDelete('CASCADE')->onUpdate('CASCADE');
      $table->foreign('colores_id')->references('id')->on('colores')->onDelete('CASCADE')->onUpdate('CASCADE');
      $table->foreign('talles_id')->references('id')->on('talles')->onDelete('CASCADE')->onUpdate('CASCADE');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('productos', function (Blueprint $table) {
      $table->dropForeign(['marcas_id']);
      $table->dropForeign(['colores_id']);
      $table->dropForeign(['talles_id']);
    });
  }
};
