<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void {
    Schema::table('generos', function (Blueprint $table) {
      $table->foreign('edades_id')->references('id')->on('edades')->onDelete('CASCADE')->onUpdate('CASCADE');
    });

    Schema::table('categorias', function (Blueprint $table) {
      $table->foreign('generos_id')->references('id')->on('generos')->onDelete('CASCADE')->onUpdate('CASCADE');

      $table->foreign('talle_tipos_id')->references('id')->on('talle_tipos')->onDelete('CASCADE')->onUpdate('CASCADE');
    });

    Schema::table('marcas', function (Blueprint $table) {
      $table->foreign('categorias_id')->references('id')->on('categorias')->onDelete('CASCADE')->onUpdate('CASCADE');
    });

    Schema::table('pedidos', function (Blueprint $table) {
      $table->foreign('users_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
    });

    Schema::table('productos_pedidos', function (Blueprint $table) {
      $table->foreign('productos_id')->references('id')->on('productos')->onDelete('CASCADE')->onUpdate('CASCADE');

      $table->foreign('pedidos_id')->references('id')->on('pedidos')->onDelete('CASCADE')->onUpdate('CASCADE');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::table('generos', function (Blueprint $table) {
      $table->dropForeign(['edades_id']);
    });

    Schema::table('categorias', function (Blueprint $table) {
      $table->dropForeign(['generos_id']);
    });

    Schema::table('marcas', function (Blueprint $table) {
      $table->dropForeign(['categorias_id']);
    });

    Schema::table('talle_tipos', function (Blueprint $table) {
      $table->dropForeign(['categorias_id']);
    });

    Schema::table('pedidos', function (Blueprint $table) {
      $table->dropForeign(['users_id']);
    });

    Schema::table('productos_pedidos', function (Blueprint $table) {
      $table->dropForeign(['productos_id']);
      $table->dropForeign(['pedidos_id']);
    });
  }
};
