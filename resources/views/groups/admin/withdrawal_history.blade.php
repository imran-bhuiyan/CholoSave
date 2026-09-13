@extends('layouts.group_admin')

@section('title', 'My Withdrawal History')

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

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .status-approved {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fcd34d;
    }

     .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-hidden">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-center">
            <h1 class="text-2xl font-semibold custom-font text-black">
                <i class="fa-solid fa-money-bill-transfer mr-2 text-blue-600" style="color: #2563eb;"></i>
                My Withdrawal History
            </h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6 overflow-auto h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto animate-fade-in">
            <!-- Withdrawal History Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-black">My Withdrawal Details</h2>
                </div>
                <div class="table-container overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount (BDT)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approve Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($withdrawals as $withdrawal)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳{{ number_format($withdrawal->amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $withdrawal->payment_method ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $withdrawal->payment_number ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $withdrawal->created_at ? $withdrawal->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge 
                                            @if($withdrawal->status === 'approved') status-approved
                                            @elseif($withdrawal->status === 'pending') status-pending
                                            @elseif($withdrawal->status === 'rejected') status-rejected
                                            @else text-gray-600 bg-gray-100 border border-gray-200
                                            @endif">
                                            {{ ucfirst($withdrawal->status ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $withdrawal->approve_date ? $withdrawal->approve_date->format('M d, Y') : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No withdrawal history found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection 