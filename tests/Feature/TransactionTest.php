<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_sees_transactions_but_not_record_button_on_index(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertOk();
        $response->assertDontSee(route('transactions.create'), false);
    }

    public function test_manager_sees_record_button_on_index(): void
    {
        $user = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertOk();
        $response->assertSee(route('transactions.create'), false);
    }

    public function test_staff_cannot_open_transaction_create_page(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($user)->get(route('transactions.create'));

        $response->assertForbidden();
    }

    public function test_staff_can_view_transaction_details_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $branch = Branch::create(['name' => 'Main Branch']);
        $product = Product::create(['sku' => 'TSHOW', 'name' => 'Show Product']);

        $transaction = Transaction::create([
            'type' => 'in',
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'to_branch_id' => null,
            'quantity' => 4,
            'user_id' => $admin->id,
            'notes' => 'Seed transaction',
        ]);

        $response = $this->actingAs($staff)->get(route('transactions.show', $transaction));

        $response->assertOk();
        $response->assertSeeText('Transaction Details');
        $response->assertSeeText('Show Product');
    }

    public function test_staff_cannot_record_transaction(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $branch = Branch::create(['name' => 'Main Branch']);
        $product = Product::create(['sku' => 'T000', 'name' => 'Guard Product']);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'in',
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 1,
        ]);

        $response->assertForbidden();
    }

    public function test_out_transaction_fails_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $branch = Branch::create(['name' => 'Main Branch']);
        $product = Product::create(['sku' => 'T003', 'name' => 'Test Product 3']);

        $product->branches()->attach($branch->id, ['stock_level' => 2]);

        $response = $this->actingAs($user)
            ->from(route('transactions.create'))
            ->post(route('transactions.store'), [
                'type' => 'out',
                'product_id' => $product->id,
                'branch_id' => $branch->id,
                'quantity' => 5,
            ]);

        $response->assertRedirect(route('transactions.create'));
        $response->assertSessionHasErrors(['quantity']);
        $this->assertDatabaseHas('product_branch', [
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'stock_level' => 2,
        ]);
    }

    public function test_transfer_requires_different_destination_branch(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $branch = Branch::create(['name' => 'Main Branch']);
        $product = Product::create(['sku' => 'T004', 'name' => 'Test Product 4']);

        $product->branches()->attach($branch->id, ['stock_level' => 10]);

        $response = $this->actingAs($user)
            ->from(route('transactions.create'))
            ->post(route('transactions.store'), [
                'type' => 'transfer',
                'product_id' => $product->id,
                'branch_id' => $branch->id,
                'to_branch_id' => $branch->id,
                'quantity' => 2,
            ]);

        $response->assertRedirect(route('transactions.create'));
        $response->assertSessionHasErrors(['to_branch_id']);
    }

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
