<?php

use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function() {
            \App\Models\Team::insert([
                ['name' => 'Chelsea'],
                ['name' => 'Arsenal'],
                ['name' => 'Manchester City'],
                ['name' => 'Liverpool'],
            ]);
        });
    }
}
