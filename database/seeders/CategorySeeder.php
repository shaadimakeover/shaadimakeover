<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['title' => 'Hair Cut'],
            ['title' => 'Styling'],
            ['title' => 'Hair spa'],
            ['title' => 'Shampo'],
            ['title' => 'Hair Cut'],
            ['title' => 'Styling'],
            ['title' => 'Hair spa'],
            ['title' => 'Shampo']

        ];
        foreach ($items as $key => $item) {
            Category::create($item);
        }
    }
}
