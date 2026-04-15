<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dashboard_and_reports_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $branch = Branch::create(['name' => 'Main Branch']);
        $product = Product::create(['sku' => 'AN-001', 'name' => 'Analytics Product']);

        $product->branches()->attach($branch->id, [
            'stock_level' => 3,
            'reorder_threshold' => 5,
        ]);

        Transaction::create([
            'type' => 'in',
            'product_id' => $product->id,
            'branch_id' => $branch->id,
            'quantity' => 3,
            'user_id' => $admin->id,
        ]);

        $dashboardResponse = $this->actingAs($admin)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSeeText('Low Stock Alerts');
        $dashboardResponse->assertSeeText('Analytics Product');

        $reportResponse = $this->actingAs($admin)->get(route('reports.index'));
        $reportResponse->assertOk();
        $reportResponse->assertSeeText('Reporting');
        $reportResponse->assertSeeText('Total Transactions');
    }

    public function test_non_admin_dashboard_scopes_transactions_to_user_branch(): void
    {
        $branchA = Branch::create(['name' => 'Branch A']);
        $branchB = Branch::create(['name' => 'Branch B']);

        $manager = User::factory()->create([
            'role' => 'manager',
            'branch_id' => $branchA->id,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $productA = Product::create(['sku' => 'AN-101', 'name' => 'Product A']);
        $productB = Product::create(['sku' => 'AN-102', 'name' => 'Product B']);

        Transaction::create([
            'type' => 'in',
            'product_id' => $productA->id,
            'branch_id' => $branchA->id,
            'quantity' => 5,
            'user_id' => $admin->id,
        ]);

        Transaction::create([
            'type' => 'in',
            'product_id' => $productB->id,
            'branch_id' => $branchB->id,
            'quantity' => 9,
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($manager)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSeeText('Product A');
        $response->assertDontSeeText('Product B');
    }
}
