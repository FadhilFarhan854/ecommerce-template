@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="mt-2 text-gray-600">Overview of your business performance</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Profit Today -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Profit Today</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($profitToday, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Orders Today -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Orders Today</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $orderedToday }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUsers) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            
        </div>

        <!-- Alert for Low Stock -->
        @if($lowStockProducts > 0)
        <div class="mb-8">
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Low Stock Alert
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>You have {{ $lowStockProducts }} products with low stock (≤5 items). Please restock soon!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Sales Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900" id="salesChartTitle">Sales Trend (Last 30 Days)</h3>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button id="dailyBtn" class="px-3 py-1 text-sm font-medium text-gray-700 bg-white rounded-md shadow-sm transition-all duration-200">
                            Daily
                        </button>
                        <button id="monthlyBtn" class="px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 transition-all duration-200">
                            Monthly
                        </button>
                        <button id="yearlyBtn" class="px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 transition-all duration-200">
                            Yearly
                        </button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Order Status Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities Table -->
        <div class="mt-8 bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('products.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Manage Products</p>
                            <p class="text-sm text-gray-600">Add, edit, or delete products</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('orders.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">View Orders</p>
                            <p class="text-sm text-gray-600">Check and manage orders</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('users.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Manage Users</p>
                            <p class="text-sm text-gray-600">View and manage users</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.banners.index') }}" class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <svg class="w-8 h-8 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Manage Banners</p>
                            <p class="text-sm text-gray-600">Add, edit, or delete banners</p>
                        </div>
                    </a>
                    <a href="/faqs" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4C9.243 4 7 6.243 7 9h2c0-1.654 1.346-3 3-3s3 1.346 3 3c0 1.069-.454 1.465-1.481 2.255-.382.294-.813.626-1.226 1.038C10.981 13.604 10.995 14.897 11 15v2h2v-2.009c0-.024.023-.601.707-1.284.32-.32.682-.598 1.031-.867C15.798 12.024 17 11.1 17 9c0-2.757-2.243-5-5-5zm-1 14h2v2h-2z"></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Manage FAQs</p>
                            <p class="text-sm text-gray-600">Add, edit, or delete FAQs</p>
                        </div>
                    </a>
                    <a href="{{ route('orders.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <svg class="w-8 h-8 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d=""></path>
                        </svg>
                        <div>
                            <p class="font-medium text-gray-900">Finance</p>
                            <p class="text-sm text-gray-600">Check and manage finances</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Sales Chart Data
const dailySalesData = @json($sellingChartData);
const monthlySalesData = @json($monthlySalesData);
const yearlySalesData = @json($yearlySalesData);

let currentChart = null;
let currentPeriod = 'daily';

// Initialize Sales Chart
function initSalesChart(period = 'daily') {
    let data, labels, values, title;
    
    switch(period) {
        case 'monthly':
            data = monthlySalesData;
            title = 'Sales Trend (Last 12 Months)';
            break;
        case 'yearly':
            data = yearlySalesData;
            title = 'Sales Trend (Last 5 Years)';
            break;
        default:
            data = dailySalesData;
            title = 'Sales Trend (Last 30 Days)';
    }
    
    labels = Object.keys(data);
    values = Object.values(data);
    
    // Update title
    document.getElementById('salesChartTitle').textContent = title;
    
    // Destroy existing chart if it exists
    if (currentChart) {
        currentChart.destroy();
    }
    
    // Create new chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    currentChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Items Sold',
                data: values,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Button event handlers
function setActiveButton(activeBtn) {
    // Remove active class from all buttons
    document.getElementById('dailyBtn').className = 'px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 transition-all duration-200';
    document.getElementById('monthlyBtn').className = 'px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 transition-all duration-200';
    document.getElementById('yearlyBtn').className = 'px-3 py-1 text-sm font-medium text-gray-500 hover:text-gray-700 transition-all duration-200';
    
    // Add active class to clicked button
    activeBtn.className = 'px-3 py-1 text-sm font-medium text-gray-700 bg-white rounded-md shadow-sm transition-all duration-200';
}

document.getElementById('dailyBtn').addEventListener('click', function() {
    setActiveButton(this);
    currentPeriod = 'daily';
    initSalesChart('daily');
});

document.getElementById('monthlyBtn').addEventListener('click', function() {
    setActiveButton(this);
    currentPeriod = 'monthly';
    initSalesChart('monthly');
});

document.getElementById('yearlyBtn').addEventListener('click', function() {
    setActiveButton(this);
    currentPeriod = 'yearly';
    initSalesChart('yearly');
});

// Initialize chart on page load
document.addEventListener('DOMContentLoaded', function() {
    initSalesChart('daily');
});

// Order Status Chart Data
const statusData = @json($statusOrderChartData);
const statusLabels = Object.keys(statusData);
const statusValues = Object.values(statusData);

// Define colors for different statuses
const statusColors = {
    'pending': '#f59e0b',
    'processing': '#3b82f6',
    'completed': '#10b981',
    'cancelled': '#ef4444',
    'shipped': '#8b5cf6'
};

const backgroundColors = statusLabels.map(status => statusColors[status] || '#6b7280');

// Create Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusValues,
            backgroundColor: backgroundColors,
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection