<?php

namespace Database\Seeders;

use App\Models\PricingCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['name' => 'AIRBRUSH BRIDAL MAKEUP'],
            ['name' => 'BRIDAL MAKEUP'],
            ['name' => 'GUEST/FAMILY MAKEUP'],
            ['name' => 'TRIAL MAKEUP']
        ];
        foreach ($datas as $key => $value) {
            PricingCategory::create($value);
        }
    }
}
