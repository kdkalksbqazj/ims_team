<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Modules\Catalog\Models\Product;
use App\Modules\Catalog\Models\Category;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic gadgets and devices'
        ]);

        $furniture = Category::create([
            'name' => 'Furniture',
            'slug' => 'furniture',
            'description' => 'Office and home furniture'
        ]);

        Product::create([
            'sku' => 'P001',
            'name' => 'Laptop',
            'category_id' => $electronics->id,
            'description' => 'High-performance laptop'
        ]);

        Product::create([
            'sku' => 'P002',
            'name' => 'Smartphone',
            'category_id' => $electronics->id,
            'description' => 'Latest smartphone'
        ]);

        Product::create([
            'sku' => 'P003',
            'name' => 'Office Chair',
            'category_id' => $furniture->id,
            'description' => 'Ergonomic office chair'
        ]);
    }
}
