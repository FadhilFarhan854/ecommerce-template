@extends('layouts.app')

@section('title', 'Categories - ' . config('app.name'))

@section('content')
<div class="max-w-6xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">Categories</h1>
        <a href="{{ route('categories.create') }}" 
           class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
            Add New Category
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-xl overflow-hidden">
        <div class="p-4">
            @if($categories->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead class="bg-gray-100 text-gray-700 text-sm">
                            <tr>
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2">Name</th>
                                <th class="px-4 py-2">Slug</th>
                                <th class="px-4 py-2">Products Count</th>
                                <th class="px-4 py-2">Created At</th>
                                <th class="px-4 py-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($categories as $category)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $category->id }}</td>
                                    <td class="px-4 py-2 font-medium">{{ $category->name }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $category->slug }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $category->products->count() }}</td>
                                    <td class="px-4 py-2 text-gray-600">
                                        {{ $category->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('categories.show', $category) }}" 
                                               class="px-3 py-1 rounded-md text-sm bg-blue-100 text-blue-700 hover:bg-blue-200">
                                                View
                                            </a>
                                            <a href="{{ route('categories.edit', $category) }}" 
                                               class="px-3 py-1 rounded-md text-sm bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                                Edit
                                            </a>
                                            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 rounded-md text-sm bg-red-100 text-red-700 hover:bg-red-200">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500 py-6">No categories found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
