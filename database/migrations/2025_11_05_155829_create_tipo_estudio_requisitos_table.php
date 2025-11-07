<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tipoEstudio_requisito', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedInteger('codTest');
            $table->unsignedInteger('codRequisito');
            $table->string('observacion', 200)->nullable();

            $table->primary(['codTest', 'codRequisito']);
            $table->foreign('codTest')->references('codTest')->on('TipoEstudio')->onDelete('cascade');
            $table->foreign('codRequisito')->references('codRequisito')->on('Requisito')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tipoEstudio_requisito');
    }
};
