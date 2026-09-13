@extends('layouts.group_admin')

@section('title', 'Group Members')

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
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-header {
        background-color: #F9FAFB;
        color: #000000;
        font-weight: 600;
    }

    .table-cell {
        color: #000000;
    }

    .member-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto p-6 animate-fade-in">
    <!-- Header Section -->
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-semibold custom-font text-black mb-2">
            <i class="fa-solid fa-users mr-2 text-blue-600"></i>
            Group Members
        </h2>
        <p class="text-lg text-black">View and manage your group members</p>
    </div>

    <div class="member-card overflow-hidden">
        <div class="table-container overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">Join Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">Contribution</th>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">Current Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">Installments Left</th>
                        <th class="px-6 py-3 text-left text-xs font-medium table-header uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($members as $i => $member)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap table-cell">{{ $i + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-gray-500"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium table-cell">
                                            {{ $member['name'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm table-cell">
                                {{ \Carbon\Carbon::parse($member['join_date'])->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $isAdmin = isset($member['is_admin']) ? $member['is_admin'] : 0;
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $isAdmin ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $isAdmin ? 'Admin' : 'Member' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm table-cell">
                                {{ number_format($member['contribution'] ?? 0) }} BDT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm table-cell">
                                <span class="font-medium text-blue-600">{{ number_format($member['current_balance'], 2) }} BDT</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="ml-2 text-sm table-cell">{{ $member['remaining_installment'] }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $isAdmin = isset($member['is_admin']) ? $member['is_admin'] : 0; @endphp
                                @if(!$isAdmin)
                                    <button class="make-admin-btn px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition" data-user-id="{{ $member['user_id'] ?? '' }}">Make Admin</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center table-cell">No members found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.make-admin-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const groupId = @json($group->group_id);
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to transfer admin rights to this member. You will be logged out.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, transfer!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Transferring admin rights...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                        fetch(`/groups/${groupId}/admin/transfer-admin`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ new_admin_user_id: userId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.redirect) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: 'Admin rights transferred! You will be logged out.',
                                    icon: 'success',
                                    allowOutsideClick: false
                                }).then(() => {
                                    window.location.href = data.redirect;
                                });
                            } else {
                                Swal.fire('Error', data.error || 'Failed to transfer admin rights.', 'error');
                            }
                        })
                        .catch(() => Swal.fire('Error', 'Failed to transfer admin rights.', 'error'));
                    }
                });
            });
        });
    });
</script>
@endpush 