<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('personalSalud', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('codPer');
            $table->string('usuarioPer', 50)->unique();
            $table->string('clavePer', 100);
            $table->string('nomPer', 100)->nullable();
            $table->string('paternoPer', 100)->nullable();
            $table->string('maternoPer', 100)->nullable();
            $table->string('estado', 20)->nullable();
            $table->unsignedInteger('codRol');

            $table->foreign('codRol')->references('codRol')->on('Rol')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('personalSalud');
    }
};
