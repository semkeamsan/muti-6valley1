<?php

namespace App\CPU;

use App\Model\Category;
use App\Model\Product;

class CategoryManager
{
    public static function parents()
    {
        return Category::with(['childes.childes'])->where('position', 0)->priority()->get();
    }

    public static function child($parent_id)
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }

    public static function products($category_id)
    {
        return Product::query()
            ->Active()
            ->where('category_ids', 'like', '%' . $category_id . '%')->get();
            // ->where('category_ids', 'like', '%"id":"' . $category_id . '"%')->get();
            // ->whereJsonContains('category_ids', ["id" => $category_id])->get();
    }
}
