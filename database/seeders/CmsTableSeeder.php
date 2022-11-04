<?php

namespace Database\Seeders;

use App\Models\Cms;
use Illuminate\Database\Seeder;

class CmsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
           [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
            ],
        ];
        foreach ($datas as $data) {
            Cms::create($data);
        }
    }
}
