<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refacciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('sku')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('costo', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('ubicacion')->nullable();
            $table->string('proveedor')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refacciones');
    }
};
