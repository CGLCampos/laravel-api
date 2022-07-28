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
        Schema::create('livros', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('titulo');
            $table->string('autor');
            $table->string('editora');
            $table->string('idioma');
            $table->string('data_publicacao');
            $table->boolean('reservado')->default(false);
            $table->boolean('excluido')->default(false);
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('reserva_id')->nullable();
            
            $table->foreign('categoria_id')->references('id')->on('categorias');
            $table->foreign('reserva_id')->references('id')->on('reservas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('livros');
    }
};
