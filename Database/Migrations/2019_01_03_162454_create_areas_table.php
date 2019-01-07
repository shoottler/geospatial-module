<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
	        $table->increments('id');
	        $table->string('type');
	        $table->integer('company_id')->unsigned()->index();
	        $table->string('name');
	        $table->string('country');
	        $table->string('city');
	        $table->string('IATA');
	        $table->string('ICAO')->nullable();
	        $table->string('FAA')->nullable();
	        $table->timestamps();

	        $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('areas');
    }
}
