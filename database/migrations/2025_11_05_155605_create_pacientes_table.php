<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('paciente', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('codPa');
            $table->string('nomPa', 100);
            $table->string('paternoPa', 100)->nullable();
            $table->string('maternoPa', 100)->nullable();
            $table->string('estado', 20)->nullable();
            $table->date('fechaNac')->nullable();
            $table->enum('sexo', ['M','F'])->nullable();
            $table->string('nroHCI', 50)->unique()->nullable();
            $table->enum('tipoPac', ['SUS','SINSUS']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('paciente');
    }
};
