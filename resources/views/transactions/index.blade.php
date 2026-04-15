<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transactions') }}
            </h2>
            @can('create', \App\Models\Transaction::class)
                <a href="{{ route('transactions.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Record Transaction
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('transactions.index') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All</option>
                                    <option value="in" {{ ($filters['type'] ?? '') === 'in' ? 'selected' : '' }}>Stock In</option>
                                    <option value="out" {{ ($filters['type'] ?? '') === 'out' ? 'selected' : '' }}>Stock Out</option>
                                    <option value="transfer" {{ ($filters['type'] ?? '') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="adjustment" {{ ($filters['type'] ?? '') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                </select>
                            </div>
                            <div>
                                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                                <select name="product_id" id="product_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ (string) ($filters['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->sku }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                                <select name="branch_id" id="branch_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ (string) ($filters['branch_id'] ?? '') === (string) $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From</label>
                                <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To</label>
                                <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded">Apply Filters</button>
                            <a href="{{ route('transactions.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded">Reset</a>
                        </div>
                    </form>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transactions as $transaction)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $transaction->type === 'out' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $transaction->type === 'transfer' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $transaction->type === 'adjustment' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $transaction->branch->name }}
                                        @if ($transaction->type === 'transfer' && $transaction->toBranch)
                                            <span class="text-gray-500">to {{ $transaction->toBranch->name }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->quantity }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $transaction->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">No transactions found for the selected filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
