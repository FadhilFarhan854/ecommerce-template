{{-- Shipment Calculator Component --}}
@if(config('shipment.use_shipment', true))
<div class="shipment-calculator" id="shipmentCalculator">
    <h3>Hitung Ongkos Kirim</h3>
    
    <form id="shippingForm" class="row g-3">
        <div class="col-md-6">
            <label for="originProvince" class="form-label">Provinsi Asal</label>
            <select class="form-select" id="originProvince" name="origin_province" required>
                <option value="">Pilih Provinsi</option>
            </select>
        </div>
        
        <div class="col-md-6">
            <label for="originCity" class="form-label">Kota Asal</label>
            <select class="form-select" id="originCity" name="origin_city" required>
                <option value="">Pilih Kota</option>
            </select>
        </div>
        
        <div class="col-md-6">
            <label for="destProvince" class="form-label">Provinsi Tujuan</label>
            <select class="form-select" id="destProvince" name="dest_province" required>
                <option value="">Pilih Provinsi</option>
            </select>
        </div>
        
        <div class="col-md-6">
            <label for="destCity" class="form-label">Kota Tujuan</label>
            <select class="form-select" id="destCity" name="dest_city" required>
                <option value="">Pilih Kota</option>
            </select>
        </div>
        
        <div class="col-md-6">
            <label for="weight" class="form-label">Berat (gram)</label>
            <input type="number" class="form-control" id="weight" name="weight" min="1" required>
        </div>
        
        <div class="col-md-6">
            <label for="courier" class="form-label">Kurir</label>
            <select class="form-select" id="courier" name="courier">
                <option value="">Bandingkan Semua</option>
                <option value="jne">JNE</option>
                <option value="pos">POS Indonesia</option>
                <option value="tiki">TIKI</option>
            </select>
        </div>
        
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-calculator"></i> Hitung Ongkir
            </button>
        </div>
    </form>
    
    <div id="shippingResults" class="mt-4" style="display: none;">
        <h4>Hasil Perhitungan Ongkir</h4>
        <div id="shippingOptions" class="shipping-options">
            <!-- Results will be loaded here -->
        </div>
    </div>
    
    <div id="shippingLoader" class="text-center mt-3" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Menghitung ongkos kirim...</p>
    </div>
</div>

<style>
.shipment-calculator {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.shipping-options {
    display: grid;
    gap: 15px;
}

.shipping-option {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.shipping-option:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.1);
}

.shipping-option.selected {
    border-color: #007bff;
    background: #f8f9ff;
}

.courier-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.courier-name {
    font-weight: bold;
    color: #333;
}

.service-options {
    display: grid;
    gap: 8px;
}

.service-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s ease;
}

.service-option:hover {
    background: #e9ecef;
}

.service-option input[type="radio"] {
    margin-right: 10px;
}

.service-info {
    flex: 1;
}

.service-name {
    font-weight: 600;
    margin-bottom: 2px;
}

.service-desc {
    font-size: 0.85em;
    color: #666;
}

.service-price {
    text-align: right;
}

.price {
    font-weight: bold;
    color: #007bff;
    font-size: 1.1em;
}

.etd {
    font-size: 0.85em;
    color: #666;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}
</style>

<script>
class ShipmentCalculator {
    constructor() {
        this.apiBase = '/api/shipment';
        this.selectedShipping = null;
        this.init();
    }
    
    init() {
        this.loadProvinces();
        this.bindEvents();
    }
    
    bindEvents() {
        // Form submission
        document.getElementById('shippingForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.calculateShipping();
        });
        
        // Province change events
        document.getElementById('originProvince').addEventListener('change', (e) => {
            this.loadCities(e.target.value, 'originCity');
        });
        
        document.getElementById('destProvince').addEventListener('change', (e) => {
            this.loadCities(e.target.value, 'destCity');
        });
    }
    
    async loadProvinces() {
        try {
            const response = await fetch(`${this.apiBase}/provinces`);
            const data = await response.json();
            
            if (data.success) {
                const originSelect = document.getElementById('originProvince');
                const destSelect = document.getElementById('destProvince');
                
                data.data.forEach(province => {
                    const option1 = new Option(province.province, province.province_id);
                    const option2 = new Option(province.province, province.province_id);
                    originSelect.add(option1);
                    destSelect.add(option2);
                });
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
            this.showError('Gagal memuat data provinsi');
        }
    }
    
    async loadCities(provinceId, targetSelectId) {
        if (!provinceId) return;
        
        try {
            const response = await fetch(`${this.apiBase}/cities?province_id=${provinceId}`);
            const data = await response.json();
            
            if (data.success) {
                const select = document.getElementById(targetSelectId);
                select.innerHTML = '<option value="">Pilih Kota</option>';
                
                data.data.forEach(city => {
                    const option = new Option(
                        `${city.type} ${city.city_name}`, 
                        city.city_id
                    );
                    select.add(option);
                });
            }
        } catch (error) {
            console.error('Error loading cities:', error);
            this.showError('Gagal memuat data kota');
        }
    }
    
    async calculateShipping() {
        const form = document.getElementById('shippingForm');
        const formData = new FormData(form);
        
        const origin = formData.get('origin_city');
        const destination = formData.get('dest_city');
        const weight = formData.get('weight');
        const courier = formData.get('courier');
        
        if (!origin || !destination || !weight) {
            this.showError('Mohon lengkapi semua field yang diperlukan');
            return;
        }
        
        this.showLoader(true);
        this.hideResults();
        
        try {
            let endpoint, requestData;
            
            if (courier) {
                // Single courier calculation
                endpoint = `${this.apiBase}/calculate-cost`;
                requestData = { origin, destination, weight: parseInt(weight), courier };
            } else {
                // Compare all couriers
                endpoint = `${this.apiBase}/compare-costs`;
                requestData = { 
                    origin, 
                    destination, 
                    weight: parseInt(weight),
                    couriers: ['jne', 'pos', 'tiki']
                };
            }
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(requestData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.displayResults(data.data);
            } else {
                this.showError(data.message || 'Gagal menghitung ongkos kirim');
            }
        } catch (error) {
            console.error('Error calculating shipping:', error);
            this.showError('Terjadi kesalahan saat menghitung ongkos kirim');
        } finally {
            this.showLoader(false);
        }
    }
    
    displayResults(couriers) {
        const container = document.getElementById('shippingOptions');
        container.innerHTML = '';
        
        if (!couriers || couriers.length === 0) {
            container.innerHTML = '<div class="alert alert-warning">Tidak ada opsi pengiriman yang tersedia</div>';
            this.showResults();
            return;
        }
        
        couriers.forEach(courier => {
            const courierDiv = document.createElement('div');
            courierDiv.className = 'shipping-option';
            
            let servicesHtml = '';
            courier.services.forEach(service => {
                const serviceId = `shipping_${courier.courier_code}_${service.service}`;
                servicesHtml += `
                    <div class="service-option" onclick="this.querySelector('input').checked = true; window.shipmentCalc.selectShipping(this)">
                        <input type="radio" name="selected_shipping" id="${serviceId}" 
                               value="${service.cost}"
                               data-courier="${courier.courier_code}"
                               data-service="${service.service}"
                               data-description="${service.description}"
                               data-etd="${service.etd}">
                        <div class="service-info">
                            <div class="service-name">${service.service}</div>
                            <div class="service-desc">${service.description}</div>
                        </div>
                        <div class="service-price">
                            <div class="price">Rp ${service.cost.toLocaleString('id-ID')}</div>
                            <div class="etd">${service.etd} hari</div>
                        </div>
                    </div>
                `;
            });
            
            courierDiv.innerHTML = `
                <div class="courier-info">
                    <div class="courier-name">${courier.courier_name}</div>
                </div>
                <div class="service-options">
                    ${servicesHtml}
                </div>
            `;
            
            container.appendChild(courierDiv);
        });
        
        this.showResults();
    }
    
    selectShipping(element) {
        // Remove previous selection
        document.querySelectorAll('.shipping-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        
        // Add selection to current option
        element.closest('.shipping-option').classList.add('selected');
        
        // Store selected shipping info
        const radio = element.querySelector('input[type="radio"]');
        this.selectedShipping = {
            cost: radio.value,
            courier: radio.dataset.courier,
            service: radio.dataset.service,
            description: radio.dataset.description,
            etd: radio.dataset.etd
        };
        
        // Trigger custom event for parent components
        document.dispatchEvent(new CustomEvent('shippingSelected', {
            detail: this.selectedShipping
        }));
    }
    
    getSelectedShipping() {
        return this.selectedShipping;
    }
    
    showLoader(show) {
        document.getElementById('shippingLoader').style.display = show ? 'block' : 'none';
    }
    
    showResults() {
        document.getElementById('shippingResults').style.display = 'block';
    }
    
    hideResults() {
        document.getElementById('shippingResults').style.display = 'none';
    }
    
    showError(message) {
        const container = document.getElementById('shippingOptions');
        container.innerHTML = `<div class="alert alert-danger">${message}</div>`;
        this.showResults();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.shipmentCalc = new ShipmentCalculator();
});

// Listen for shipping selection
document.addEventListener('shippingSelected', function(event) {
    console.log('Shipping selected:', event.detail);
    // You can handle the selected shipping data here
});

// For pages that need to access the calculator instance
window.getShipmentCalculator = function() {
    return window.shipmentCalc;
};
</script>
@else
{{-- Address Calculator Component (Raja Ongkir only for addresses) --}}
<div class="address-calculator" id="addressCalculator">
    <h3>Pilih Alamat Pengiriman</h3>
    
    <form id="addressForm" class="row g-3">
        <div class="col-md-6">
            <label for="destProvince" class="form-label">Provinsi Tujuan</label>
            <select class="form-select" id="destProvince" name="dest_province" required>
                <option value="">Pilih Provinsi</option>
            </select>
        </div>
        
        <div class="col-md-6">
            <label for="destCity" class="form-label">Kota Tujuan</label>
            <select class="form-select" id="destCity" name="dest_city" required>
                <option value="">Pilih Kota</option>
            </select>
        </div>
        
        <div class="col-12">
            <div class="alert alert-info">
                <strong>Info:</strong> Ongkos kirim gratis untuk semua pesanan!
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize address calculator for provinces and cities only
    window.addressCalc = {
        init: function() {
            this.loadProvinces();
            this.bindEvents();
        },
        
        loadProvinces: function() {
            fetch('/api/shipment/provinces')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('destProvince');
                    select.innerHTML = '<option value="">Pilih Provinsi</option>';
                    
                    if (data.success && data.data.results) {
                        data.data.results.forEach(province => {
                            const option = document.createElement('option');
                            option.value = province.province_id;
                            option.textContent = province.province;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading provinces:', error);
                });
        },
        
        loadCities: function(provinceId) {
            const citySelect = document.getElementById('destCity');
            citySelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/api/shipment/cities/${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">Pilih Kota</option>';
                    
                    if (data.success && data.data.results) {
                        data.data.results.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.city_id;
                            option.textContent = `${city.type} ${city.city_name}`;
                            select.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                });
        },
        
        bindEvents: function() {
            document.getElementById('destProvince').addEventListener('change', (e) => {
                if (e.target.value) {
                    this.loadCities(e.target.value);
                } else {
                    document.getElementById('destCity').innerHTML = '<option value="">Pilih Kota</option>';
                }
            });
        }
    };
    
    window.addressCalc.init();
});

// For pages that need to access the address calculator instance
window.getAddressCalculator = function() {
    return window.addressCalc;
};
</script>
@endif
