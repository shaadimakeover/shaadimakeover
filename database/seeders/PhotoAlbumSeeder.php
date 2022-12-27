<?php

namespace Database\Seeders;

use App\Models\PhotoAlbum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhotoAlbumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['name' => "Top Photos"],
            ['name' => "Bridal Makeup"],
            ['name' => "Engagement Makeup"],
            ['name' => "Party Makeup"],
            ['name' => "Studio Photo"],
            ['name' => "Profile Photo"],
            ['name' => "Achievement Photo"],
            ['name' => "Hair style Photo"],
            ['name' => "Mehendi Photo"]
        ];
        foreach ($datas as $key => $item) {
            PhotoAlbum::create($item);
        }
    }
}
