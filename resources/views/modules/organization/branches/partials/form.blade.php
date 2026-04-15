<div class="mb-4">
    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name:</label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $branch->name ?? '') }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        required
    >
    @error('name')
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="location" class="block text-gray-700 text-sm font-bold mb-2">Location/City:</label>
    <input
        type="text"
        name="location"
        id="location"
        value="{{ old('location', $branch->location ?? '') }}"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
    >
    @error('location')
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Full Address:</label>
    <textarea
        name="address"
        id="address"
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
    >{{ old('address', $branch->address ?? '') }}</textarea>
    @error('address')
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="mb-4">
        <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">Phone:</label>
        <input
            type="text"
            name="phone"
            id="phone"
            value="{{ old('phone', $branch->phone ?? '') }}"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
        @error('phone')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
    <div class="mb-4">
        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', $branch->email ?? '') }}"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        >
        @error('email')
            <p class="text-red-500 text-xs italic">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-4">
    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
    <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        <option value="active" {{ old('status', $branch->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', $branch->status ?? 'active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status')
        <p class="text-red-500 text-xs italic">{{ $message }}</p>
    @enderror
</div>

<div class="flex items-center justify-between">
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
        {{ $submitLabel }}
    </button>
    <a href="{{ route('branches.index') }}" class="text-gray-600 hover:text-gray-900">Cancel</a>
</div>