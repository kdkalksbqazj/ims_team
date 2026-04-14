<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['sku', 'name', 'category', 'description'])]
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
}
