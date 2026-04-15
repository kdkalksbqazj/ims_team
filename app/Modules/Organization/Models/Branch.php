<?php

namespace App\Modules\Organization\Models;

use App\Modules\Catalog\Models\Product;
use App\Modules\IAM\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'location', 'address', 'phone', 'email', 'status'])]
class Branch extends Model
{
    use HasFactory;

    /**
     * Get the products in this branch.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_branch')
                    ->withPivot('stock_level', 'reorder_threshold')
                    ->withTimestamps();
    }

    /**
     * Get the users in this branch.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
