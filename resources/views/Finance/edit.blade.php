@extends('layouts.app')

@section('title', 'Edit Outcome')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="{{ route('finance.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Outcome</h1>
                    <p class="mt-2 text-gray-600">Update outcome information</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Outcome Details</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('finance.update', $outcome->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" id="description" name="description" value="{{ old('description', $outcome->description) }}" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                               placeholder="Enter outcome description">
                        @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (Rp)</label>
                        <input type="number" id="amount" name="amount" value="{{ old('amount', $outcome->amount) }}" required step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                               placeholder="Enter amount">
                        @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('finance.index') }}" class="px-6 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                            Update Outcome
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
