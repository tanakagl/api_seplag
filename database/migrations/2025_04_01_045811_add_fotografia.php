<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fotografia', function (Blueprint $table) {
            $table->id('fot_id');
            $table->unsignedBigInteger('pes_id');
            $table->string('fot_caminho', 255);
            $table->string('fot_nome_original', 255);
            $table->string('fot_tipo', 100);
            $table->unsignedBigInteger('fot_tamanho');
            $table->timestamps();
            
            $table->foreign('pes_id')
                  ->references('pes_id')
                  ->on('pessoa')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fotografia');
    }
};
