@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Tambah Banner</h2>
                <a href="{{ route('admin.banners.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Kembali
                </a>
            </div>
        </div>

        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">
                        Gambar Banner *
                    </label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required
                           onchange="previewImage(this)">
                    <p class="text-gray-500 text-xs mt-1">Format: JPEG, PNG, JPG, GIF, WEBP. Maksimal 5MB.</p>
                    
                    <!-- Preview gambar -->
                    <div id="imagePreview" class="mt-3 hidden">
                        <p class="text-sm text-gray-600 mb-2">Preview:</p>
                        <img id="preview" src="" alt="Preview" class="w-48 h-28 object-cover rounded border shadow-sm">
                    </div>
                </div>

                <script>
                function previewImage(input) {
                    const preview = document.getElementById('preview');
                    const previewDiv = document.getElementById('imagePreview');
                    
                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            previewDiv.classList.remove('hidden');
                        }
                        
                        reader.readAsDataURL(input.files[0]);
                    } else {
                        previewDiv.classList.add('hidden');
                    }
                }
                </script>

                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="status" 
                               id="status"
                               value="1" 
                               {{ old('status', true) ? 'checked' : '' }}
                               class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="status" class="text-gray-700 text-sm font-bold">
                            Banner Aktif
                        </label>
                    </div>
                    <p class="text-gray-500 text-xs mt-1">Centang untuk mengaktifkan banner di halaman utama.</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('admin.banners.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                        Batal
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
