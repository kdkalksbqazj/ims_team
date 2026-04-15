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
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <div class="lg:col-span-2 bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Branch Profile</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs uppercase text-gray-500 font-semibold">Branch Name</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $branch->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase text-gray-500 font-semibold">Location</p>
                                    <p class="text-lg text-gray-900">{{ $branch->location ?? 'N/A' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs uppercase text-gray-500 font-semibold">Address</p>
                                    <p class="text-lg text-gray-900">{{ $branch->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">Contact and Status</h3>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-xs uppercase text-gray-500 font-semibold mb-1">Status</p>
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $branch->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($branch->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs uppercase text-gray-500 font-semibold">Phone</p>
                                    <p class="text-gray-900">{{ $branch->phone ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase text-gray-500 font-semibold">Email</p>
                                    <p class="text-gray-900">{{ $branch->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-white rounded-lg border border-gray-200">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold">Assigned Staff</h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @forelse ($branch->users as $user)
                                <li class="px-5 py-3 flex justify-between items-center">
                                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ ucfirst($user->role) }}</span>
                                </li>
                            @empty
                                <li class="px-5 py-4 text-gray-500 italic">No staff assigned to this branch.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="mt-8 bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold">Inventory in this Branch</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reorder Threshold</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock State</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($branch->products as $product)
                                        @php
                                            $lowStock = $product->pivot->stock_level <= $product->pivot->reorder_threshold;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $product->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->pivot->stock_level }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->pivot->reorder_threshold }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $lowStock ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                                    {{ $lowStock ? 'Low Stock' : 'Healthy' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-5 text-center text-gray-500">No inventory recorded for this branch.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8">
                        @can('update', $branch)
                            <a href="{{ route('branches.edit', $branch) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Edit Branch
                            </a>
                        @else
                            <span></span>
                        @endcan
                        <a href="{{ route('branches.index') }}" class="text-gray-600 hover:text-gray-900">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
