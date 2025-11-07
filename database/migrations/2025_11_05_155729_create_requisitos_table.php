<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('requisito', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('codRequisito');
            $table->string('descripRequisito', 200);
        });
    }

    public function down(): void {
        Schema::dropIfExists('requisito');
    }
};
