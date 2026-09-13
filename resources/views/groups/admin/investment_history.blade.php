@extends('layouts.group_admin')

@section('title', 'Investment History')

@push('styles')
<style>
    .custom-font {
        font-family: 'Poppins', sans-serif;
    }

    .investment-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .profit {
        color: #10B981;
    }

    .loss {
        color: #EF4444;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-pending {
        background-color: #FEF3C7;
        color: #92400E;
    }

    .status-completed {
        background-color: #D1FAE5;
        color: #065F46;
    }

    .table-container {
        overflow-x: auto;
    }

    .investment-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .investment-table th {
        background-color: #F9FAFB;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #000000;
        border-bottom: 2px solid #E5E7EB;
    }

    .investment-table td {
        padding: 1rem;
        border-bottom: 1px solid #E5E7EB;
        color: #000000;
    }

    .investment-table tr:hover {
        background-color: #F9FAFB;
    }

    .card-title {
        color: #000000;
    }

    .card-value {
        color: #000000;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="p-6">
    <!-- Header Section -->
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-semibold custom-font text-black mb-2">
            <i class="fas fa-history mr-2 text-blue-600"></i>
            Investment History
        </h2>
        <p class="text-lg text-black">Track all investments and their returns</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Investments -->
        <div class="investment-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-black card-title">Total Investments</p>
                    <h3 class="text-2xl font-semibold card-value">৳{{ number_format($totalInvestments, 2) }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-piggy-bank text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Returns -->
        <div class="investment-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-black card-title">Total Returns</p>
                    <h3 class="text-2xl font-semibold card-value">৳{{ number_format($totalReturns, 2) }}</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Net Profit/Loss -->
        <div class="investment-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-black card-title">Net Profit/Loss</p>
                    <h3 class="text-2xl font-semibold {{ $netProfitLoss >= 0 ? 'profit' : 'loss' }} card-value">
                        ৳{{ number_format(abs($netProfitLoss), 2) }}
                        <span class="text-sm">{{ $netProfitLoss >= 0 ? 'Profit' : 'Loss' }}</span>
                    </h3>
                </div>
                <div class="w-12 h-12 {{ $netProfitLoss >= 0 ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center">
                    <i class="fas {{ $netProfitLoss >= 0 ? 'fa-arrow-trend-up text-green-600' : 'fa-arrow-trend-down text-red-600' }} text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Investments Table -->
    <div class="investment-card p-6">
        <div class="table-container">
            <table class="investment-table">
                <thead>
                    <tr>
                        <th>Investment Type</th>
                        <th>Amount</th>
                        <th>Expected Return</th>
                        <th>Expected Date</th>
                        <th>Actual Return</th>
                        <th>Return Date</th>
                        <th>Profit/Loss</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($investments as $investment)
                        <tr>
                            <td>{{ $investment->investment_type }}</td>
                            <td>৳{{ number_format($investment->amount, 2) }}</td>
                            <td>৳{{ number_format($investment->ex_profit, 2) }}</td>
                            <td>{{ $investment->ex_return_date->format('M d, Y') }}</td>
                            <td>
                                @if($investment->returns->isNotEmpty())
                                    ৳{{ number_format($investment->returns->sum('amount'), 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($investment->returns->isNotEmpty())
                                    {{ $investment->returns->last()->created_at->format('M d, Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($investment->returns->isNotEmpty())
                                    @php
                                        $actualReturn = $investment->returns->sum('amount');
                                        $profitLoss = $actualReturn - $investment->amount;
                                    @endphp
                                    <span class="{{ $profitLoss >= 0 ? 'profit' : 'loss' }}">
                                        ৳{{ number_format(abs($profitLoss), 2) }}
                                        ({{ $profitLoss >= 0 ? 'Profit' : 'Loss' }})
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ $investment->status }}">
                                    {{ ucfirst($investment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-black">
                                No investments found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($investments->hasPages())
            <div class="mt-6">
                {{ $investments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection 