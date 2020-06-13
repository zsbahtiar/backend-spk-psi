<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('dta_id')->nullable();
            $table->string('name',80);
            $table->enum('gender', ['male', 'female']);
            $table->string('email')->unique();
            $table->string('token_login')->nullable();
            $table->unsignedInteger('token_exp')->nullable();
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
        Schema::dropIfExists('operator');
    }
}
