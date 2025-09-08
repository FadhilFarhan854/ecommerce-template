@extends('layouts.app')

@section('title', 'Edit Product - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Edit Product</h1>
                <p class="text-blue-100 mt-1">Update product information</p>
            </div>
            
            <div class="p-6">
                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Information Section -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Category --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select id="category_id" name="category_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Product Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Product Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="Enter product name">
                                @error('name')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Product Slug --}}
                        <div class="mt-6">
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Product Slug</label>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="Auto-generated from name">
                            <p class="text-gray-500 text-xs mt-1">Leave empty to auto-generate from product name</p>
                            @error('slug')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" name="description" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="Enter product description">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Pricing & Inventory Section -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Pricing & Inventory
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Price --}}
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Price <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 font-medium">Rp</span>
                                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                                        class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                        placeholder="0.00">
                                </div>
                                @error('price')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Stock --}}
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                                    Stock Quantity <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="0">
                                @error('stock')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Weight --}}
                            @if(config('shipment.use_shipment', true))
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                                    Weight (grams) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="weight" name="weight" value="{{ old('weight', $product->weight) }}" min="0" step="0.1" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="0">
                                <p class="text-gray-500 text-xs mt-1">Weight for shipping calculation</p>
                                @error('weight')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @else
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (grams)</label>
                                <input type="number" id="weight" name="weight" value="{{ old('weight', $product->weight) }}" min="0" step="0.1"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                    placeholder="0">
                                <p class="text-gray-500 text-xs mt-1">Optional - shipping is free</p>
                                @error('weight')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Images Section -->
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Product Images
                        </h3>
                        
                        {{-- Current Images --}}
                        @if($product->images && $product->images->count() > 0)
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Current Images:</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach($product->images as $image)
                                        <div class="relative group">
                                            <img src="{{ $image->url }}" alt="Current product image" class="w-full h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                                            <div class="absolute top-2 right-2 flex items-center">
                                                <label class="flex items-center bg-white bg-opacity-80 rounded px-2 py-1 text-xs">
                                                    <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" class="mr-1">
                                                    Remove
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Check "Remove" to delete images when updating</p>
                            </div>
                        @endif

                        <!-- File Upload Option -->
                        <div class="mb-6">
                            <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Upload New Image Files
                                </span>
                                <span class="text-gray-400 text-xs block mt-1">JPEG, PNG, JPG, GIF - Max 2MB each, select multiple files</span>
                            </label>
                            <input type="file" id="images" name="images[]" accept="image/jpeg,image/png,image/jpg,image/gif" multiple onchange="previewImages(this)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('images')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @error('images.*')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <!-- Image Previews -->
                            <div id="imagesPreview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
                            <div id="imageFileInfo" class="mt-2 text-xs text-gray-500"></div>
                        </div>

                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-300"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-gray-50 text-gray-500">OR</span>
                            </div>
                        </div>

                        <!-- URL Input Option -->
                        <div class="mt-6">
                            <label for="image_urls" class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                    </svg>
                                    Add Image URLs
                                </span>
                                <span class="text-gray-400 text-xs block mt-1">External image links, separate multiple URLs with commas</span>
                            </label>
                            <input type="text" id="image_urls" name="image_urls" value="{{ old('image_urls') }}" onchange="previewImageUrls(this)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                placeholder="https://example.com/image1.jpg, https://example.com/image2.png">
                            @error('image_urls')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <!-- URL Image Previews -->
                            <div id="urlImagesPreview" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4"></div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="{{ route('products.index') }}" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Update Product
                        </button>
                    </div>
                
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });

    // Preview multiple uploaded image files
    function previewImages(input) {
        const imagesPreview = document.getElementById('imagesPreview');
        const imageFileInfo = document.getElementById('imageFileInfo');
        imagesPreview.innerHTML = '';
        imageFileInfo.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            let totalSize = 0;
            let validFiles = 0;
            
            // Display file count
            imageFileInfo.innerHTML = `Selected ${input.files.length} file(s)`;
            
            Array.from(input.files).forEach((file, index) => {
                totalSize += file.size;
                
                // Check file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-600 text-xs mt-1';
                    errorDiv.textContent = `File "${file.name}" is too large (${(file.size / 1024 / 1024).toFixed(2)}MB). Max 2MB allowed.`;
                    imageFileInfo.appendChild(errorDiv);
                    return;
                }
                
                // Check file type
                if (!file.type.match(/^image\/(jpeg|png|jpg|gif)$/)) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-600 text-xs mt-1';
                    errorDiv.textContent = `File "${file.name}" is not a valid image type.`;
                    imageFileInfo.appendChild(errorDiv);
                    return;
                }
                
                validFiles++;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'relative group';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-24 object-cover rounded-lg border border-gray-200 shadow-sm';
                    
                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center';
                    
                    const fileName = document.createElement('span');
                    fileName.className = 'text-white text-xs text-center px-2';
                    fileName.textContent = file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name;
                    
                    overlay.appendChild(fileName);
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(overlay);
                    imagesPreview.appendChild(previewContainer);
                };
                reader.readAsDataURL(file);
            });
            
            // Show summary
            const summaryDiv = document.createElement('div');
            summaryDiv.className = 'text-green-600 text-xs mt-1';
            summaryDiv.textContent = `${validFiles} valid file(s) ready to upload. Total size: ${(totalSize / 1024 / 1024).toFixed(2)}MB`;
            imageFileInfo.appendChild(summaryDiv);
        }
        
        // Clear URL input and previews
        document.getElementById('image_urls').value = '';
        document.getElementById('urlImagesPreview').innerHTML = '';
    }

    // Preview multiple image URLs
    function previewImageUrls(input) {
        const urlImagesPreview = document.getElementById('urlImagesPreview');
        urlImagesPreview.innerHTML = '';
        
        const urls = input.value.split(',').map(u => u.trim()).filter(u => u);
        
        if (urls.length > 0) {
            urls.forEach((url, index) => {
                if (isValidUrl(url)) {
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'relative group';
                    
                    const img = document.createElement('img');
                    img.src = url;
                    img.className = 'w-full h-24 object-cover rounded-lg border border-gray-200 shadow-sm';
                    img.onerror = function() {
                        this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTMgM1YyMUgyMVYzSDNaTTEwIDEyTDEzLjUgMTguNUwxNiAxNUwyMCAyMUg0TDEwIDEyWiIgZmlsbD0iI0Q0RUREV0EiLz4KPC9zdmc+Cg==';
                        this.className += ' opacity-50';
                    };
                    
                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center';
                    
                    const urlText = document.createElement('span');
                    urlText.className = 'text-white text-xs text-center px-2';
                    urlText.textContent = `URL ${index + 1}`;
                    
                    overlay.appendChild(urlText);
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(overlay);
                    urlImagesPreview.appendChild(previewContainer);
                }
            });
        }
        
        // Clear file input and previews
        document.getElementById('images').value = '';
        document.getElementById('imagesPreview').innerHTML = '';
    }

    // Helper function to validate URL
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    // Form submission handler for debugging
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const formData = new FormData(this);
            const imageFiles = formData.getAll('images[]');
            
            console.log('Form submission:');
            console.log('Image files count:', imageFiles.length);
            console.log('Image files:', imageFiles);
            console.log('Image URLs:', formData.get('image_urls'));
            console.log('Remove image IDs:', formData.getAll('remove_image_ids[]'));
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Updating...';
            }
        });
    });
</script>
@endsection
