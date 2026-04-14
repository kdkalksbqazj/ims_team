<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">SKU:</h3>
                        <p>{{ $product->sku }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">Name:</h3>
                        <p>{{ $product->name }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">Category:</h3>
                        <p>{{ $product->category->name ?? 'Uncategorized' }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">Description:</h3>
                        <p>{{ $product->description ?? 'No description provided.' }}</p>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-bold mb-4">Stock Levels by Branch</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($product->branches as $branch)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $branch->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $branch->pivot->stock_level }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($branch->pivot->stock_level <= $branch->pivot->reorder_threshold)
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">Low Stock</span>
                                            @else
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-bold">Healthy</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500 italic">No stock records found for any branch.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-between">
                        <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Edit Product
                        </a>
                        <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
