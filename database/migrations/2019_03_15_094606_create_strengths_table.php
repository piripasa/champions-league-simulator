<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStrengthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strengths', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('team_id')->nullable();
            $table->boolean('is_home')->nullable()->default(0);
            $table->enum('strength', ['weak','average','strong'])->nullable();
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
        Schema::dropIfExists('strengths');
    }
}
