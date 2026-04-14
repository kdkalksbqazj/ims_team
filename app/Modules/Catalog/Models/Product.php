<?php

namespace App\Modules\Catalog\Models;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sku', 'name', 'category_id', 'description'])]
class Product extends Model
{
    use HasFactory;

    /**
     * Get the category that the product belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the branches that have this product.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branch')
                    ->withPivot('stock_level', 'reorder_threshold')
                    ->withTimestamps();
    }
}
