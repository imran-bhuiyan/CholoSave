@extends('layouts.group_admin')

@section('title', 'Admin - Member Payment History')

@push('styles')
<style>
    .custom-font {
        font-family: 'Poppins', sans-serif;
    }

    .table-container {
        scrollbar-width: thin;
        scrollbar-color: #CBD5E0 #EDF2F7;
    }

    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #EDF2F7;
    }

    .table-container::-webkit-scrollbar-thumb {
        background-color: #CBD5E0;
        border-radius: 4px;
    }

    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Force black text in light mode */
    .force-light h1,
    .force-light h2,
    .force-light th,
    .force-light td {
        color: #000 !important;
    }

    .search-highlight {
        background-color: #fef3c7;
        padding: 2px 4px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-hidden">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-center">
            <h1 class="text-2xl font-semibold custom-font text-black">
                <i class="fa-solid fa-users-cog mr-2 text-blue-600" style="color: #2563eb;"></i>
                Group Payment History
            </h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6 overflow-auto h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto animate-fade-in">
            
            <!-- Search and Filter Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search by Transaction ID -->
                    <div>
                        <label for="transaction_search" class="block text-sm font-medium text-gray-700 mb-2">Search Transaction ID</label>
                        <input type="text" 
                               id="transaction_search" 
                               name="transaction_search" 
                               placeholder="Enter transaction ID..."
                               value="{{ request('transaction_search') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter by Payment Method -->
                    <div>
                        <label for="payment_method_filter" class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select id="payment_method_filter" 
                                name="payment_method_filter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Methods</option>
                            <option value="bKash" {{ request('payment_method_filter') == 'bKash' ? 'selected' : '' }}>bKash</option>
                            <option value="Rocket" {{ request('payment_method_filter') == 'Rocket' ? 'selected' : '' }}>Rocket</option>
                            <option value="Nagad" {{ request('payment_method_filter') == 'Nagad' ? 'selected' : '' }}>Nagad</option>
                        </select>
                    </div>

                    <!-- Filter by Date Range -->
                    <div>
                        <label for="date_filter" class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                        <select id="date_filter" 
                                name="date_filter" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>This Week</option>
                            <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>This Month</option>
                            <option value="year" {{ request('date_filter') == 'year' ? 'selected' : '' }}>This Year</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center mt-4">
                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="applyFilters()" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>
                            Search & Filter
                        </button>
                        
                        <button type="button" 
                                onclick="clearFilters()" 
                                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50 transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </button>
                    </div>
                    
                    <a href="{{ route('admin.member.payment.export', $group->group_id) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>
                        Export CSV
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-receipt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Payments</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalPayments }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Amount</p>
                            <p class="text-2xl font-bold text-gray-900">৳{{ number_format($totalAmount, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Members</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $activeMembers }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Avg. Payment</p>
                            <p class="text-2xl font-bold text-gray-900">৳{{ number_format($averagePayment, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method Breakdown -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method Breakdown</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @php
                        $bKashPayments = $transactions->where('payment_method', 'bKash')->count();
                        $rocketPayments = $transactions->where('payment_method', 'Rocket')->count();
                        $nagadPayments = $transactions->where('payment_method', 'Nagad')->count();
                        $otherPayments = $transactions->whereNotIn('payment_method', ['bKash', 'Rocket', 'Nagad'])->count();
                    @endphp
                    
                    <div class="flex items-center p-4 bg-pink-50 rounded-lg">
                        <div class="p-2 bg-pink-100 rounded-lg">
                            <i class="fas fa-mobile-alt text-pink-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">bKash</p>
                            <p class="text-xl font-bold text-gray-900">{{ $bKashPayments }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-blue-50 rounded-lg">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="fas fa-rocket text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Rocket</p>
                            <p class="text-xl font-bold text-gray-900">{{ $rocketPayments }}</p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-green-50 rounded-lg">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="fas fa-wallet text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Nagad</p>
                            <p class="text-xl font-bold text-gray-900">{{ $nagadPayments }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-black">Payment Details</h2>
                    <div class="text-sm text-gray-600">
                        Showing {{ $transactions->count() }} of {{ $totalPayments }} payments
                    </div>
                </div>
                <div class="table-container overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (BDT)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transactions as $index => $transaction)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-3">
                                                {{ strtoupper(substr($transaction->user->name, 0, 1)) }}
                                            </div>
                                            {{ $transaction->user->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 transaction-id">
                                        {{ $transaction->transaction_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">
                                        ৳{{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($transaction->payment_method == 'bKash') bg-pink-100 text-pink-800
                                            @elseif($transaction->payment_method == 'Rocket') bg-blue-100 text-blue-800
                                            @elseif($transaction->payment_method == 'Nagad') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            <i class="fas fa-credit-card mr-1"></i>
                                            {{ $transaction->payment_method ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transaction->payment_time ? \Carbon\Carbon::parse($transaction->payment_time)->format('M d, Y H:i') : 'Not Set' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Completed
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center py-8">
                                            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-lg font-medium text-gray-500">No payments found</p>
                                            <p class="text-sm text-gray-400">Try adjusting your search criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script>
    function applyFilters() {
        const transactionSearch = document.getElementById('transaction_search').value;
        const paymentMethod = document.getElementById('payment_method_filter').value;
        const dateFilter = document.getElementById('date_filter').value;

        let url = new URL(window.location);
        
        if (transactionSearch) {
            url.searchParams.set('transaction_search', transactionSearch);
        } else {
            url.searchParams.delete('transaction_search');
        }
        
        if (paymentMethod) {
            url.searchParams.set('payment_method_filter', paymentMethod);
        } else {
            url.searchParams.delete('payment_method_filter');
        }
        
        if (dateFilter) {
            url.searchParams.set('date_filter', dateFilter);
        } else {
            url.searchParams.delete('date_filter');
        }

        window.location.href = url.toString();
    }

    function clearFilters() {
        window.location.href = window.location.pathname;
    }

    // Highlight search terms in transaction IDs
    document.addEventListener('DOMContentLoaded', function() {
        const searchTerm = '{{ request('transaction_search') }}';
        if (searchTerm) {
            const transactionIds = document.querySelectorAll('.transaction-id');
            transactionIds.forEach(element => {
                const text = element.textContent;
                const highlightedText = text.replace(
                    new RegExp(searchTerm, 'gi'),
                    match => `<span class="search-highlight">${match}</span>`
                );
                element.innerHTML = highlightedText;
            });
        }
    });

    // Enter key to search
    document.getElementById('transaction_search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
</script>
@endpush

@endsection 