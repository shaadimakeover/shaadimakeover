<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PricingCategorySeeder::class);
        $this->call(UserRoleTableSeeder::class);
        $this->call(UserTableSeeder::class);
        //$this->call(ProductSeeder::class);
        $this->call(CmsTableSeeder::class);
        $this->call(CategorySeeder::class);
    }
}
