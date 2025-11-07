<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('consultorio', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('codCons');
            $table->string('numCons', 50);
        });
    }

    public function down(): void {
        Schema::dropIfExists('consultorio');
    }
};
