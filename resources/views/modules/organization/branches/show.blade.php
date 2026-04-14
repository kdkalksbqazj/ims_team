<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Branch Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">Name:</h3>
                        <p>{{ $branch->name }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">Location:</h3>
                        <p>{{ $branch->location }}</p>
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-lg font-bold mb-4">Inventory in this Branch</h3>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reorder Threshold</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($branch->products as $product)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->pivot->stock_level }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $product->pivot->reorder_threshold }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No inventory recorded for this branch.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between mt-8">
                        <a href="{{ route('branches.edit', $branch) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Edit Branch
                        </a>
                        <a href="{{ route('branches.index') }}" class="text-gray-600 hover:text-gray-900">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
