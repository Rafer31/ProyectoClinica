<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('servicio_diagnostico', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('codServ');
            $table->unsignedInteger('codDiag');
            $table->enum('tipo', ['sol','eco'])->nullable();

            $table->primary(['codServ','codDiag']);
            $table->foreign('codServ')->references('codServ')->on('Servicio')->onDelete('cascade');
            $table->foreign('codDiag')->references('codDiag')->on('Diagnostico')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('servicio_diagnostico');
    }
};
