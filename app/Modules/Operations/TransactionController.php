<?php

namespace App\Modules\Operations;

use App\Http\Controllers\Controller;
use App\Events\StockUpdated;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['product', 'branch', 'user'])->latest()->paginate(20);
        return view('modules.operations.transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $branches = Branch::all();
        return view('modules.operations.transactions.create', compact('products', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,transfer,adjustment',
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required_if:type,transfer|nullable|exists:branches,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        DB::transaction(function () use ($validated) {
            $transaction = Transaction::create($validated);

            $product = Product::findOrFail($validated['product_id']);
            $branch = Branch::findOrFail($validated['branch_id']);

            // Get or create pivot record
            $pivot = $product->branches()->where('branch_id', $branch->id)->first();
            if (!$pivot) {
                $product->branches()->attach($branch->id, ['stock_level' => 0]);
                $pivot = $product->branches()->where('branch_id', $branch->id)->first();
            }

            if ($validated['type'] === 'in' || $validated['type'] === 'adjustment') {
                $newStock = $pivot->pivot->stock_level + $validated['quantity'];
            } else {
                $newStock = $pivot->pivot->stock_level - $validated['quantity'];
            }

            $product->branches()->updateExistingPivot($branch->id, ['stock_level' => $newStock]);

            event(new StockUpdated($product, $branch));

            if ($validated['type'] === 'transfer') {
                $toBranch = Branch::findOrFail($validated['to_branch_id']);
                $toPivot = $product->branches()->where('branch_id', $toBranch->id)->first();
                if (!$toPivot) {
                    $product->branches()->attach($toBranch->id, ['stock_level' => 0]);
                    $toPivot = $product->branches()->where('branch_id', $toBranch->id)->first();
                }
                $toNewStock = $toPivot->pivot->stock_level + $validated['quantity'];
                $product->branches()->updateExistingPivot($toBranch->id, ['stock_level' => $toNewStock]);

                event(new StockUpdated($product, $toBranch));
            }
        });

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return view('modules.operations.transactions.show', compact('transaction'));
    }
}
