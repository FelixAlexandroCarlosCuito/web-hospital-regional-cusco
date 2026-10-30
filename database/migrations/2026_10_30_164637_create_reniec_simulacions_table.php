<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reniec_simulacions', function (Blueprint $table) {
            $table->id();
            $table->string('dni', 8)->unique();
            $table->char('digito_verificador', 1);
            $table->date('fecha_emision'); // Formato YYYY-MM-DD
            $table->string('nombres');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->date('fecha_nacimiento');
            $table->string('direccion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reniec_simulacions');
    }
};
