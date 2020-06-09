<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValuesOfCriteriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('values_of_criteria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('criteria_id');
            $table->bigInteger('alternatif_id');
            $table->string('value',10);
            $table->timestamps();

            $table->index('criteria_id');
            $table->foreign('criteria_id')
            ->references('id')
            ->on('criterias')
            ->onUpdate('cascade')
            ->onDelete('restrict');

            $table->index('alternatif_id');
            $table->foreign('alternatif_id')
            ->references('id')
            ->on('alternatif')
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
        Schema::dropIfExists('values_of_criteria');
    }
}
