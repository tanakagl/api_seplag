<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CidadeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            \Canducci\ZipCode\Seeders\StatesTableSeeder::class,
            \Canducci\ZipCode\Seeders\CitiesTableSeeder::class,
        ]);
    }
}
