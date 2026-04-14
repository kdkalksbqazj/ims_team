<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create(['sku' => 'P001', 'name' => 'Laptop', 'category' => 'Electronics', 'description' => 'High-performance laptop']);
        Product::create(['sku' => 'P002', 'name' => 'Smartphone', 'category' => 'Electronics', 'description' => 'Latest smartphone']);
        Product::create(['sku' => 'P003', 'name' => 'Office Chair', 'category' => 'Furniture', 'description' => 'Ergonomic office chair']);
    }
}
