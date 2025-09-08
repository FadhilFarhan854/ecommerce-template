@extends('layouts.app')

@section('title', 'Tambah Diskon')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Tambah Diskon</h1>
            <a href="{{ route('admin.discounts.index') }}" class="text-gray-600 hover:text-gray-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.discounts.store') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Produk</label>
                <select name="product_id" id="product_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} - Rp {{ number_format($product->price, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="percentage" class="block text-sm font-medium text-gray-700 mb-2">Persentase Diskon (%)</label>
                <input type="number" name="percentage" id="percentage" min="1" max="100" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="{{ old('percentage') }}" required>
            </div>

            <div class="mb-4">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai (Opsional)</label>
                <input type="date" name="start_date" id="start_date" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="{{ old('start_date') }}">
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika diskon langsung aktif</p>
            </div>

            <div class="mb-6">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir (Opsional)</label>
                <input type="date" name="end_date" id="end_date" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       value="{{ old('end_date') }}">
                <p class="text-xs text-gray-500 mt-1">Kosongkan jika diskon tidak memiliki batas waktu</p>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} 
                           class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-sm text-gray-700">Aktifkan diskon</span>
                </label>
            </div>

            <div class="flex space-x-4">
                <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md transition duration-200">
                    Simpan Diskon
                </button>
                <a href="{{ route('admin.discounts.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-4 rounded-md text-center transition duration-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    startDateInput.addEventListener('change', function() {
        if (this.value) {
            endDateInput.min = this.value;
        }
    });
    
    endDateInput.addEventListener('change', function() {
        if (this.value && startDateInput.value) {
            if (this.value < startDateInput.value) {
                alert('Tanggal berakhir tidak boleh lebih awal dari tanggal mulai');
                this.value = '';
            }
        }
    });
});
</script>
@endsection
