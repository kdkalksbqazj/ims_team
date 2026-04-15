<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="bg-gray-900 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded">
                Open Reports
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium">Total Products</h3>
                    <p class="text-3xl font-bold">{{ $totalProducts }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium">Branches</h3>
                    <p class="text-3xl font-bold">{{ $totalBranches }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium">Recent Transactions</h3>
                    <p class="text-3xl font-bold">{{ $recentTransactions->count() }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-gray-500 text-sm font-medium">Low Stock Items</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $lowStockItems->count() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recent Transactions</h3>
                    <ul class="divide-y divide-gray-200">
                        @forelse ($recentTransactions as $transaction)
                            <li class="py-3">
                                <div class="flex justify-between">
                                    <span class="font-medium">{{ $transaction->product->name }} ({{ $transaction->type }})</span>
                                    <span class="text-gray-500 text-sm">{{ $transaction->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-gray-600 text-sm">
                                    {{ $transaction->quantity }} units @ {{ $transaction->branch->name ?? 'N/A' }}
                                </div>
                            </li>
                        @empty
                            <li class="py-3 text-gray-500">No recent transactions.</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('transactions.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm mt-4 block">View All Transactions</a>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-red-600">Low Stock Alerts</h3>
                    @if ($lowStockItems->isEmpty())
                        <p class="text-gray-500">All stock levels are healthy.</p>
                    @else
                        <ul class="divide-y divide-gray-200">
                            @foreach ($lowStockItems as $item)
                                <li class="py-3">
                                    <div class="flex justify-between">
                                        <span class="font-medium">{{ $item->product_name }}</span>
                                        <span class="text-red-600 font-bold">{{ $item->stock_level }} / {{ $item->reorder_threshold }}</span>
                                    </div>
                                    @if (isset($item->branch_name))
                                        <div class="text-gray-600 text-sm">Branch: {{ $item->branch_name }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
