<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cronogramaAtencion', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->date('fechaCrono')->primary();
            $table->integer('cantDispo')->nullable();
            $table->integer('cantFijo')->nullable();
            $table->integer('cantEmergencia')->nullable();
            $table->string('estado', 20)->nullable();
            $table->unsignedInteger('codPer')->nullable();

            $table->foreign('codPer')->references('codPer')->on('PersonalSalud')->onDelete('set null');
        });
    }

    public function down(): void {
        Schema::dropIfExists('cronogramaAtencion');
    }
};
