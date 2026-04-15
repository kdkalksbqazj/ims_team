<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reporting') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 font-semibold">Back to Dashboard</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Total Transactions</p>
                    <p class="text-2xl font-bold">{{ $summary['total_transactions'] }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Stock In Qty</p>
                    <p class="text-2xl font-bold text-green-700">{{ $summary['stock_in_quantity'] }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Stock Out Qty</p>
                    <p class="text-2xl font-bold text-red-700">{{ $summary['stock_out_quantity'] }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Transferred Qty</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $summary['transferred_quantity'] }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-5">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Adjusted Qty</p>
                    <p class="text-2xl font-bold text-amber-700">{{ $summary['adjusted_quantity'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Daily Trend (Last 7 Active Days)</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Transactions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($daily as $row)
                                <tr>
                                    <td class="px-4 py-2">{{ $row->date }}</td>
                                    <td class="px-4 py-2">{{ $row->total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-gray-500">No data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4">Top Products by Usage</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Transactions</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($topProducts as $item)
                                <tr>
                                    <td class="px-4 py-2">{{ $item->product_name }}</td>
                                    <td class="px-4 py-2">{{ $item->uses }}</td>
                                    <td class="px-4 py-2">{{ $item->total_qty }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-gray-500">No data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
