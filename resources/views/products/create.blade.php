@extends('layouts.app')

@section('title', 'Create Product - ' . config('app.name'))

@section('content')
<div class="max-w-3xl mx-auto mt-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-md rounded-lg">
        <div class="border-b px-6 py-4">
            <h3 class="text-2xl font-bold text-gray-800">Create New Product</h3>
        </div>
        <div class="p-6">
            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="category_id" name="category_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug <span class="text-gray-400 text-xs">(Optional - auto generated if empty)</span>
                    </label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('slug')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga (Rp)</label>
                        <input type="number" id="price" name="price" step="1" min="0" value="{{ old('price') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Contoh: 50000">
                        @error('price')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stock --}}
                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" min="0" value="{{ old('stock') }}" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('stock')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Image --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                    
                    {{-- Image Upload Option --}}
                    <div class="mb-4">
                        <label for="image" class="block text-sm font-medium text-gray-600 mb-2">
                            Upload Image File <span class="text-gray-400 text-xs">(JPEG, PNG, JPG, GIF - Max 2MB)</span>
                        </label>
                        <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('image')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        {{-- Image Preview --}}
                        <div id="imagePreview" class="mt-3 hidden">
                            <img id="preview" src="" alt="Image preview" class="max-w-xs max-h-48 object-cover rounded">
                        </div>
                    </div>

                    <div class="text-center text-gray-500 text-sm my-2">OR</div>

                    {{-- Image URL Option --}}
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-600 mb-2">
                            Image URL <span class="text-gray-400 text-xs">(External image link)</span>
                        </label>
                        <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}" onchange="previewImageUrl(this)"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('image_url')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        {{-- URL Image Preview --}}
                        <div id="urlImagePreview" class="mt-3 hidden">
                            <img id="urlPreview" src="" alt="URL image preview" class="max-w-xs max-h-48 object-cover rounded">
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-between pt-4">
                    <a href="{{ route('products.index') }}" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
                        Cancel
                    </a>
                    <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Auto-generate slug from name --}}
<script>
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });

    // Preview uploaded image file
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('preview');
        const urlPreview = document.getElementById('urlImagePreview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
                // Hide URL preview if showing
                urlPreview.classList.add('hidden');
                // Clear URL input
                document.getElementById('image_url').value = '';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    }

    // Preview image from URL
    function previewImageUrl(input) {
        const urlPreview = document.getElementById('urlImagePreview');
        const urlPreviewImg = document.getElementById('urlPreview');
        const filePreview = document.getElementById('imagePreview');
        
        if (input.value) {
            urlPreviewImg.src = input.value;
            urlPreview.classList.remove('hidden');
            // Hide file preview if showing
            filePreview.classList.add('hidden');
            // Clear file input
            document.getElementById('image').value = '';
        } else {
            urlPreview.classList.add('hidden');
        }
    }
</script>
@endsection
