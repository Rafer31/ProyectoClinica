<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('diagnostico', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('codDiag');
            $table->string('descripDiag', 200);
        });
    }

    public function down(): void {
        Schema::dropIfExists('diagnostico');
    }
};
