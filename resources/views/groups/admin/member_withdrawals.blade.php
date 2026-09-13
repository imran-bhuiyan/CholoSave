@extends('layouts.group_admin')

@push('styles')
<style>
    .filter-btn.active {
        background-color: #10B981;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-black mb-2">Member Withdrawal Requests</h1>
        <p class="text-lg text-black">Manage and track all withdrawal requests from group members</p>
    </div>

    <!-- Filter Buttons -->
    <div class="flex justify-center space-x-4 mb-6">
        <button onclick="filterWithdrawals('all')" class="filter-btn active px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            All
        </button>
        <button onclick="filterWithdrawals('pending')" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            Pending
        </button>
        <button onclick="filterWithdrawals('approved')" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            Approved
        </button>
        <button onclick="filterWithdrawals('declined')" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            Declined
        </button>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Withdrawal Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Contribution</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Payment Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($withdrawals as $index => $withdrawal)
                    <tr class="withdrawal-row" data-status="{{ $withdrawal->status }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $withdrawal->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">৳{{ number_format($withdrawal->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">৳{{ number_format($withdrawal->user->savings->where('group_id', $group->group_id)->sum('amount'), 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $withdrawal->payment_method }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $withdrawal->payment_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $withdrawal->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $withdrawal->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $withdrawal->status === 'declined' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($withdrawal->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            @if($withdrawal->status === 'pending')
                            <div class="flex space-x-2">
                                <button onclick="approveWithdrawal({{ $withdrawal->withdrawal_id }})" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="declineWithdrawal({{ $withdrawal->withdrawal_id }})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-black">No withdrawal requests found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function filterWithdrawals(status) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Filter rows
    const rows = document.querySelectorAll('.withdrawal-row');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function approveWithdrawal(withdrawalId) {
    Swal.fire({
        title: 'Approve Withdrawal Request?',
        text: 'Are you sure you want to approve this withdrawal request?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/groups/{{ $group->group_id }}/admin/withdrawals/${withdrawalId}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Approved!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to approve withdrawal request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to approve withdrawal request. Please try again.', 'error');
            });
        }
    });
}

function declineWithdrawal(withdrawalId) {
    Swal.fire({
        title: 'Decline Withdrawal Request?',
        text: 'Are you sure you want to decline this withdrawal request?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, decline it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/groups/{{ $group->group_id }}/admin/withdrawals/${withdrawalId}/decline`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('Declined!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to decline withdrawal request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to decline withdrawal request. Please try again.', 'error');
            });
        }
    });
}
</script>
@endpush
@endsection 