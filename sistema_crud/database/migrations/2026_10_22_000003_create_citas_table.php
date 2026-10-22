<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('paciente_nombre');
            $table->string('paciente_documento');
            $table->string('especialidad'); 
            $table->foreignId('medico_id')->nullable()->constrained('medicos')->onDelete('set null');
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('monto_pagar', 8, 2);
            $table->string('estado')->default('pendiente_pago'); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};