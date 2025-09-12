@extends('layouts.app')

@section('title', 'Edit Address - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">Edit Address</h1>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('addresses.update', $address) }}" id="addressForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="nama_depan" class="block text-sm font-medium text-gray-700 mb-2">Nama Depan *</label>
                    <input type="text" id="nama_depan" name="nama_depan" value="{{ old('nama_depan', $address->nama_depan) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                    @error('nama_depan')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="nama_belakang" class="block text-sm font-medium text-gray-700 mb-2">Nama Belakang *</label>
                    <input type="text" id="nama_belakang" name="nama_belakang" value="{{ old('nama_belakang', $address->nama_belakang) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                    @error('nama_belakang')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap *</label>
                <textarea id="alamat" name="alamat" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 resize-vertical min-h-[100px]" 
                          placeholder="Contoh: Jl. Sudirman No. 123, RT 01/RW 02" required>{{ old('alamat', $address->alamat) }}</textarea>
                @error('alamat')
                    <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="kelurahan" class="block text-sm font-medium text-gray-700 mb-2">Kelurahan *</label>
                    <input type="text" id="kelurahan" name="kelurahan" value="{{ old('kelurahan', $address->kelurahan) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                    @error('kelurahan')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-2">Kecamatan *</label>
                    <input type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $address->kecamatan) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                    @error('kecamatan')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-2">Provinsi *</label>
                    <select id="provinsi" name="provinsi" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required>
                        <option value="">Pilih Provinsi</option>
                    </select>
                    <input type="hidden" id="provinsi_name" name="provinsi_name" value="{{ old('provinsi_name', $address->provinsi_name) }}">
                    @error('provinsi')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="kota" class="block text-sm font-medium text-gray-700 mb-2">Kota/Kabupaten *</label>
                    <select id="kota" name="kota" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" required disabled>
                        <option value="">Pilih Kota/Kabupaten</option>
                    </select>
                    <input type="hidden" id="kota_name" name="kota_name" value="{{ old('kota_name', $address->kota_name) }}">
                    @error('kota')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="kode_pos" class="block text-sm font-medium text-gray-700 mb-2">Kode Pos *</label>
                    <input type="text" id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $address->kode_pos) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                           placeholder="12345" maxlength="5" pattern="[0-9]{5}" required>
                    @error('kode_pos')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="hp" class="block text-sm font-medium text-gray-700 mb-2">Nomor HP</label>
                    <input type="text" id="hp" name="hp" value="{{ old('hp', $address->hp) }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200" 
                           placeholder="08123456789">
                    @error('hp')
                        <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                    Update Address
                </button>
                <a href="{{ route('addresses.index') }}" 
                   class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition duration-200 text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinsiSelect = document.getElementById('provinsi');
    const kotaSelect = document.getElementById('kota');
    const provinsiNameInput = document.getElementById('provinsi_name');
    const kotaNameInput = document.getElementById('kota_name');

    let provinces = [];
    let cities = [];

    // Current address data
    const currentProvinsi = '{{ old("provinsi", $address->provinsi) }}';
    const currentKota = '{{ old("kota", $address->kota) }}';

    // Load provinces on page load
    loadProvinces();

    async function loadProvinces() {
        try {
            provinsiSelect.innerHTML = '<option value="">Loading...</option>';
            
            const response = await fetch('/api/wilayah/provinces');
            
            if (!response.ok) {
                throw new Error('Failed to load provinces');
            }
            
            const provinces = await response.json();
            
            provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
            provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                option.dataset.name = province.name;
                if (province.code == currentProvinsi) {
                    option.selected = true;
                }
                provinsiSelect.appendChild(option);
            });

            // If we have a current province, load its cities
            if (currentProvinsi) {
                await loadCities(currentProvinsi);
            }
            
        } catch (error) {
            console.error('Error loading provinces:', error);
            provinsiSelect.innerHTML = '<option value="">Error loading provinces</option>';
            showError('Gagal memuat data provinsi. Silakan refresh halaman.');
        }
    }

    async function loadCities(provinceCode) {
        try {
            kotaSelect.innerHTML = '<option value="">Loading...</option>';
            kotaSelect.disabled = true;
            
            const response = await fetch(`/api/wilayah/regencies/${provinceCode}`);
            
            if (!response.ok) {
                throw new Error('Failed to load cities');
            }
            
            const cities = await response.json();
            
            kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.code;
                option.textContent = city.name;
                option.dataset.name = city.name;
                if (city.code == currentKota) {
                    option.selected = true;
                }
                kotaSelect.appendChild(option);
            });
            
            kotaSelect.disabled = false;
            
        } catch (error) {
            console.error('Error loading cities:', error);
            kotaSelect.innerHTML = '<option value="">Error loading cities</option>';
            kotaSelect.disabled = false;
            showError('Gagal memuat data kota/kabupaten. Silakan pilih provinsi lagi.');
        }
    }

    // Province change handler
    provinsiSelect.addEventListener('change', function() {
        const provinceCode = this.value;
        
        // Reset city selection
        kotaSelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
        kotaSelect.disabled = true;
        kotaNameInput.value = '';
        
        if (provinceCode) {
            // Set province name
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.name) {
                provinsiNameInput.value = selectedOption.dataset.name;
            }
            
            // Load cities for selected province
            loadCities(provinceCode);
        } else {
            provinsiNameInput.value = '';
        }
    });

    // City change handler
    kotaSelect.addEventListener('change', function() {
        const cityCode = this.value;
        
        if (cityCode) {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.dataset.name) {
                kotaNameInput.value = selectedOption.dataset.name;
            }
        } else {
            kotaNameInput.value = '';
        }
    });

    // Form submission handler
    document.getElementById('addressForm').addEventListener('submit', function(e) {
        // Validate that province and city names are set
        if (!provinsiNameInput.value || !kotaNameInput.value) {
            e.preventDefault();
            showError('Silakan pilih provinsi dan kota/kabupaten dari dropdown yang tersedia.');
            return false;
        }
    });

    // Error message function
    function showError(message) {
        // Remove existing error messages
        const existingError = document.querySelector('.wilayah-error');
        if (existingError) {
            existingError.remove();
        }

        // Create new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'wilayah-error bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4';
        errorDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <span>${message}</span>
            </div>
        `;

        // Insert after the form title
        const title = document.querySelector('h1');
        title.parentNode.insertBefore(errorDiv, title.nextSibling);

        // Auto remove after 10 seconds
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 10000);
    }
});
</script>
@endsection
