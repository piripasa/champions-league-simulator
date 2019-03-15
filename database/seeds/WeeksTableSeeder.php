<?php

use Illuminate\Database\Seeder;

class WeeksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function() {
            \App\Models\Week::insert([
                ['name' => '1st Week', 'season_id' => 1],
                ['name' => '2nd Week', 'season_id' => 1],
                ['name' => '3rd Week', 'season_id' => 1],
                ['name' => '4th Week', 'season_id' => 1],
                ['name' => '5th week', 'season_id' => 1],
                ['name' => '6th week', 'season_id' => 1],
            ]);
        });
    }
}
