<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_trabajo_id')->constrained('ordenes_trabajo')->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->string('payment_method')->default('card');
            $table->decimal('monto', 10, 2);
            $table->string('estado')->default('completado'); // completado, fallido, reembolsado
            $table->string('moneda', 3)->default('mxn');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};