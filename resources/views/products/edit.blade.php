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
                {{-- ...existing code... --}}
                {{-- Product Images --}}
                <div>
                    <label class="block font-medium mb-2">Product Images</label>
                    {{-- Current Images --}}
                    @if($product->images && $product->images->count() > 0)
                        <div class="mb-3 flex gap-2 flex-wrap">
                            <p class="text-sm text-gray-500 mb-1 w-full">Current Images:</p>
                            @foreach($product->images as $image)
                                <div style="position:relative;display:inline-block;">
                                    <img src="{{ $image->url }}" alt="Current product image" class="rounded-lg shadow max-w-[120px] max-h-[120px]">
                                    <input type="checkbox" name="remove_image_ids[]" value="{{ $image->id }}" style="position:absolute;top:4px;right:4px;z-index:2;">
                                    <span style="position:absolute;top:4px;right:24px;background:#fff;padding:2px 6px;border-radius:4px;font-size:12px;">Remove</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Upload New Images --}}
                    <div class="mb-3">
                        <label for="images" class="block font-medium">Upload New Images
                            <span class="text-sm text-gray-500">(JPEG, PNG, GIF - Max 2MB each, you can select multiple)</span>
                        </label>
                        <input type="file" id="images" name="images[]" accept="image/*" multiple onchange="previewImages(this)"
                            class="w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                        @error('images')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div id="imagesPreview" class="mt-3 flex gap-2 flex-wrap"></div>
                    </div>

                    <div class="text-center text-gray-400 my-2">OR</div>

                    {{-- Image URLs --}}
                    <div>
                        <label for="image_urls" class="block font-medium">Image URLs
                            <span class="text-sm text-gray-500">(External links, separate by comma)</span>
                        </label>
                        <input type="text" id="image_urls" name="image_urls" value="{{ old('image_urls') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://img1.jpg, https://img2.png" onchange="previewImageUrls(this)">
                        @error('image_urls')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div id="urlImagesPreview" class="mt-3 flex gap-2 flex-wrap"></div>
                    </div>
                </div>
                {{-- ...existing code... --}}
            </form>
        </div>
    </div>
</div>

{{-- Auto Slug --}}
<script>
    // Preview multiple uploaded image files
    function previewImages(input) {
        const imagesPreview = document.getElementById('imagesPreview');
        imagesPreview.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'max-w-xs max-h-48 object-cover rounded';
                    imagesPreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
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
        urls.forEach(url => {
            const img = document.createElement('img');
            img.src = url;
            img.className = 'max-w-xs max-h-48 object-cover rounded';
            urlImagesPreview.appendChild(img);
        });
        // Clear file input and previews
        document.getElementById('images').value = '';
        document.getElementById('imagesPreview').innerHTML = '';
    }
    document.getElementById('name').addEventListener('input', function(e) {
        const slug = e.target.value.toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
        document.getElementById('slug').value = slug;
    });
</script>
@endsection
