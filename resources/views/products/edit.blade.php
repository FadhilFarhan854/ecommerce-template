@extends('layouts.app')

@section('title', 'Edit Product - ' . config('app.name'))

@section('content')
<div class="max-w-3xl mx-auto mt-10">
    <div class="bg-white shadow rounded-xl">
        <div class="px-6 py-4 border-b">
            <h3 class="text-xl font-semibold">Edit Product</h3>
        </div>
        <div class="p-6">
            {{-- Error Alert --}}
            @if($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block font-medium mb-1">Category</label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="block font-medium mb-1">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block font-medium mb-1">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('slug')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block font-medium mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" required
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Price & Stock --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block font-medium mb-1">Harga (Rp)</label>
                        <input type="number" step="1" min="0" id="price" name="price"
                            value="{{ old('price', $product->price) }}" placeholder="Contoh: 50000" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('price')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="stock" class="block font-medium mb-1">Stock Quantity</label>
                        <input type="number" min="0" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('stock')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Product Image --}}
                <div>
                    <label class="block font-medium mb-2">Product Image</label>

                    {{-- Current Image --}}
                    @if($product->image)
                        <div class="mb-3">
                            <p class="text-sm text-gray-500 mb-1">Current Image:</p>
                            <img src="{{ $product->image }}" alt="Current product image"
                                 class="rounded-lg shadow max-w-[200px] max-h-[200px]">
                        </div>
                    @endif

                    {{-- Upload --}}
                    <div class="mb-3">
                        <label for="image" class="block font-medium">Upload New Image
                            <span class="text-sm text-gray-500">(JPEG, PNG, GIF - Max 2MB)</span>
                        </label>
                        <input type="file" id="image" name="image" accept="image/*"
                            class="w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                        @error('image')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="text-center text-gray-400 my-2">OR</div>

                    {{-- Image URL --}}
                    <div>
                        <label for="image_url" class="block font-medium">Image URL
                            <span class="text-sm text-gray-500">(External link)</span>
                        </label>
                        <input type="url" id="image_url" name="image_url"
                            value="{{ old('image_url', $product->image && !str_starts_with($product->image, '/storage/') ? $product->image : '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('image_url')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-between items-center">
                    <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Cancel</a>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Auto Slug --}}
<script>
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
