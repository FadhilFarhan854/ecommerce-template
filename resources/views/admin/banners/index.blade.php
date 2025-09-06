@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Banner Management</h2>
                <a href="{{ route('admin.banners.create') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    Tambah Banner
                </a>
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="py-2 px-4 border-b text-left">ID</th>
                            <th class="py-2 px-4 border-b text-left">Gambar</th>
                            <th class="py-2 px-4 border-b text-left">Status</th>
                            <th class="py-2 px-4 border-b text-left">Dibuat</th>
                            <th class="py-2 px-4 border-b text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-4 border-b">{{ $banner->id }}</td>
                                <td class="py-2 px-4 border-b">
                                    @if($banner->image)
                                        <img src="{{ asset('storage/' . $banner->image) }}" 
                                             alt="Banner" 
                                             class="w-20 h-12 object-cover rounded">
                                    @else
                                        <span class="text-gray-500">No Image</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border-b">
                                    <span class="px-2 py-1 rounded text-sm {{ $banner->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $banner->status ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 border-b">{{ $banner->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-4 border-b">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.banners.edit', $banner) }}" 
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-2 py-1 rounded transition duration-300">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus banner ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded transition duration-300">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-center text-gray-500">
                                    Belum ada banner yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
