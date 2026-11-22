<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('novedades', function (Blueprint $table) {$table->id();
            $table->foreignId('user_id')->constrained('users');$table->string('titulo', 180);
            $table->string('slug', 200)->unique();$table->string('categoria', 50)->default('Comunicado');
            $table->string('resumen', 300);$table->text('contenido');
            $table->string('imagen')->nullable();$table->boolean('publicado')->default(true);
            $table->timestamp('fecha_publicacion')->nullable();$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('novedades');
    }
};