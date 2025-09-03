{{-- Example: Shipment Calculator Integration --}}
@extends('layouts.app')

@section('title', 'Contoh Integrasi Shipment Calculator')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Contoh Penggunaan Shipment Calculator</h2>
            <p class="text-gray-600 mt-2">Berikut adalah contoh implementasi kalkulator ongkos kirim menggunakan Raja Ongkir API</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                {{-- Shipment Calculator Card --}}
                <div class="bg-white rounded-lg shadow-lg p-6">
                    @include('components.shipment-calculator')
                </div>
            </div>
            
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">Info Pengiriman Terpilih</h5>
                    <div id="selectedShippingInfo">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-blue-700">
                            Belum ada pengiriman yang dipilih
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h5 class="text-lg font-semibold text-gray-800 mb-4">API Testing</h5>
                    <div class="space-y-2">
                        <button type="button" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm" onclick="testGetProvinces()">
                            Test Get Provinces
                        </button>
                        <button type="button" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition text-sm" onclick="testGetCities()">
                            Test Get Cities
                        </button>
                        <button type="button" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition text-sm" onclick="testGetCouriers()">
                            Test Get Couriers
                        </button>
                    </div>
                    
                    <div id="testResults" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script>
// Listen for shipping selection events
document.addEventListener('shippingSelected', function(event) {
    const shipping = event.detail;
    
    const infoDiv = document.getElementById('selectedShippingInfo');
    infoDiv.innerHTML = `
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h6 class="font-semibold text-gray-800 mb-2">${shipping.courier.toUpperCase()} - ${shipping.service}</h6>
            <p class="text-gray-600 text-sm mb-3">${shipping.description}</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Biaya:</p>
                    <p class="font-bold text-blue-600">Rp ${parseInt(shipping.cost).toLocaleString('id-ID')}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Estimasi:</p>
                    <p class="font-bold text-green-600">${shipping.etd} hari</p>
                </div>
            </div>
        </div>
    `;
    
    console.log('Shipping selected:', shipping);
});

// Test functions
async function testGetProvinces() {
    try {
        const response = await fetch('/api/shipment/provinces');
        const data = await response.json();
        
        showTestResult('Get Provinces', data.success, data);
    } catch (error) {
        showTestResult('Get Provinces', false, { error: error.message });
    }
}

async function testGetCities() {
    try {
        // Test with Jakarta province (ID: 6)
        const response = await fetch('/api/shipment/cities?province_id=6');
        const data = await response.json();
        
        showTestResult('Get Cities (Jakarta)', data.success, data);
    } catch (error) {
        showTestResult('Get Cities', false, { error: error.message });
    }
}

async function testGetCouriers() {
    try {
        const response = await fetch('/api/shipment/couriers');
        const data = await response.json();
        
        showTestResult('Get Couriers', data.success, data);
    } catch (error) {
        showTestResult('Get Couriers', false, { error: error.message });
    }
}

function showTestResult(testName, success, data) {
    const resultsDiv = document.getElementById('testResults');
    const alertClass = success ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700';
    const icon = success ? '✅' : '❌';
    
    resultsDiv.innerHTML = `
        <div class="border rounded-lg p-3 ${alertClass}">
            <p class="font-semibold">${icon} ${testName}</p>
            <details class="mt-2">
                <summary class="cursor-pointer text-sm">Lihat Response</summary>
                <pre class="text-xs mt-2 overflow-auto max-h-40">${JSON.stringify(data, null, 2)}</pre>
            </details>
        </div>
    `;
}
</script>
@endpush
