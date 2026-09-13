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
        <h1 class="text-3xl font-bold text-black mb-2">Member Loan Requests</h1>
        <p class="text-lg text-black">Manage and track all loan requests from group members</p>
    </div>

    <!-- Filter Buttons -->
    <div class="flex justify-center space-x-4 mb-6">
        <button onclick="filterLoans('all')" class="filter-btn active px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            All
        </button>
        <button onclick="filterLoans('pending')" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            Pending
        </button>
        <button onclick="filterLoans('approved')" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            Approved
        </button>
        <button onclick="filterLoans('declined')" class="filter-btn px-4 py-2 rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors duration-200">
            Declined
        </button>
    </div>

    <!-- Loans Table -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Loan Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Contribution</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Request Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-black uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($loans as $index => $loan)
                    <tr class="loan-row" data-status="{{ $loan->status }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $loan->user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">৳{{ number_format($loan->amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">৳{{ number_format($loan->user->savings->where('group_id', $group->group_id)->sum('amount'), 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">{{ $loan->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $loan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $loan->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $loan->status === 'declined' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                            @if($loan->status === 'pending')
                            <div class="flex space-x-2">
                                <button onclick="approveLoan({{ $loan->id }})" class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="declineLoan({{ $loan->id }})" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-black">No loan requests found</td>
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
function filterLoans(status) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');

    // Filter rows
    const rows = document.querySelectorAll('.loan-row');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function approveLoan(loanId) {
    Swal.fire({
        title: 'Approve Loan Request?',
        text: 'Are you sure you want to approve this loan request?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/groups/{{ $group->group_id }}/admin/loans/${loanId}/approve`, {
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
                    Swal.fire('Error!', data.message || 'Failed to approve loan request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to approve loan request. Please try again.', 'error');
            });
        }
    });
}

function declineLoan(loanId) {
    Swal.fire({
        title: 'Decline Loan Request?',
        text: 'Are you sure you want to decline this loan request?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, decline it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/groups/{{ $group->group_id }}/admin/loans/${loanId}/decline`, {
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
                    Swal.fire('Error!', data.message || 'Failed to decline loan request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to decline loan request. Please try again.', 'error');
            });
        }
    });
}
</script>
@endpush
@endsection 