@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
            
            <div class="space-y-4">
                @foreach($cartItems as $item)
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <h3 class="font-medium">{{ $item->product->name }}</h3>
                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }} | {{ number_format($item->product->weight * $item->quantity, 2) }} kg</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">@ Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Berat:</span>
                    <span class="font-medium">{{ number_format($totalWeight, 2) }} kg</span>
                </div>
                <div class="flex justify-between items-center text-lg font-bold mt-2">
                    <span>Total Harga:</span>
                    <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Informasi Pengiriman</h2>

            <form id="checkoutForm">
                @csrf
                
                <!-- Address Selection -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Alamat:</label>
                    
                    <div class="space-y-2">
                        @if($addresses->count() > 0)
                            @foreach($addresses as $address)
                            <div class="border rounded-lg p-3">
                                <label class="flex items-start cursor-pointer">
                                    <input type="radio" name="address_option" value="existing" class="mt-1 mr-3" data-address-id="{{ $address->id }}">
                                    <div>
                                        <p class="font-medium">{{ $address->nama_depan }} {{ $address->nama_belakang }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->alamat }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->kelurahan }}, {{ $address->kecamatan }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->kota }}, {{ $address->provinsi }} {{ $address->kode_pos }}</p>
                                        <p class="text-sm text-gray-600">HP: {{ $address->hp }}</p>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        @endif

                        <!-- New Address Option -->
                        <div class="border rounded-lg p-3">
                            <label class="flex items-start cursor-pointer">
                                <input type="radio" name="address_option" value="new" class="mt-1 mr-3">
                                <span class="font-medium">Gunakan alamat baru</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- New Address Form (Hidden by default) -->
                <div id="newAddressForm" class="space-y-4" style="display: none;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Depan *</label>
                            <input type="text" name="nama_depan" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Belakang *</label>
                            <input type="text" name="nama_belakang" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap *</label>
                        <textarea name="alamat" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan *</label>
                            <input type="text" name="kelurahan" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan *</label>
                            <input type="text" name="kecamatan" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi *</label>
                            <select name="provinsi" id="provinsiSelect" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                            <input type="hidden" name="provinsi_id" id="provinsiIdHidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota *</label>
                            <select name="kota" id="kotaSelect" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                                <option value="">Pilih Kota</option>
                            </select>
                            <input type="hidden" name="kota_id" id="kotaIdHidden">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos *</label>
                            <input type="text" name="kode_pos" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP *</label>
                            <input type="text" name="hp" class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                </div>

                <!-- Shipping Information (Hidden inputs) -->
                <input type="hidden" name="shipping_cost" id="shippingCostHidden">
                <input type="hidden" name="shipping_courier" id="shippingCourierHidden">
                <input type="hidden" name="shipping_service" id="shippingServiceHidden">
                <input type="hidden" name="shipping_description" id="shippingDescriptionHidden">
                <input type="hidden" name="shipping_etd" id="shippingEtdHidden">

                <input type="hidden" name="address_id" id="selectedAddressId">
                
                <!-- Shipping Calculation Section -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg" id="shippingSection" style="display: none;">
                    <h3 class="text-lg font-semibold mb-4">Pilih Metode Pengiriman</h3>
                    
                    <div class="mb-4">
                        <button type="button" id="calculateShippingBtn" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-calculator"></i> Hitung Ongkos Kirim
                        </button>
                    </div>
                    
                    <div id="shippingLoader" class="text-center py-4" style="display: none;">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                        <p class="text-gray-600">Menghitung ongkos kirim...</p>
                    </div>
                    
                    <div id="shippingOptions" class="space-y-3" style="display: none;">
                        <!-- Shipping options will be loaded here -->
                    </div>
                    
                    <div id="selectedShippingInfo" class="mt-4 p-3 bg-blue-50 rounded-md" style="display: none;">
                        <h4 class="font-semibold text-blue-800">Pengiriman Dipilih:</h4>
                        <div id="shippingDetails" class="text-blue-700"></div>
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Proses Checkout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg p-6 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p>Memproses checkout...</p>
    </div>
</div>

@endsection

@push('scripts')
<!-- Midtrans Snap.js -->
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}" onload="console.log('Snap.js loaded successfully')"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah Snap.js sudah loaded
    console.log('Checking Snap.js status...');
    console.log('window.snap:', typeof window.snap);
    console.log('Midtrans client key:', '{{ config("midtrans.client_key") }}');
    
    const addressOptions = document.querySelectorAll('input[name="address_option"]');
    const newAddressForm = document.getElementById('newAddressForm');
    const selectedAddressId = document.getElementById('selectedAddressId');
    const checkoutForm = document.getElementById('checkoutForm');
    const loadingModal = document.getElementById('loadingModal');

    console.log('Checkout form initialized');
    console.log('Address options found:', addressOptions.length);
    console.log('New address form:', newAddressForm);
    console.log('Checkout form:', checkoutForm);

    // Handle address option change
    addressOptions.forEach(option => {
        option.addEventListener('change', function() {
            console.log('Address option changed:', this.value);
            const newAddressInputs = newAddressForm.querySelectorAll('input, textarea');
            
            if (this.value === 'new') {
                newAddressForm.style.display = 'block';
                selectedAddressId.value = '';
                // Enable all new address form inputs
                newAddressInputs.forEach(input => {
                    input.disabled = false;
                });
                console.log('Showing new address form');
            } else {
                newAddressForm.style.display = 'none';
                const addressId = this.dataset.addressId || '';
                selectedAddressId.value = addressId;
                // Disable and clear all new address form inputs
                newAddressInputs.forEach(input => {
                    input.disabled = true;
                    input.value = '';
                });
                        console.log('Hiding new address form, selected ID:', addressId);
                    }
                    
                    // Show shipping section if city is selected
                    checkShippingSection();
                });
            });
            
            // Handle province and city changes
            const provinsiSelect = document.getElementById('provinsiSelect');
            const kotaSelect = document.getElementById('kotaSelect');
            const provinsiIdHidden = document.getElementById('provinsiIdHidden');
            const kotaIdHidden = document.getElementById('kotaIdHidden');
            
            if (provinsiSelect) {
                loadProvinces();
                provinsiSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    provinsiIdHidden.value = selectedOption.value;
                    loadCities(this.value);
                    hideShippingSection();
                });
            }
            
            if (kotaSelect) {
                kotaSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    kotaIdHidden.value = selectedOption.value;
                    checkShippingSection();
                });
            }
            
            // Calculate shipping button
            const calculateShippingBtn = document.getElementById('calculateShippingBtn');
            if (calculateShippingBtn) {
                calculateShippingBtn.addEventListener('click', calculateShipping);
            }    // Handle form submission
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            // Validasi alamat dipilih
            const selectedAddressOption = document.querySelector('input[name="address_option"]:checked');
            if (!selectedAddressOption) {
                alert('Silakan pilih alamat pengiriman.');
                return;
            }
            
            // Validasi shipping untuk alamat baru
            if (selectedAddressOption.value === 'new') {
                const shippingCost = document.getElementById('shippingCostHidden').value;
                if (!shippingCost) {
                    alert('Silakan pilih metode pengiriman terlebih dahulu.');
                    return;
                }
            }
            
            if (loadingModal) {
                loadingModal.style.display = 'flex';
            }
            
            const formData = new FormData(this);
            
            // Jika alamat existing dipilih, hapus field alamat baru dari form data
            const addressOptionValue = formData.get('address_option');
            console.log('Selected address option:', addressOptionValue);
            
            if (addressOptionValue === 'existing') {
                const addressId = selectedAddressId.value;
                console.log('Address ID:', addressId);
                
                if (!addressId) {
                    alert('Silakan pilih alamat yang akan digunakan.');
                    if (loadingModal) {
                        loadingModal.style.display = 'none';
                    }
                    return;
                }
                
                // Set address_id di form data
                formData.set('address_id', addressId);
                
                const newAddressFields = ['nama_depan', 'nama_belakang', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'hp'];
                newAddressFields.forEach(field => {
                    formData.delete(field);
                });
            }
            
            // Debug form data
            console.log('Sending form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            fetch('{{ route("checkout.process") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (loadingModal) {
                    loadingModal.style.display = 'none';
                }
                
                if (data.success) {
                    // Proses pembayaran dengan Midtrans Snap
                    if (data.snap_token) {
                        console.log('Snap token received:', data.snap_token);
                        
                        // Function untuk membuka pembayaran
                        const openPayment = () => {
                            if (typeof window.snap !== 'undefined') {
                                console.log('Snap is loaded, opening payment popup...');
                                window.snap.pay(data.snap_token, {
                                    onSuccess: function(result) {
                                        console.log('Payment success:', result);
                                        alert('Pembayaran berhasil!');
                                        window.location.href = '/orders';
                                    },
                                    onPending: function(result) {
                                        console.log('Payment pending:', result);
                                        alert('Pembayaran pending. Silakan selesaikan pembayaran Anda.');
                                        window.location.href = '/orders';
                                    },
                                    onError: function(result) {
                                        console.log('Payment error:', result);
                                        alert('Pembayaran gagal. Silakan coba lagi.');
                                    },
                                    onClose: function() {
                                        console.log('Payment popup closed');
                                        alert('Anda menutup popup pembayaran. Anda dapat melanjutkan pembayaran melalui halaman pesanan.');
                                        window.location.href = '/orders';
                                    }
                                });
                            } else {
                                console.error('Snap.js not loaded yet, retrying...');
                                setTimeout(openPayment, 500); // Retry setelah 500ms
                            }
                        };
                        
                        // Mulai proses pembayaran
                        openPayment();
                        
                    } else {
                        console.log('No snap token received');
                        alert('Checkout berhasil! Pesanan Anda sedang diproses.');
                        window.location.href = data.redirect_url || '/orders';
                    }
                } else {
                    let errorMessage = 'Error: ' + data.message;
                    if (data.errors) {
                        console.log('Validation errors:', data.errors);
                        errorMessage += '\n\nDetail error:';
                        for (let field in data.errors) {
                            errorMessage += '\n- ' + data.errors[field].join(', ');
                        }
                    }
                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                if (loadingModal) {
                    loadingModal.style.display = 'none';
                }
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    } else {
        console.error('Checkout form not found!');
    }
    
    // Shipment functions
    let selectedShipping = null;
    const storeLocation = 501; // Default store location (Yogyakarta)
    const totalWeight = {{ $totalWeight * 1000 ?? 1000 }}; // Convert to grams
    
    async function loadProvinces() {
        try {
            const response = await fetch('/api/shipment/provinces');
            const data = await response.json();
            
            if (data.success) {
                const select = document.getElementById('provinsiSelect');
                select.innerHTML = '<option value="">Pilih Provinsi</option>';
                
                data.data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.province_id;
                    option.textContent = province.province;
                    option.dataset.provinceName = province.province;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }
    
    async function loadCities(provinceId) {
        const kotaSelect = document.getElementById('kotaSelect');
        kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
        
        if (!provinceId) return;
        
        try {
            const response = await fetch(`/api/shipment/cities?province_id=${provinceId}`);
            const data = await response.json();
            
            if (data.success) {
                data.data.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.city_id;
                    option.textContent = `${city.type} ${city.city_name}`;
                    option.dataset.cityName = city.city_name;
                    option.dataset.cityType = city.type;
                    kotaSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading cities:', error);
        }
    }
    
    function checkShippingSection() {
        const kotaSelect = document.getElementById('kotaSelect');
        const shippingSection = document.getElementById('shippingSection');
        const selectedAddressOption = document.querySelector('input[name="address_option"]:checked');
        
        if (selectedAddressOption && selectedAddressOption.value === 'new' && kotaSelect && kotaSelect.value) {
            shippingSection.style.display = 'block';
        } else {
            hideShippingSection();
        }
    }
    
    function hideShippingSection() {
        const shippingSection = document.getElementById('shippingSection');
        const shippingOptions = document.getElementById('shippingOptions');
        const selectedShippingInfo = document.getElementById('selectedShippingInfo');
        
        shippingSection.style.display = 'none';
        shippingOptions.style.display = 'none';
        selectedShippingInfo.style.display = 'none';
        selectedShipping = null;
    }
    
    async function calculateShipping() {
        const kotaSelect = document.getElementById('kotaSelect');
        const destination = kotaSelect.value;
        
        if (!destination) {
            alert('Pilih kota tujuan terlebih dahulu');
            return;
        }
        
        showShippingLoader(true);
        
        try {
            const response = await fetch('/api/shipment/compare-costs', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    origin: storeLocation,
                    destination: parseInt(destination),
                    weight: totalWeight,
                    couriers: ['jne', 'pos', 'tiki']
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                displayShippingOptions(data.data);
            } else {
                showShippingError(data.message || 'Gagal menghitung ongkos kirim');
            }
        } catch (error) {
            console.error('Error calculating shipping:', error);
            showShippingError('Terjadi kesalahan saat menghitung ongkos kirim');
        } finally {
            showShippingLoader(false);
        }
    }
    
    function displayShippingOptions(couriers) {
        const container = document.getElementById('shippingOptions');
        container.innerHTML = '';
        
        if (!couriers || couriers.length === 0) {
            container.innerHTML = '<div class="text-red-600">Tidak ada opsi pengiriman yang tersedia</div>';
            container.style.display = 'block';
            return;
        }
        
        couriers.forEach(courier => {
            courier.services.forEach(service => {
                const option = document.createElement('div');
                option.className = 'border rounded-lg p-3 cursor-pointer hover:bg-blue-50 transition';
                option.innerHTML = `
                    <label class="flex items-center cursor-pointer w-full">
                        <input type="radio" name="shipping_option" value="${service.cost}" 
                               data-courier="${courier.courier_code}"
                               data-service="${service.service}"
                               data-description="${service.description}"
                               data-etd="${service.etd}"
                               class="mr-3">
                        <div class="flex-1">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-semibold text-gray-800">${courier.courier_name}</h4>
                                    <p class="text-sm text-gray-600">${service.service} - ${service.description}</p>
                                    <p class="text-xs text-gray-500">Estimasi: ${service.etd} hari</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-blue-600">Rp ${service.cost.toLocaleString('id-ID')}</p>
                                </div>
                            </div>
                        </div>
                    </label>
                `;
                
                const radio = option.querySelector('input[type="radio"]');
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        selectShipping(this);
                        
                        // Remove previous selection styling
                        document.querySelectorAll('#shippingOptions > div').forEach(div => {
                            div.classList.remove('bg-blue-50', 'border-blue-500');
                            div.classList.add('border-gray-300');
                        });
                        
                        // Add selection styling
                        option.classList.add('bg-blue-50', 'border-blue-500');
                        option.classList.remove('border-gray-300');
                    }
                });
                
                option.classList.add('border-gray-300');
                container.appendChild(option);
            });
        });
        
        container.style.display = 'block';
    }
    
    function selectShipping(radio) {
        selectedShipping = {
            cost: parseInt(radio.value),
            courier: radio.dataset.courier,
            service: radio.dataset.service,
            description: radio.dataset.description,
            etd: radio.dataset.etd
        };
        
        // Update hidden inputs for form submission
        document.getElementById('shippingCostHidden').value = selectedShipping.cost;
        document.getElementById('shippingCourierHidden').value = selectedShipping.courier;
        document.getElementById('shippingServiceHidden').value = selectedShipping.service;
        document.getElementById('shippingDescriptionHidden').value = selectedShipping.description;
        document.getElementById('shippingEtdHidden').value = selectedShipping.etd;
        
        // Update selected shipping info
        const selectedShippingInfo = document.getElementById('selectedShippingInfo');
        const shippingDetails = document.getElementById('shippingDetails');
        
        shippingDetails.innerHTML = `
            <p><strong>${selectedShipping.courier.toUpperCase()} - ${selectedShipping.service}</strong></p>
            <p>${selectedShipping.description}</p>
            <p>Biaya: <strong>Rp ${selectedShipping.cost.toLocaleString('id-ID')}</strong></p>
            <p>Estimasi: <strong>${selectedShipping.etd} hari</strong></p>
        `;
        
        selectedShippingInfo.style.display = 'block';
        console.log('Shipping selected:', selectedShipping);
    }
    
    function showShippingLoader(show) {
        const loader = document.getElementById('shippingLoader');
        const options = document.getElementById('shippingOptions');
        
        if (show) {
            loader.style.display = 'block';
            options.style.display = 'none';
        } else {
            loader.style.display = 'none';
        }
    }
    
    function showShippingError(message) {
        const container = document.getElementById('shippingOptions');
        container.innerHTML = `<div class="text-red-600 p-3 bg-red-50 rounded-lg">${message}</div>`;
        container.style.display = 'block';
    }
});
</script>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
