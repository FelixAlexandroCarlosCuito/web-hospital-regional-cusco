<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();
            // Llave foránea: Un médico pertenece estrictamente a una especialidad
            $table->foreignId('especialidad_id')->constrained('especialidades')->onDelete('cascade');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('consultorio');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};