<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlternatifTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alternatif', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dta_id')->nullable();
            $table->string('no_induk_dta',30)->unique()->nullable();
            $table->string('nik',16)->unique()->nullable();
            $table->string('name',80);
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->timestamps();

            $table->index('dta_id');
            $table->foreign('dta_id')
            ->references('id')
            ->on('dta')
            ->onUpdate('cascade')
            ->onDelete('restrict');
   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alternatif');
    }
}
