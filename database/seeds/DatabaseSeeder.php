<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TeamsTableSeeder::class);
        $this->call(StrengthsTableSeeder::class);
        $this->call(SeasonsTableSeeder::class);
        $this->call(WeeksTableSeeder::class);
    }
}
