<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('week_id')->nullable();
            $table->unsignedInteger('home')->nullable()->comment('home team id');
            $table->unsignedInteger('away')->nullable()->comment('away team id');
            $table->tinyInteger('home_goal')->default(0);
            $table->tinyInteger('away_goal')->default(0);
            $table->boolean('played')->default(0);
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
        Schema::dropIfExists('matches');
    }
}
