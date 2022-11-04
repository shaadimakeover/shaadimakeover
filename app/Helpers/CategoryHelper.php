<?php

namespace App\Helpers;

use App\Models\Category;

class CategoryHelper
{

    public static function getCategoryTree($parent_id = 0, $spacing = '', $tree_array = array())
    {
        $categories = Category::where('parent_id', '=', $parent_id)->orderBy('parent_id')->get();
        foreach ($categories as $item) {
            $tree_array[] = [
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'title' => $spacing . ucwords($item->title),
                'slug' => $item->slug,
                'short_description' => $item->short_description,
                'long_description' => $item->long_description,
                'meta_key' => $item->meta_key,
                'meta_description' => $item->meta_description,
                'thumbnail' => $item->thumbnail,
                'active' => $item->active,
                'cat_title' => $item->title
            ];
            $tree_array = self::getCategoryTree($item->id, $spacing . '--> ', $tree_array);
        }
        return $tree_array;
    }
}
