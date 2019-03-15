<?php

use Illuminate\Database\Seeder;

class SeasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function() {
            \App\Models\Season::insert([
                ['name' => '1 st Season', 'completed' => 0]
            ]);
        });
    }
}
