<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('servicio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('codServ');
            $table->date('fechaSol')->nullable();
            $table->time('horaSol')->nullable();
            $table->string('nroServ', 50)->unique()->nullable();
            $table->date('fechaAten')->nullable();
            $table->time('horaAten')->nullable();
            $table->date('fechaEnt')->nullable();
            $table->time('horaEnt')->nullable();
            $table->enum('tipoAseg', ['AsegEmergencia','AsegRegular','NoAsegEmergencia','NoAsegRegular'])->nullable();
            $table->string('nroFicha', 50)->nullable();
            $table->enum('estado', ['Programado','Atendido','Entregado','EnProceso','Cancelado','Archivado'])->nullable();

            $table->unsignedInteger('codPa')->nullable();
            $table->unsignedInteger('codMed')->nullable();
            $table->unsignedInteger('codTest')->nullable();
            $table->time('horaCrono')->nullable();
            $table->date('fechaCrono')->nullable();

            $table->foreign('codPa')->references('codPa')->on('Paciente')->onDelete('set null');
            $table->foreign('codMed')->references('codMed')->on('Medico')->onDelete('set null');
            $table->foreign('codTest')->references('codTest')->on('TipoEstudio')->onDelete('set null');
            $table->foreign('fechaCrono')->references('fechaCrono')->on('CronogramaAtencion')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('servicio');
    }
};
