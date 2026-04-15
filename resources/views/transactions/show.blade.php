<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transaction Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold">Type</p>
                            <p class="text-lg">{{ ucfirst($transaction->type) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold">Date</p>
                            <p class="text-lg">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold">Product</p>
                            <p class="text-lg">{{ $transaction->product->name }} ({{ $transaction->product->sku }})</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold">Quantity</p>
                            <p class="text-lg">{{ $transaction->quantity }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold">Source Branch</p>
                            <p class="text-lg">{{ $transaction->branch->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold">Destination Branch</p>
                            <p class="text-lg">{{ $transaction->toBranch?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase text-gray-500 font-semibold">Recorded By</p>
                            <p class="text-lg">{{ $transaction->user->name }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs uppercase text-gray-500 font-semibold">Notes</p>
                        <p class="text-base mt-1">{{ $transaction->notes ?: 'No notes provided.' }}</p>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-900">Back to Transactions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>