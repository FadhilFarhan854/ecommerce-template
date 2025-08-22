@extends('layouts.app')

@section('title', 'Katalog Produk - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Products</h1>
            <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                Add New Product
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Filter by Category</label>
                            <select name="category_id" id="category_id" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Search products..." value="{{ request('search') }}">
                        </div>
                        <div class="md:col-span-2 flex items-end space-x-2">
                            <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                                Filter
                            </button>
                            <a href="{{ route('products.index') }}" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition duration-200">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                @if($products->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition duration-200 flex flex-col">
                                @if($product->image)
                                    <img src="{{ $product->image }}" class="w-full h-48 object-cover rounded-t-lg" alt="{{ $product->name }}">
                                @else
                                    <div class="w-full h-48 bg-gray-100 rounded-t-lg flex items-center justify-center">
                                        <span class="text-gray-500">No Image</span>
                                    </div>
                                @endif
                                <div class="p-4 flex flex-col flex-grow">
                                    <h5 class="text-lg font-semibold text-gray-900 mb-1">{{ $product->name }}</h5>
                                    <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                                    <p class="text-gray-600 flex-grow mb-4">{{ Str::limit($product->description, 100) }}</p>
                                    <div class="mt-auto">
                                        <div class="flex justify-between items-center mb-3">
                                            <strong class="text-xl text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $product->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                Stock: {{ $product->stock }}
                                            </span>
                                        </div>
                                        <div class="flex space-x-1">
                                            
                                            <a href="{{ route('products.edit', $product) }}" 
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded text-center transition duration-200">
                                                Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                    class="w-full bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-2 rounded transition duration-200"
                                                    onclick="return confirm('Are you sure?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="flex justify-center mt-6">
                        {{ $products->links() }}
                    </div>
                @else
                    <p class="text-center text-gray-500 py-8">No products found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
