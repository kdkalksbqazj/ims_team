<?php

namespace App\Modules\Catalog\Models;

use App\Modules\Organization\Models\Branch;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sku', 'name', 'category', 'category_id', 'description'])]
class Product extends Model
{
    use HasFactory;

    /**
     * Get the branches that have this product.
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branch')
                    ->withPivot('stock_level', 'reorder_threshold')
                    ->withTimestamps();
    }

    /**
     * Get the category for this product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
