@extends('layouts.app')

@section('title', 'Manage Addresses - ' . config('app.name'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Addresses</h1>
            <a href="{{ route('addresses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                Add New Address
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

        {{-- Address Cards --}}
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                @if(isset($addresses) && $addresses->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($addresses as $address)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $address->nama_depan }} {{ $address->nama_belakang }}
                                    </h3>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('addresses.edit', $address) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="text-red-600 hover:text-red-800 text-sm"
                                                onclick="return confirm('Are you sure you want to delete this address?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="text-gray-600 space-y-1">
                                    <p>{{ $address->alamat }}</p>
                                    <p>{{ $address->kelurahan }}, {{ $address->kecamatan }}</p>
                                    <p>{{ $address->kota }}, {{ $address->provinsi }} {{ $address->kode_pos }}</p>
                                    @if($address->hp)
                                        <p class="text-sm">📞 {{ $address->hp }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No addresses</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by adding your first address.</p>
                        <div class="mt-6">
                            <a href="{{ route('addresses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Add Address
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Back to Profile --}}
        <div class="mt-6 text-center">
            <a href="{{ route('profile.show') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Profile
            </a>
        </div>
    </div>
</div>
@endsection
