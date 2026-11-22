<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Incorpora índices compuestos y restricciones de unicidad para optimizar consultas Eloquent
 * y prevenir bloqueos masivos o degradación por carga en InnoDB.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index(['rol', 'activo'], 'idx_users_rol_activo');
            $table->index(['rol', 'created_at'], 'idx_users_rol_created');
        });

        Schema::table('especialidades', function (Blueprint $table) {
            $table->index(['activo', 'nombre'], 'idx_especialidades_activo_nombre');
        });

        Schema::table('medicos', function (Blueprint $table) {
            $table->unique('user_id', 'uq_medicos_user_id');
            $table->index(['especialidad_id', 'activo'], 'idx_medicos_esp_activo');
        });

        Schema::table('citas', function (Blueprint $table) {
            // Índice cubriente para disponibilidad, cálculo de orden y bloqueo por fila en reservas
            $table->index(['medico_id', 'fecha', 'estado', 'hora'], 'idx_citas_medico_agenda');
            // Acelera el panel del paciente, tope de citas pendientes y control de cruces horarios
            $table->index(['user_id', 'estado', 'fecha', 'hora'], 'idx_citas_paciente_estado');
            // Acelera métricas diarias, mensuales y tendencia de 7 días en el panel administrativo
            $table->index(['fecha', 'estado'], 'idx_citas_fecha_estado');
            $table->index('created_at', 'idx_citas_created_at');
        });

        Schema::table('novedades', function (Blueprint $table) {
            $table->index(['publicado', 'fecha_publicacion', 'id'], 'idx_novedades_pub_fecha');
            $table->index(['publicado', 'categoria', 'fecha_publicacion'], 'idx_novedades_pub_cat_fecha');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_rol_activo');
            $table->dropIndex('idx_users_rol_created');
        });

        Schema::table('especialidades', function (Blueprint $table) {
            $table->dropIndex('idx_especialidades_activo_nombre');
        });

        Schema::table('medicos', function (Blueprint $table) {
            $table->dropUnique('uq_medicos_user_id');
            $table->dropIndex('idx_medicos_esp_activo');
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->dropIndex('idx_citas_medico_agenda');
            $table->dropIndex('idx_citas_paciente_estado');
            $table->dropIndex('idx_citas_fecha_estado');
            $table->dropIndex('idx_citas_created_at');
        });

        Schema::table('novedades', function (Blueprint $table) {
            $table->dropIndex('idx_novedades_pub_fecha');
            $table->dropIndex('idx_novedades_pub_cat_fecha');
        });
    }
};