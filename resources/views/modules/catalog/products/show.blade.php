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
                        <p>{{ $product->category }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-lg font-bold">Description:</h3>
                        <p>{{ $product->description }}</p>
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
