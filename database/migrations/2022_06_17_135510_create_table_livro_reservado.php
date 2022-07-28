<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('livro_reservado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('data_devolucao')->nullable();
            $table->boolean('devolvido')->default(false);
            $table->unsignedBigInteger('livro_id');
            $table->unsignedBigInteger('reserva_id');
            
            $table->foreign('livro_id')->references('id')->on('livros');
            $table->foreign('reserva_id')->references('id')->on('reservas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('livro_reservado');
    }
};
