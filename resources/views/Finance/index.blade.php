@extends('layouts.app')

@section('title', 'Finance Management')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }
    
    .modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Finance Management</h1>
                    <p class="mt-2 text-gray-600">Monitor your business financial performance</p>
                </div>
                @if(auth()->check() && auth()->user()->role === 'admin')
                <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Add Outcome
                </button>
                @endif
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Income</p>
                        <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Outcome</p>
                        <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($totalOutcome, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Net Profit</p>
                        <p class="text-2xl font-semibold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($netProfit, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Monthly Profit</p>
                        <p class="text-2xl font-semibold {{ $monthlyProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($monthlyProfit, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Profit Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Profit Trend</h3>
                <canvas id="monthlyProfitChart" width="400" height="200"></canvas>
            </div>

            <!-- Annual Summary Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Annual Financial Summary</h3>
                <canvas id="annualSummaryChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Outcomes List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Outcomes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($outcomes as $outcome)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $outcome->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Rp {{ number_format($outcome->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $outcome->created_at->format('d M Y, H:i') }}</div>
                            </td>
                            @if(auth()->check() && auth()->user()->role === 'admin')
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="openEditModal({{ $outcome->id }}, '{{ $outcome->description }}', {{ $outcome->amount }})" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <form action="{{ route('finance.destroy', $outcome->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this outcome?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->check() && auth()->user()->role === 'admin' ? '4' : '3' }}" class="px-6 py-4 text-center text-gray-500">
                                No outcomes found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Outcome Modal -->
@if(auth()->check() && auth()->user()->role === 'admin')
<div id="addOutcomeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddModal()">&times;</span>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Add New Outcome</h2>
        <form action="{{ route('finance.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" id="description" name="description" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Enter outcome description">
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (Rp)</label>
                <input type="number" id="amount" name="amount" required step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Enter amount">
                @error('amount')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                    Add Outcome
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Outcome Modal -->
<div id="editOutcomeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Edit Outcome</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input type="text" id="edit_description" name="description" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="mb-6">
                <label for="edit_amount" class="block text-sm font-medium text-gray-700 mb-2">Amount (Rp)</label>
                <input type="number" id="edit_amount" name="amount" required step="0.01" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                    Update Outcome
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
// Modal functions
function openAddModal() {
    document.getElementById('addOutcomeModal').classList.add('show');
}

function closeAddModal() {
    document.getElementById('addOutcomeModal').classList.remove('show');
}

function openEditModal(id, description, amount) {
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_amount').value = amount;
    document.getElementById('editForm').action = `/finance/${id}`;
    document.getElementById('editOutcomeModal').classList.add('show');
}

function closeEditModal() {
    document.getElementById('editOutcomeModal').classList.remove('show');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addOutcomeModal');
    const editModal = document.getElementById('editOutcomeModal');
    if (event.target == addModal) {
        closeAddModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
}

// Monthly Profit Chart
const monthlyData = @json($monthlyData);
const monthlyLabels = monthlyData.map(data => data.month);
const monthlyProfits = monthlyData.map(data => data.profit);
const monthlyIncomes = monthlyData.map(data => data.income);
const monthlyOutcomes = monthlyData.map(data => data.outcome);

const monthlyCtx = document.getElementById('monthlyProfitChart').getContext('2d');
const monthlyChart = new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: monthlyLabels,
        datasets: [{
            label: 'Profit',
            data: monthlyProfits,
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4
        }, {
            label: 'Income',
            data: monthlyIncomes,
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4
        }, {
            label: 'Outcome',
            data: monthlyOutcomes,
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

// Annual Summary Chart
const annualCtx = document.getElementById('annualSummaryChart').getContext('2d');
const annualChart = new Chart(annualCtx, {
    type: 'doughnut',
    data: {
        labels: ['Income', 'Outcome', 'Profit'],
        datasets: [{
            data: [{{ $yearlyIncome }}, {{ $yearlyOutcome }}, {{ $yearlyProfit >= 0 ? $yearlyProfit : 0 }}],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)',
                'rgb(59, 130, 246)'
            ],
            borderColor: [
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)',
                'rgb(59, 130, 246)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': Rp ' + context.parsed.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection
