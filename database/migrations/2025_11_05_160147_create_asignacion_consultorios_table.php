<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asignacionConsultorio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('idAsignacion');
            $table->date('fechaInicio')->nullable();
            $table->date('fechaFin')->nullable();
            $table->unsignedInteger('codPer')->nullable();
            $table->unsignedInteger('codCons')->nullable();

            $table->foreign('codPer')->references('codPer')->on('PersonalSalud')->onDelete('set null');
            $table->foreign('codCons')->references('codCons')->on('Consultorio')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('asignacionConsultorio');
    }
};
