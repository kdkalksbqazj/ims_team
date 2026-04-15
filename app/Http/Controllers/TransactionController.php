<?php

namespace App\Http\Controllers;

use App\Events\StockUpdated;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Transaction::class);

        $type = (string) $request->input('type', '');
        $productId = (string) $request->input('product_id', '');
        $branchId = (string) $request->input('branch_id', '');
        $dateFrom = (string) $request->input('date_from', '');
        $dateTo = (string) $request->input('date_to', '');

        $transactions = Transaction::query()
            ->with(['product:id,name,sku', 'branch:id,name', 'toBranch:id,name', 'user:id,name'])
            ->when(in_array($type, ['in', 'out', 'transfer', 'adjustment'], true), function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($productId !== '', function ($query) use ($productId) {
                $query->where('product_id', $productId);
            })
            ->when($branchId !== '', function ($query) use ($branchId) {
                $query->where(function ($nested) use ($branchId) {
                    $nested->where('branch_id', $branchId)
                        ->orWhere('to_branch_id', $branchId);
                });
            })
            ->when($dateFrom !== '', function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo !== '', function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $products = Product::query()->orderBy('name')->get(['id', 'name', 'sku']);
        $branches = Branch::query()->orderBy('name')->get(['id', 'name']);

        return view('transactions.index', [
            'transactions' => $transactions,
            'products' => $products,
            'branches' => $branches,
            'filters' => [
                'type' => $type,
                'product_id' => $productId,
                'branch_id' => $branchId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', Transaction::class);

        $products = Product::query()->orderBy('name')->get();
        $branches = Branch::query()->orderBy('name')->get();

        return view('transactions.create', compact('products', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;

        if ($validated['type'] !== 'transfer') {
            $validated['to_branch_id'] = null;
        }

        DB::transaction(function () use ($validated) {
            $product = Product::findOrFail($validated['product_id']);
            $sourceBranch = Branch::findOrFail($validated['branch_id']);
            $sourceRow = $this->getLockedStockRow($product->id, $sourceBranch->id);
            $quantity = (int) $validated['quantity'];
            $sourceStock = (int) $sourceRow->stock_level;

            if (in_array($validated['type'], ['out', 'transfer'], true) && $sourceStock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Insufficient stock in the selected source branch.',
                ]);
            }

            if (in_array($validated['type'], ['in', 'adjustment'], true)) {
                $this->updateStockRow($product->id, $sourceBranch->id, $sourceStock + $quantity);
                event(new StockUpdated($product, $sourceBranch));
            }

            if ($validated['type'] === 'out') {
                $this->updateStockRow($product->id, $sourceBranch->id, $sourceStock - $quantity);
                event(new StockUpdated($product, $sourceBranch));
            }

            if ($validated['type'] === 'transfer') {
                $this->updateStockRow($product->id, $sourceBranch->id, $sourceStock - $quantity);
                event(new StockUpdated($product, $sourceBranch));

                $destinationBranch = Branch::findOrFail($validated['to_branch_id']);
                $destinationRow = $this->getLockedStockRow($product->id, $destinationBranch->id);
                $destinationStock = (int) $destinationRow->stock_level;

                $this->updateStockRow($product->id, $destinationBranch->id, $destinationStock + $quantity);
                event(new StockUpdated($product, $destinationBranch));
            }

            Transaction::create($validated);
        });

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        Gate::authorize('view', $transaction);

        $transaction->load(['product:id,name,sku', 'branch:id,name', 'toBranch:id,name', 'user:id,name']);

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Lock the stock row for a product/branch pair, creating it when missing.
     */
    private function getLockedStockRow(int $productId, int $branchId): object
    {
        $row = DB::table('product_branch')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->lockForUpdate()
            ->first();

        if ($row) {
            return $row;
        }

        DB::table('product_branch')->insert([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'stock_level' => 0,
            'reorder_threshold' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('product_branch')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->lockForUpdate()
            ->first();
    }

    /**
     * Update stock level for an existing product/branch pivot row.
     */
    private function updateStockRow(int $productId, int $branchId, int $newStock): void
    {
        DB::table('product_branch')
            ->where('product_id', $productId)
            ->where('branch_id', $branchId)
            ->update([
                'stock_level' => $newStock,
                'updated_at' => now(),
            ]);
    }
}
