<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('productos', function(Blueprint $table) {
      $table->dropColumn('edades_id');
      $table->dropColumn('generos_id');
      $table->dropColumn('categorias_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('productos', function(Blueprint $table) {
      $table->unsignedBigInteger('edades_id')->after('imagen');
      $table->unsignedBigInteger('generos_id')->after('edades_id');
      $table->unsignedBigInteger('categorias_id')->after('generos_id');
    });
  }
};
