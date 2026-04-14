<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Catalog\Models\Product;
use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_is_updated_on_transaction(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $branch = Branch::create(['name' => 'Main Branch']);
        $product = Product::create(['sku' => 'T001', 'name' => 'Test Product']);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'in',
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 10,
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('product_branch', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'stock_level' => 10,
        ]);
    }

    public function test_stock_is_transferred_between_branches(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $branchA = Branch::create(['name' => 'Branch A']);
        $branchB = Branch::create(['name' => 'Branch B']);
        $product = Product::create(['sku' => 'T002', 'name' => 'Test Product 2']);

        // Initial stock in Branch A
        $product->branches()->attach($branchA->id, ['stock_level' => 20]);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'transfer',
            'product_id' => $product->id,
            'branch_id' => $branchA->id,
            'to_branch_id' => $branchB->id,
            'quantity' => 5,
        ]);

        $response->assertRedirect(route('transactions.index'));
        $this->assertDatabaseHas('product_branch', [
            'product_id' => $product->id,
            'branch_id' => $branchA->id,
            'stock_level' => 15,
        ]);
        $this->assertDatabaseHas('product_branch', [
            'product_id' => $product->id,
            'branch_id' => $branchB->id,
            'stock_level' => 5,
        ]);
    }
}
