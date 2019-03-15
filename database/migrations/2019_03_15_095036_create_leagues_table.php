<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->nullable();
            $table->tinyInteger('points')->nullable();
            $table->tinyInteger('played')->nullable();
            $table->tinyInteger('won')->nullable();
            $table->tinyInteger('drawn')->nullable();
            $table->tinyInteger('lost')->nullable();
            $table->tinyInteger('goal_difference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leagues');
    }
}
