<?php

namespace App\Models;

use App\Modules\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'product_id', 'branch_id', 'to_branch_id', 'quantity', 'user_id', 'notes'])]
class Transaction extends Model
{
    use HasFactory;

    /**
     * Get the product involved in the transaction.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch where the transaction occurred.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the destination branch for transfers.
     */
    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    /**
     * Get the user who performed the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
