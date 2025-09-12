@extends('layouts.app')

@section('title', 'Profile - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <!-- Header with Gradient Background -->
            <div class="bg-gradient-to-br from-blue-600 to-green-500 px-6 py-12 text-center">
                <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 text-white text-3xl font-bold backdrop-blur-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h1 class="text-2xl lg:text-3xl font-bold text-white mb-2">{{ $user->name }}</h1>
                <p class="text-blue-100 text-lg">{{ $user->email }}</p>
                <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm bg-white/20 text-white backdrop-blur-sm">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    {{ ucfirst($user->role) }}
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Personal Information Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">Informasi Personal</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-600">Nama Lengkap</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900 font-medium">
                        {{ $user->name }}
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-600">Email</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900 font-medium">
                        {{ $user->email }}
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-600">Nomor Telepon</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900 font-medium">
                        {{ $user->phone ?? 'Belum diisi' }}
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-600">Bergabung Sejak</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900 font-medium">
                        {{ $user->created_at->format('d F Y') }}
                    </div>
                </div>
                
                <div class="space-y-2 md:col-span-2">
                    <label class="text-sm font-medium text-gray-600">Alamat</label>
                    <div class="bg-gray-50 rounded-lg px-4 py-3 text-gray-900 font-medium">
                        {{ $user->address ?? 'Belum diisi' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">Pengaturan Akun</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('profile.edit') }}" 
                   class="group bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl text-center">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                        </svg>
                        Edit Profile
                    </div>
                </a>
                
                <a href="{{ route('profile.change-password') }}" 
                   class="group bg-gradient-to-br from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl text-center">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        Ubah Password
                    </div>
                </a>
                
                <a href="{{ route('products.catalog') }}" 
                   class="group bg-gradient-to-br from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-xl text-center sm:col-span-2 lg:col-span-1">
                    <div class="flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Kembali ke Katalog
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
