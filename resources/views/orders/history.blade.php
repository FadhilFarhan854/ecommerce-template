@extends('layouts.app')

@section('title', 'Orders - ' . config('app.name'))

@section('content')
<div class="max-w-6xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold">List Orders</h1>
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
            @if(count($orderData) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead class="bg-gray-100 text-gray-700 text-sm">
                            <tr>
                                <th class="px-4 py-2">ID</th>               
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Quantity</th>
                                <th class="px-4 py-2">Address</th>
                                <th class="px-4 py-2">Status</th>
                                <th class="px-4 py-2">Total Price</th>
                                <th class="px-4 py-2">Date</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($orderData as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $order['order_number'] ?? $order['id'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-gray-600">
                                        @if(isset($order['products']) && is_array($order['products']) && count($order['products']) > 0)
                                            @foreach($order['products'] as $product)
                                                <div class="mb-1">
                                                    <span class="font-medium">{{ $product['name'] ?? 'N/A' }}</span>
                                                    <span class="text-sm text-gray-500">({{ $product['quantity'] ?? 1 }}x)</span>
                                                </div>
                                            @endforeach
                                        @elseif(isset($order['product_name']))
                                            {{ $order['product_name'] }}
                                        @else
                                            <span class="text-red-500">No products found</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">{{ $order['total_quantity'] ?? $order['quantity'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2 text-gray-600">{{ $order['address'] ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 rounded-full text-xs
                                            @if(($order['status'] ?? '') === 'finished') bg-green-100 text-green-800
                                            @elseif(($order['status'] ?? '') === 'paid') bg-blue-100 text-blue-800
                                            @elseif(($order['status'] ?? '') === 'sending') bg-purple-100 text-purple-800
                                            @elseif(($order['status'] ?? '') === 'unpaid') bg-yellow-100 text-yellow-800
                                            @elseif(($order['status'] ?? '') === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            @php
                                                $statusLabels = [
                                                    'unpaid' => 'Unpaid',
                                                    'paid' => 'Paid',
                                                    'sending' => 'Sending',
                                                    'finished' => 'Finished',
                                                    'cancelled' => 'Cancelled'
                                                ];
                                                $statusText = $statusLabels[$order['status'] ?? ''] ?? ucfirst($order['status'] ?? 'Unknown');
                                            @endphp
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">
                                        Rp {{ number_format((float)($order['total_price'] ?? 0), 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-2 text-gray-600">
                                        @if(isset($order['created_at']))
                                            {{ date('d M Y', strtotime($order['created_at'])) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex justify-center space-x-2">
                                            <button onclick="openOrderModal({{ json_encode($order) }})" 
                                                    class="px-3 py-1 rounded-md text-sm bg-blue-100 text-blue-700 hover:bg-blue-200">
                                                View Details
                                            </button>
                                            @if(isset($order['id']))
                                                {{-- Actions based on order status --}}
                                                @if(($order['status'] ?? '') === 'unpaid')
                                                    <button onclick="retryPayment('{{ $order['id'] }}')"
                                                        class="px-3 py-1 rounded-md text-sm bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                                        Retry Payment
                                                    </button>
                                                @elseif(($order['status'] ?? '') === 'sending')
                                                    <form action="{{ route('orders.mark-finished', $order['id']) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                onclick="return confirm('Have you received this order?')"
                                                                class="px-3 py-1 rounded-md text-sm bg-green-100 text-green-700 hover:bg-green-200">
                                                            Mark Finished
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500 py-6">No orders found.</p>
            @endif
        </div>
    </div>
</div>

{{-- Order Detail Modal --}}
<div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Order Details</h3>
                <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="mt-4 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order ID</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalOrderId">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer Name</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalCustomerName">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <span class="mt-1 inline-block" id="modalStatus"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Price</label>
                        <p class="mt-1 text-sm text-gray-900 font-semibold" id="modalTotalPrice">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order Date</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalOrderDate">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Quantity</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalTotalQuantity">-</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Shipping Address</label>
                    <p class="mt-1 text-sm text-gray-900" id="modalAddress">-</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Products</label>
                    <div class="mt-2 bg-gray-50 rounded-md p-3">
                        <div id="modalProducts" class="space-y-2">
                            <!-- Products will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="mt-6 flex justify-end space-x-3">
                <div id="modalActions" class="flex space-x-3">
                    <!-- Dynamic action buttons will be added here -->
                </div>
                <button onclick="closeOrderModal()" 
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openOrderModal(order) {
    console.log('Order data:', order); // Debug log
    
    // Show modal
    document.getElementById('orderModal').classList.remove('hidden');
    
    // Populate modal data
    document.getElementById('modalOrderId').textContent = order.order_number || order.id || 'N/A';
    document.getElementById('modalCustomerName').textContent = order.customer_name || order.user_name || 'N/A';
    document.getElementById('modalTotalPrice').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(order.total_price || 0));
    document.getElementById('modalTotalQuantity').textContent = order.total_quantity || order.quantity || 'N/A';
    document.getElementById('modalAddress').textContent = order.address || order.shipping_address || 'N/A';
    
    // Format and display date
    if (order.created_at) {
        const date = new Date(order.created_at);
        document.getElementById('modalOrderDate').textContent = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } else {
        document.getElementById('modalOrderDate').textContent = 'N/A';
    }
    
    // Status with styling
    const statusElement = document.getElementById('modalStatus');
    const status = order.status || 'unknown';
    statusElement.className = 'mt-1 inline-block px-2 py-1 rounded-full text-xs';
    
    switch(status.toLowerCase()) {
        case 'finished':
            statusElement.className += ' bg-green-100 text-green-800';
            break;
        case 'paid':
            statusElement.className += ' bg-blue-100 text-blue-800';
            break;
        case 'sending':
            statusElement.className += ' bg-purple-100 text-purple-800';
            break;
        case 'unpaid':
            statusElement.className += ' bg-yellow-100 text-yellow-800';
            break;
        case 'cancelled':
            statusElement.className += ' bg-red-100 text-red-800';
            break;
        default:
            statusElement.className += ' bg-gray-100 text-gray-800';
    }
    statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    
    // Setup action buttons based on status
    setupModalActions(order);
    
    // Display products
    const productsContainer = document.getElementById('modalProducts');
    productsContainer.innerHTML = '';
    
    if (order.products && Array.isArray(order.products)) {
        order.products.forEach(product => {
            const productDiv = document.createElement('div');
            productDiv.className = 'flex justify-between items-center p-2 bg-white rounded border';
            productDiv.innerHTML = `
                <div>
                    <p class="font-medium">${product.name || 'Product Name N/A'}</p>
                    <p class="text-sm text-gray-600">Price: Rp ${new Intl.NumberFormat('id-ID').format(parseFloat(product.price || 0))}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm">Qty: ${product.quantity || product.pivot?.quantity || 1}</p>
                    <p class="text-sm font-medium">Total: Rp ${new Intl.NumberFormat('id-ID').format(parseFloat(product.price || 0) * parseInt(product.quantity || product.pivot?.quantity || 1))}</p>
                </div>
            `;
            productsContainer.appendChild(productDiv);
        });
    } else if (order.product_name) {
        const productDiv = document.createElement('div');
        productDiv.className = 'flex justify-between items-center p-2 bg-white rounded border';
        productDiv.innerHTML = `
            <div>
                <p class="font-medium">${order.product_name}</p>
                <p class="text-sm text-gray-600">Price: Rp ${new Intl.NumberFormat('id-ID').format(parseFloat(order.product_price || order.total_price || 0))}</p>
            </div>
            <div class="text-right">
                <p class="text-sm">Qty: ${order.quantity || 1}</p>
                <p class="text-sm font-medium">Total: Rp ${new Intl.NumberFormat('id-ID').format(parseFloat(order.total_price || 0))}</p>
            </div>
        `;
        productsContainer.appendChild(productDiv);
    } else {
        productsContainer.innerHTML = '<p class="text-gray-500 text-sm">No product information available</p>';
    }
}

function setupModalActions(order) {
    const actionsContainer = document.getElementById('modalActions');
    actionsContainer.innerHTML = ''; // Clear existing actions
    
    const status = order.status?.toLowerCase();
    const orderId = order.id;
    
    if (status === 'unpaid') {
        // Add retry payment button
        const retryButton = document.createElement('button');
        retryButton.onclick = () => retryPayment(orderId);
        retryButton.className = 'px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md transition duration-200';
        retryButton.textContent = 'Retry Payment';
        actionsContainer.appendChild(retryButton);
    } else if (status === 'sending') {
        // Add finish order button
        const finishButton = document.createElement('button');
        finishButton.onclick = () => finishOrder(orderId);
        finishButton.className = 'px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-md transition duration-200';
        finishButton.textContent = 'Mark as Finished';
        actionsContainer.appendChild(finishButton);
    }
}

function finishOrder(orderId) {
    if (!confirm('Are you sure you have received this order?')) {
        return;
    }
    
    fetch(`/orders/${orderId}/mark-finished`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order marked as finished successfully!');
            location.reload();
        } else {
            alert('Failed to mark order as finished: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
}

// Retry payment function
async function retryPayment(orderId) {
    try {
        const response = await fetch(`/orders/${orderId}/retry-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.snap_token) {
            // Open Midtrans payment popup
            if (typeof window.snap !== 'undefined') {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        alert('Pembayaran berhasil!');
                        location.reload();
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        alert('Pembayaran pending. Silakan selesaikan pembayaran Anda.');
                        location.reload();
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        alert('Anda menutup popup pembayaran. Anda dapat mencoba lagi kapan saja.');
                    }
                });
            } else {
                console.error('Snap.js not loaded');
                alert('Payment gateway sedang loading. Silakan coba lagi dalam beberapa detik.');
                
                // Load Snap.js dynamically
                const script = document.createElement('script');
                script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                script.setAttribute('data-client-key', '{{ config("midtrans.client_key") }}');
                script.onload = function() {
                    console.log('Snap.js loaded, retrying payment...');
                    retryPayment(orderId);
                };
                document.head.appendChild(script);
            }
        } else {
            alert(data.message || 'Gagal membuat token pembayaran');
        }
    } catch (error) {
        console.error('Retry payment error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}

// Close modal when clicking outside
document.getElementById('orderModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeOrderModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeOrderModal();
    }
});
</script>

{{-- Load Midtrans Snap.js --}}
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

@endsection