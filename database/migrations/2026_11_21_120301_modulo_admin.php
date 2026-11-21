<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ampliar el ENUM de roles sin perder registros
        DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('paciente', 'medico', 'admision', 'admin') NOT NULL DEFAULT 'paciente'");

        // 2. Bandera de estado para baja lógica (evita borrados físicos en cascada)
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('rol');
        });

        Schema::table('medicos', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('consultorio');
        });

        Schema::table('especialidades', function (Blueprint $table) {
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            $table->dropColumn('activo');
        });

        Schema::table('medicos', function (Blueprint $table) {
            $table->dropColumn('activo');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activo');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN rol ENUM('paciente', 'medico', 'admision') NOT NULL DEFAULT 'paciente'");
    }
};