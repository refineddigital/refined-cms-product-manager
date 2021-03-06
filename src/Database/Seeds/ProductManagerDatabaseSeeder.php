<?php

namespace RefinedDigital\ProductManager\Database\Seeds;

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProductManagerDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(OrderSeeder::class);
        $this->call(ProductManagerTemplatesTableSeeder::class);
        $this->call(ProductFormSeeder::class);
        $this->call(ProductManagerPageTableSeeder::class);
        $this->call(ProductStatusTableSeeder::class);
    }
}
