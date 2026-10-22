<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_clinicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->onDelete('cascade');
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('cascade');
            $table->string('paciente_documento'); 
            $table->text('diagnostico');
            $table->text('tratamiento');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_clinicos');
    }
};