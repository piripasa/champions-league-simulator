<?php

use Illuminate\Database\Seeder;

class StrengthsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function() {
            \App\Models\Strength::insert([
                ['team_id' => 1, 'is_home' => 1,'strength' => 'strong'],
                ['team_id' => 1, 'is_home' => 0,'strength' => 'average'],
                ['team_id' => 2, 'is_home' => 1,'strength' => 'average'],
                ['team_id' => 2, 'is_home' => 0,'strength' => 'average'],
                ['team_id' => 3, 'is_home' => 1,'strength' => 'weak'],
                ['team_id' => 3, 'is_home' => 0,'strength' => 'average'],
                ['team_id' => 4, 'is_home' => 1,'strength' => 'strong'],
                ['team_id' => 4, 'is_home' => 0,'strength' => 'strong'],
            ]);
        });
    }
}
