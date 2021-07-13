<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Database\Seeders\DishSeeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([IngredientSeeder::class, DishSeeder::class]);
    }
}
