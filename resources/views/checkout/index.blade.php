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
                        <p class="text-sm text-gray-600">
                            Qty: {{ $item->quantity }}
                            @if(config('shipment.use_shipment', true) && $item->product->weight)
                                | {{ number_format(($item->product->weight ?? 0) * $item->quantity, 2) }} kg
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">@ Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="border-t pt-4 mt-4">
                @if(config('shipment.use_shipment', true) && $totalWeight)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total Berat:</span>
                    <span class="font-medium">{{ number_format($totalWeight, 2) }} kg</span>
                </div>
                @endif
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
                @if(config('shipment.use_shipment', true))
                <div class="flex justify-between items-center mt-2" id="shippingCostRow" style="display: none;">
                    <span class="text-gray-600">Ongkos Kirim:</span>
                    <span class="font-medium" id="shippingCostDisplay">Rp 0</span>
                @else
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-600">Ongkos Kirim:</span>
                    <span class="font-medium text-green-600">-</span>
                @endif
                </div>
                <div class="flex justify-between items-center text-lg font-bold mt-3 pt-2 border-t">
                    <span>Total Bayar:</span>
                    <span id="grandTotalDisplay">Rp {{ number_format($totalPrice ?? 0, 0, ',', '.') }}</span>
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
                                    <input type="radio" name="address_option" value="existing" class="mt-1 mr-3" 
                                           data-address-id="{{ $address->id }}"
                                           data-city-id="{{ $address->kota }}"
                                           data-city-name="{{ $address->kota_name ?? $address->kota }}">
                                    <div>
                                        <p class="font-medium">{{ $address->nama_depan }} {{ $address->nama_belakang }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->alamat }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->kelurahan }}, {{ $address->kecamatan }}</p>
                                        <p class="text-sm text-gray-600">{{ $address->kota_name ?? $address->kota }}, {{ $address->provinsi_name ?? $address->provinsi }} {{ $address->kode_pos }}</p>
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
                            <input type="hidden" name="provinsi_name" id="provinsiNameHidden">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kota *</label>
                            <select name="kota" id="kotaSelect" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                                <option value="">Pilih Kota</option>
                            </select>
                            <input type="hidden" name="kota_name" id="kotaNameHidden">
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
                @if(config('shipment.use_shipment', true))
                <input type="hidden" name="shipping_cost" id="shippingCostHidden">
                <input type="hidden" name="shipping_courier" id="shippingCourierHidden">
                <input type="hidden" name="shipping_service" id="shippingServiceHidden">
                <input type="hidden" name="shipping_description" id="shippingDescriptionHidden">
                <input type="hidden" name="shipping_etd" id="shippingEtdHidden">
                @else
                <input type="hidden" name="shipping_cost" value="0">
                <input type="hidden" name="shipping_courier" value="free">
                <input type="hidden" name="shipping_service" value="Free Shipping">
                <input type="hidden" name="shipping_description" value="Gratis Ongkos Kirim">
                <input type="hidden" name="shipping_etd" value="2-3 hari">
                @endif
                <input type="hidden" name="grand_total" id="grandTotalHidden" value="{{ intval($totalPrice) }}">

                <input type="hidden" name="address_id" id="selectedAddressId">
                
                <!-- Shipping Information Display (for existing address) -->
                @if(config('shipment.use_shipment', true))
                <div id="existingAddressShipping" class="mt-4 p-3 bg-blue-50 rounded-md" style="display: none;">
                    <h4 class="font-semibold text-blue-800">Pengiriman Otomatis:</h4>
                    <div id="existingShippingDetails" class="text-blue-700 text-sm"></div>
                </div>
                
                <!-- Shipping Calculation Section -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg" id="shippingSection" style="display: none;">
                    <h3 class="text-lg font-semibold mb-4">Pilih Metode Pengiriman</h3>
                    
                    <!-- Courier Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kurir:</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <label class="flex items-center cursor-pointer p-2 border rounded-md hover:bg-blue-50">
                                <input type="radio" name="courier_selection" value="all" class="mr-2" checked>
                                <span class="text-sm">Semua</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-2 border rounded-md hover:bg-blue-50">
                                <input type="radio" name="courier_selection" value="jne" class="mr-2">
                                <span class="text-sm">JNE</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-2 border rounded-md hover:bg-blue-50">
                                <input type="radio" name="courier_selection" value="pos" class="mr-2">
                                <span class="text-sm">POS Indonesia</span>
                            </label>
                            <label class="flex items-center cursor-pointer p-2 border rounded-md hover:bg-blue-50">
                                <input type="radio" name="courier_selection" value="tiki" class="mr-2">
                                <span class="text-sm">TIKI</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-4" id="calculateShippingSection">
                        <button type="button" id="calculateShippingBtn" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                            <i class="fas fa-calculator"></i> 
                            <span>Hitung Ongkos Kirim</span>
                        </button>
                        <p class="text-sm text-gray-600 mt-2">Klik tombol di atas untuk menghitung ongkos kirim setelah memilih provinsi dan kota.</p>
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
               
                @endif

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

    // Initialize: disable required validation for new address form if it's hidden
    if (newAddressForm && newAddressForm.style.display === 'none') {
        const newAddressInputs = newAddressForm.querySelectorAll('input, select, textarea');
        newAddressInputs.forEach(input => {
            if (input.required) {
                input.setAttribute('data-required', 'true');
                input.required = false;
            }
            input.disabled = true;
        });
    }

    console.log('Checkout form initialized');
    console.log('Address options found:', addressOptions.length);
    console.log('New address form:', newAddressForm);
    console.log('Checkout form:', checkoutForm);

    // Handle address option change
    addressOptions.forEach(option => {
        option.addEventListener('change', function() {
            console.log('Address option changed:', this.value, this);
            const newAddressInputs = newAddressForm.querySelectorAll('input, select, textarea');
            
            if (this.value === 'new') {
                newAddressForm.style.display = 'block';
                selectedAddressId.value = '';
                // Enable all new address form inputs and restore required attributes
                newAddressInputs.forEach(input => {
                    input.disabled = false;
                    if (input.hasAttribute('data-required')) {
                        input.required = true;
                    }
                });
                console.log('Showing new address form');
                // Hide shipping section for new address until city is selected
                hideShippingSection();
                resetShippingCost();
            } else if (this.value === 'existing') {
                newAddressForm.style.display = 'none';
                // Disable required validation for hidden form
                newAddressInputs.forEach(input => {
                    if (input.required) {
                        input.setAttribute('data-required', 'true');
                        input.required = false;
                    }
                    input.disabled = true;
                });
                const addressId = this.dataset.addressId || '';
                const cityId = this.dataset.cityId || '';
                const cityName = this.dataset.cityName || '';
                
                console.log('Address data:', {
                    addressId: addressId,
                    cityId: cityId,
                    cityName: cityName,
                    dataset: this.dataset
                });
                
                selectedAddressId.value = addressId;
                // Disable and clear all new address form inputs
                newAddressInputs.forEach(input => {
                    input.disabled = true;
                    input.value = '';
                });
                console.log('Hiding new address form, selected ID:', addressId, 'City ID:', cityId);
                
                // Auto-calculate shipping for existing address
                if (cityId && cityId !== '') {
                    console.log('Starting auto-calculate shipping...');
                    autoCalculateShippingForAddress(cityId, cityName);
                } else {
                    console.warn('No city ID found for address:', addressId);
                    resetShippingCost();
                }
            }
            
            // Show shipping section if city is selected
            checkShippingSection();
        });
    });
            
            // Handle province and city changes
            const provinsiSelect = document.getElementById('provinsiSelect');
            const kotaSelect = document.getElementById('kotaSelect');
            const provinsiNameHidden = document.getElementById('provinsiNameHidden');
            const kotaNameHidden = document.getElementById('kotaNameHidden');
            
            if (provinsiSelect) {
                loadProvinces();
                provinsiSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.provinceName) {
                        provinsiNameHidden.value = selectedOption.dataset.provinceName;
                    }
                    loadCities(this.value);
                    hideShippingSection();
                    
                    // Reset city selection and name
                    kotaSelect.value = '';
                    kotaNameHidden.value = '';
                });
            }
            
            if (kotaSelect) {
                kotaSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.cityName) {
                        kotaNameHidden.value = selectedOption.dataset.cityName;
                    }
                    checkShippingSection();
                });
            }
            
            // Calculate shipping button (only if shipment is enabled)
            const calculateShippingBtn = document.getElementById('calculateShippingBtn');
            if (calculateShippingBtn && shipmentEnabled) {
                calculateShippingBtn.addEventListener('click', calculateShipping);
            }
            
            // Handle courier selection change
            const courierRadios = document.querySelectorAll('input[name="courier_selection"]');
            courierRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Reset shipping options when courier selection changes
                    hideShippingOptions();
                });
            });    // Handle form submission
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
            
            // Re-enable required validation for new address form if selected
            if (selectedAddressOption.value === 'new') {
                const newAddressInputs = newAddressForm.querySelectorAll('input, select, textarea');
                newAddressInputs.forEach(input => {
                    if (input.hasAttribute('data-required')) {
                        input.required = true;
                        input.disabled = false;
                    }
                });
            }
            
            if (loadingModal) {
                loadingModal.style.display = 'flex';
            }
            
            const formData = new FormData(this);
            
            // Debug form data sebelum dikirim
            console.log('Form data before submission:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value} (type: ${typeof value})`);
            }
            
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
                
                const newAddressFields = ['nama_depan', 'nama_belakang', 'alamat', 'kelurahan', 'kecamatan', 'kota', 'kota_name', 'provinsi', 'provinsi_name', 'kode_pos', 'hp'];
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
    const storeLocation = {{ config('store.shipping_origin.city_id', 501) }}; // Store location from config
    const shipmentEnabled = {{ config('shipment.use_shipment', true) ? 'true' : 'false' }};
    const totalWeight = {{ ($totalWeight * 1000) ?? 1000 }}; // Convert to grams
    const initialTotal = parseInt({{ $totalPrice ?? 0 }}); // Convert to exact integer tanpa pembulatan
    
    console.log('Checkout initialized:', {
        storeLocation: storeLocation,
        totalWeight: totalWeight,
        initialTotal: initialTotal,
        totalPriceFromPHP: {{ $totalPrice ?? 'null' }},
        totalWeightFromPHP: {{ $totalWeight ?? 'null' }}
    });
    
    // Initialize order summary on page load
    updateOrderSummary();
    
    // Debug existing addresses
    console.log('Checking existing addresses...');
    const existingAddresses = document.querySelectorAll('input[name="address_option"][value="existing"]');
    existingAddresses.forEach((addr, index) => {
        console.log(`Address ${index + 1}:`, {
            addressId: addr.dataset.addressId,
            cityId: addr.dataset.cityId,
            cityName: addr.dataset.cityName
        });
    });
    // Test function for manual debugging (can be called from browser console)
    window.testAutoCalculate = function(cityId) {
        console.log('Testing auto-calculate with city ID:', cityId);
        autoCalculateShippingForAddress(cityId, 'Test City');
    };
    
    async function loadProvinces() {
        try {
            const response = await fetch('/api/wilayah/provinces');
            const data = await response.json();
            
            const select = document.getElementById('provinsiSelect');
            select.innerHTML = '<option value="">Pilih Provinsi</option>';
            
            data.forEach(province => {
                const option = document.createElement('option');
                option.value = province.code;
                option.textContent = province.name;
                option.dataset.provinceName = province.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading provinces:', error);
            alert('Gagal memuat data provinsi. Silakan refresh halaman.');
        }
    }
    
    async function loadCities(provinceCode) {
        const kotaSelect = document.getElementById('kotaSelect');
        kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
        
        if (!provinceCode) return;
        
        try {
            const response = await fetch(`/api/wilayah/regencies/${provinceCode}`);
            const cities = await response.json();
            
            cities.forEach(city => {
                const option = document.createElement('option');
                option.value = city.code;
                option.textContent = city.name;
                option.dataset.cityName = city.name;
                kotaSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading cities:', error);
            alert('Gagal memuat data kota/kabupaten. Silakan coba lagi.');
        }
    }
    
    function checkShippingSection() {
        const kotaSelect = document.getElementById('kotaSelect');
        const shippingSection = document.getElementById('shippingSection');
        const selectedAddressOption = document.querySelector('input[name="address_option"]:checked');
        
        // Only show shipping section if shipment is enabled
        if (shipmentEnabled && selectedAddressOption && selectedAddressOption.value === 'new' && kotaSelect && kotaSelect.value) {
            shippingSection.style.display = 'block';
        } else {
            hideShippingSection();
        }
    }
    
    function hideShippingSection() {
        const shippingSection = document.getElementById('shippingSection');
        hideShippingOptions();
        if (shippingSection) {
            shippingSection.style.display = 'none';
        }
    }
    
    function hideShippingOptions() {
        const shippingOptions = document.getElementById('shippingOptions');
        const selectedShippingInfo = document.getElementById('selectedShippingInfo');
        
        if (shippingOptions) shippingOptions.style.display = 'none';
        if (selectedShippingInfo) selectedShippingInfo.style.display = 'none';
        selectedShipping = null;
        
        // Reset hidden inputs (only if they exist)
        const shippingCostHidden = document.getElementById('shippingCostHidden');
        const shippingCourierHidden = document.getElementById('shippingCourierHidden');
        const shippingServiceHidden = document.getElementById('shippingServiceHidden');
        const shippingDescriptionHidden = document.getElementById('shippingDescriptionHidden');
        const shippingEtdHidden = document.getElementById('shippingEtdHidden');
        
        if (shippingCostHidden) shippingCostHidden.value = '';
        if (shippingCourierHidden) shippingCourierHidden.value = '';
        if (shippingServiceHidden) shippingServiceHidden.value = '';
        if (shippingDescriptionHidden) shippingDescriptionHidden.value = '';
        if (shippingEtdHidden) shippingEtdHidden.value = '';
        
        // Update order summary
        updateOrderSummary();
    }
    
    async function calculateShipping() {
        // Skip calculation if shipment is disabled
        if (!shipmentEnabled) {
            console.log('Shipment is disabled, skipping shipping calculation');
            return;
        }
        
        const kotaSelect = document.getElementById('kotaSelect');
        const destination = kotaSelect.value;
        
        if (!destination) {
            alert('Pilih kota tujuan terlebih dahulu');
            return;
        }
        
        // Get selected courier
        const selectedCourier = document.querySelector('input[name="courier_selection"]:checked');
        const courierValue = selectedCourier ? selectedCourier.value : 'all';
        
        showShippingLoader(true);
        
        try {
            let endpoint, requestData;
            
            if (courierValue === 'all') {
                // Compare all couriers
                endpoint = '/api/shipment/compare-costs';
                requestData = {
                    origin: storeLocation,
                    destination: parseInt(destination),
                    weight: totalWeight,
                    couriers: ['jne', 'pos', 'tiki']
                };
            } else {
                // Single courier calculation
                endpoint = '/api/shipment/calculate-cost';
                requestData = {
                    origin: storeLocation,
                    destination: parseInt(destination),
                    weight: totalWeight,
                    courier: courierValue
                };
            }
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(requestData)
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
                                    <p class="text-xs text-gray-500">Estimasi: ${service.etd}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-blue-600">Rp ${parseInt(service.cost).toLocaleString('id-ID')}</p>
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
            cost: parseInt(radio.value) || 0,
            courier: radio.dataset.courier,
            service: radio.dataset.service,
            description: radio.dataset.description,
            etd: radio.dataset.etd
        };
        
        // Update hidden inputs for form submission (except shipping cost - will be handled by updateOrderSummary)
        const shippingCourierHidden = document.getElementById('shippingCourierHidden');
        const shippingServiceHidden = document.getElementById('shippingServiceHidden');
        const shippingDescriptionHidden = document.getElementById('shippingDescriptionHidden');
        const shippingEtdHidden = document.getElementById('shippingEtdHidden');
        
        if (shippingCourierHidden) shippingCourierHidden.value = selectedShipping.courier;
        if (shippingServiceHidden) shippingServiceHidden.value = selectedShipping.service;
        if (shippingDescriptionHidden) shippingDescriptionHidden.value = selectedShipping.description;
        if (shippingEtdHidden) shippingEtdHidden.value = selectedShipping.etd;
        
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
        
        // Update order summary
        updateOrderSummary();
        
        console.log('Shipping selected:', selectedShipping);
    }
    
    function updateOrderSummary() {
        const subtotal = parseInt({{ $totalPrice ?? 0 }});  // Pastikan integer tanpa pembulatan
        const shippingCost = selectedShipping ? (parseInt(selectedShipping.cost) || 0) : 0;
        const grandTotal = subtotal + shippingCost;
        
        console.log('Update Order Summary:', {
            subtotal: subtotal,
            subtotalType: typeof subtotal,
            subtotalExact: subtotal,
            shippingCost: shippingCost,
            shippingCostType: typeof shippingCost,
            grandTotal: grandTotal,
            grandTotalType: typeof grandTotal,
            rawTotalPriceFromPHP: {{ $totalPrice ?? 0 }},
            selectedShipping: selectedShipping
        });
        
        // Update shipping cost display
        const shippingCostRow = document.getElementById('shippingCostRow');
        const shippingCostDisplay = document.getElementById('shippingCostDisplay');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const grandTotalHidden = document.getElementById('grandTotalHidden');
        
        if (shippingCost > 0) {
            shippingCostRow.style.display = 'flex';
            shippingCostDisplay.textContent = `Rp ${shippingCost.toLocaleString('id-ID')}`;
        } else {
            shippingCostRow.style.display = 'none';
        }
        
        // Ensure grandTotal is a valid number
        if (isNaN(grandTotal) || !isFinite(grandTotal)) {
            console.error('Grand total is invalid:', {subtotal, shippingCost, grandTotal});
            grandTotalDisplay.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
            if (grandTotalHidden) grandTotalHidden.value = subtotal;
        } else {
            grandTotalDisplay.textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
            if (grandTotalHidden) grandTotalHidden.value = grandTotal;
        }
        
        // Update shipping cost hidden input as well
        const shippingCostHidden = document.getElementById('shippingCostHidden');
        if (shippingCostHidden) {
            shippingCostHidden.value = shippingCost;
        }
    }
    
    function resetShippingCost() {
        console.log('Resetting shipping cost...');
        selectedShipping = null;
        // Reset hidden inputs
        const shippingCostHidden = document.getElementById('shippingCostHidden');
        const shippingCourierHidden = document.getElementById('shippingCourierHidden');
        const shippingServiceHidden = document.getElementById('shippingServiceHidden');
        const shippingDescriptionHidden = document.getElementById('shippingDescriptionHidden');
        const shippingEtdHidden = document.getElementById('shippingEtdHidden');
        
        if (shippingCostHidden) shippingCostHidden.value = '';
        if (shippingCourierHidden) shippingCourierHidden.value = '';
        if (shippingServiceHidden) shippingServiceHidden.value = '';
        if (shippingDescriptionHidden) shippingDescriptionHidden.value = '';
        if (shippingEtdHidden) shippingEtdHidden.value = '';
        
        // Hide existing address shipping info
        const existingAddressShipping = document.getElementById('existingAddressShipping');
        if (existingAddressShipping) {
            existingAddressShipping.style.display = 'none';
        }
        
        // Update order summary to show only subtotal
        updateOrderSummary();
        console.log('Shipping cost reset complete');
    }
    
    async function autoCalculateShippingForAddress(cityId, cityName) {
        console.log('Auto-calculating shipping for city:', cityId, cityName);
        
        // Skip auto-calculation if shipment is disabled
        if (!shipmentEnabled) {
            console.log('Shipment is disabled, skipping auto shipping calculation');
            
            // Show free shipping info for existing address
            const existingAddressShipping = document.getElementById('existingAddressShipping');
            if (existingAddressShipping) {
                document.getElementById('existingShippingDetails').innerHTML = 
                    `<strong>Gratis Ongkos Kirim!</strong><br>Pengiriman ke ${cityName} - GRATIS`;
                existingAddressShipping.style.display = 'block';
            }
            
            // Set shipping cost to 0
            selectedShipping = {
                cost: 0,
                courier: 'free',
                service: 'Free Shipping',
                description: 'Gratis Ongkos Kirim',
                etd: '2-3 hari'
            };
            
            updateOrderSummary();
            return;
        }
        
        // Show loading indicator in total
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const originalText = grandTotalDisplay.textContent;
        grandTotalDisplay.textContent = 'Menghitung...';
        
        try {
            // Use JNE as default for auto-calculation
            const requestData = {
                origin: storeLocation,
                destination: parseInt(cityId),
                weight: totalWeight,
                courier: 'jne'
            };
            
            console.log('Sending request:', requestData);
            
            const response = await fetch('/api/shipment/calculate-cost', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(requestData)
            });
            
            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success && data.data.length > 0) {
                // Data structure: data[0].services[0]
                const courierData = data.data[0];
                const firstService = courierData.services[0];
                
                selectedShipping = {
                    cost: parseInt(firstService.cost) || 0,
                    courier: courierData.courier_code.toUpperCase(),
                    service: firstService.service,
                    description: firstService.description,
                    etd: firstService.etd
                };
                
                console.log('Parsed shipping cost:', selectedShipping.cost, typeof selectedShipping.cost);
                console.log('Full shipping data:', selectedShipping);
                
                // Update hidden form fields (except shipping cost - will be handled by updateOrderSummary)
                const shippingCourierHidden = document.getElementById('shippingCourierHidden');
                const shippingServiceHidden = document.getElementById('shippingServiceHidden');
                const shippingDescriptionHidden = document.getElementById('shippingDescriptionHidden');
                const shippingEtdHidden = document.getElementById('shippingEtdHidden');
                
                if (shippingCourierHidden) shippingCourierHidden.value = selectedShipping.courier;
                if (shippingServiceHidden) shippingServiceHidden.value = selectedShipping.service;
                if (shippingDescriptionHidden) shippingDescriptionHidden.value = selectedShipping.description;
                if (shippingEtdHidden) shippingEtdHidden.value = selectedShipping.etd;
                
                // Update order summary
                updateOrderSummary();
                
                // Show shipping info for existing address
                const existingAddressShipping = document.getElementById('existingAddressShipping');
                const existingShippingDetails = document.getElementById('existingShippingDetails');
                
                existingShippingDetails.innerHTML = `
                    <p><strong>${selectedShipping.courier} - ${selectedShipping.service}</strong></p>
                    <p>${selectedShipping.description}</p>
                    <p>Biaya: <strong>Rp ${selectedShipping.cost.toLocaleString('id-ID')}</strong></p>
                    <p>Estimasi: <strong>${selectedShipping.etd}</strong></p>
                `;
                existingAddressShipping.style.display = 'block';
                
                console.log('Auto-selected shipping:', selectedShipping);
            } else {
                console.warn('No shipping options available for this address');
                grandTotalDisplay.textContent = originalText;
                resetShippingCost();
            }
        } catch (error) {
            console.error('Error auto-calculating shipping:', error);
            grandTotalDisplay.textContent = originalText;
            resetShippingCost();
        }
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
