<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('medico', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('codMed');
            $table->string('nomMed', 100);
            $table->string('paternoMed', 100)->nullable();
            $table->string('tipoMed', 50)->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('medico');
    }
};
