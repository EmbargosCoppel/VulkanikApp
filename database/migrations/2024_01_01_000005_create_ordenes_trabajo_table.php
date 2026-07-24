<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained()->onDelete('cascade');
            $table->foreignId('mecanico_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['diagnóstico', 'esperando_piezas', 'reparación', 'finalizado'])->default('diagnóstico');
            $table->text('diagnostico')->nullable();
            $table->text('trabajos_realizados')->nullable();
            $table->decimal('mano_obra', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->dateTime('fecha_entrada');
            $table->dateTime('fecha_salida')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};
