<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('productos', function (Blueprint $table) {
      $table->id();
      $table->string('nombre', 100);
      $table->float('precio', 8, 2);
      $table->float('descuento', 4, 2);
      $table->string('imagen', 255);
      $table->unsignedBigInteger('edades_id');
      $table->unsignedBigInteger('generos_id');
      $table->unsignedBigInteger('categorias_id');
      $table->unsignedBigInteger('marcas_id');
      $table->unsignedBigInteger('colores_id');
      $table->unsignedBigInteger('talles_id');
      $table->longText('descripcion');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('productos');
  }
};
