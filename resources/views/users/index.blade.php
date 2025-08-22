@extends('layouts.app')

@section('title', 'Users Management - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="w-full">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
            <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                Add New User
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

        {{-- Filters and Search --}}
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('users.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div class="md:col-span-3">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Filter by Role</label>
                            <select name="role" id="role" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                               
                                <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" name="search" id="search"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Search by name, email, or phone..." value="{{ request('search') }}">
                        </div>
                        <div class="md:col-span-3 flex items-end space-x-2">
                            <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200">
                                Filter
                            </button>
                            <a href="{{ route('users.index') }}" 
                                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md transition duration-200">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Users Table --}}
        <div class="bg-white shadow rounded-lg">
            <div class="p-6">
                @if($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead class="bg-gray-100 text-gray-700 text-sm">
                                <tr>
                                    <th class="px-4 py-2">ID</th>
                                    <th class="px-4 py-2">Name</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Phone</th>
                                    <th class="px-4 py-2">Role</th>
                                    <th class="px-4 py-2">Orders Count</th>
                                    <th class="px-4 py-2">Addresses</th>
                                    <th class="px-4 py-2">Joined Date</th>
                                    <th class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">{{ $user->id }}</td>
                                        <td class="px-4 py-2">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">{{ $user->email }}</td>
                                        <td class="px-4 py-2 text-gray-600">{{ $user->phone ?? '-' }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium
                                                @if($user->role === 'admin') bg-purple-100 text-purple-800
                                                @elseif($user->role === 'user') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($user->role ?? 'user') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                                {{ $user->orders->count() }} orders
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">
                                            {{ $user->addresses->count() }} addresses
                                        </td>
                                        <td class="px-4 py-2 text-gray-600">
                                            {{ $user->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <div class="flex space-x-2">
                                                <button onclick="openUserModal({{ json_encode($user->load(['addresses', 'orders'])) }})" 
                                                   class="px-3 py-1 rounded-md text-sm bg-blue-100 text-blue-700 hover:bg-blue-200">
                                                    View
                                                </button>
                                                <a href="{{ route('users.edit', $user) }}" 
                                                   class="px-3 py-1 rounded-md text-sm bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                                    Edit
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this user?')" 
                                                          class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-3 py-1 rounded-md text-sm bg-red-100 text-red-700 hover:bg-red-200">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="flex justify-center mt-6">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
                        <div class="mt-6">
                            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Add New User
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- User Detail Modal --}}
<div id="userModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            {{-- Modal Header --}}
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">User Details</h3>
                <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Modal Body --}}
            <div class="mt-4 space-y-4">
                {{-- User Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">User ID</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalUserId">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalUserName">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalUserEmail">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalUserPhone">-</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <span class="mt-1 inline-block" id="modalUserRole"></span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Joined Date</label>
                        <p class="mt-1 text-sm text-gray-900" id="modalUserJoined">-</p>
                    </div>
                </div>
                
                {{-- Addresses --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Addresses</label>
                    <div class="mt-2 bg-gray-50 rounded-md p-3">
                        <div id="modalUserAddresses" class="space-y-2">
                            <!-- Addresses will be loaded here -->
                        </div>
                    </div>
                </div>
                
                {{-- Orders --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">Recent Orders</label>
                    <div class="mt-2 bg-gray-50 rounded-md p-3">
                        <div id="modalUserOrders" class="space-y-2">
                            <!-- Orders will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Modal Footer --}}
            <div class="mt-6 flex justify-end space-x-2">
                <button onclick="editUser()" id="editUserBtn"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                    Edit User
                </button>
                <button onclick="closeUserModal()" 
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-md transition duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentUserId = null;

function openUserModal(user) {
    console.log('User data:', user); // Debug log
    
    currentUserId = user.id;
    
    // Show modal
    document.getElementById('userModal').classList.remove('hidden');
    
    // Populate user basic info
    document.getElementById('modalUserId').textContent = user.id;
    document.getElementById('modalUserName').textContent = user.name;
    document.getElementById('modalUserEmail').textContent = user.email;
    document.getElementById('modalUserPhone').textContent = user.phone || '-';
    
    // Format and display joined date
    if (user.created_at) {
        const date = new Date(user.created_at);
        document.getElementById('modalUserJoined').textContent = date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } else {
        document.getElementById('modalUserJoined').textContent = 'N/A';
    }
    
    // Role with styling
    const roleElement = document.getElementById('modalUserRole');
    const role = user.role || 'user';
    roleElement.className = 'mt-1 inline-block px-2 py-1 rounded-full text-xs font-medium';
    
    switch(role.toLowerCase()) {
        case 'admin':
            roleElement.className += ' bg-purple-100 text-purple-800';
            break;
        case 'user':
            roleElement.className += ' bg-blue-100 text-blue-800';
            break;
        default:
            roleElement.className += ' bg-gray-100 text-gray-800';
    }
    roleElement.textContent = role.charAt(0).toUpperCase() + role.slice(1);
    
    // Display addresses
    const addressesContainer = document.getElementById('modalUserAddresses');
    addressesContainer.innerHTML = '';
    
    if (user.addresses && user.addresses.length > 0) {
        user.addresses.forEach(address => {
            const addressDiv = document.createElement('div');
            addressDiv.className = 'p-2 bg-white rounded border';
            addressDiv.innerHTML = `
                <p class="font-medium">${address.nama_depan || ''} ${address.nama_belakang || ''}</p>
                <p class="text-sm text-gray-600">${address.alamat || 'No address'}</p>
                <p class="text-sm text-gray-600">${address.kelurahan || ''}, ${address.kecamatan || ''}</p>
                <p class="text-sm text-gray-600">${address.kota || ''}, ${address.provinsi || ''} ${address.kode_pos || ''}</p>
                ${address.hp ? `<p class="text-sm text-gray-500">📞 ${address.hp}</p>` : ''}
            `;
            addressesContainer.appendChild(addressDiv);
        });
    } else {
        addressesContainer.innerHTML = '<p class="text-gray-500 text-sm">No addresses found</p>';
    }
    
    // Display recent orders
    const ordersContainer = document.getElementById('modalUserOrders');
    ordersContainer.innerHTML = '';
    
    if (user.orders && user.orders.length > 0) {
        user.orders.slice(0, 5).forEach(order => {
            const orderDiv = document.createElement('div');
            orderDiv.className = 'flex justify-between items-center p-2 bg-white rounded border';
            const orderDate = new Date(order.created_at).toLocaleDateString();
            orderDiv.innerHTML = `
                <div>
                    <p class="font-medium">Order #${order.id}</p>
                    <p class="text-sm text-gray-600">${orderDate}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium">Rp ${new Intl.NumberFormat('id-ID').format(parseFloat(order.total_price || 0))}</p>
                    <span class="text-xs px-2 py-1 rounded-full ${getStatusColor(order.status)}">${order.status}</span>
                </div>
            `;
            ordersContainer.appendChild(orderDiv);
        });
    } else {
        ordersContainer.innerHTML = '<p class="text-gray-500 text-sm">No orders found</p>';
    }
}

function getStatusColor(status) {
    switch(status?.toLowerCase()) {
        case 'completed': return 'bg-green-100 text-green-800';
        case 'pending': return 'bg-yellow-100 text-yellow-800';
        case 'cancelled': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function editUser() {
    if (currentUserId) {
        window.location.href = `{{ route('users.index') }}/${currentUserId}/edit`;
    }
}

function closeUserModal() {
    document.getElementById('userModal').classList.add('hidden');
    currentUserId = null;
}

// Close modal when clicking outside
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeUserModal();
    }
});
</script>




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
            <div class="mt-6 flex justify-end">
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
        case 'completed':
            statusElement.className += ' bg-green-100 text-green-800';
            break;
        case 'pending':
            statusElement.className += ' bg-yellow-100 text-yellow-800';
            break;
        case 'cancelled':
            statusElement.className += ' bg-red-100 text-red-800';
            break;
        default:
            statusElement.className += ' bg-gray-100 text-gray-800';
    }
    statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    
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

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
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

@endsection