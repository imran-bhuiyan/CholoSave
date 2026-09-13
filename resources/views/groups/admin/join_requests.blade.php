@extends('layouts.group_admin')

@section('title', 'Join Requests')

@section('content')
<div class="content p-6 overflow-auto h-[calc(100vh-4rem)]">
    <div class="container mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-3">Join Requests</h1>
            <p class="text-gray-600">Review and manage requests from users who want to join your group</p>
        </div>

        <!-- Stats Card -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Pending Requests</h2>
                        <p class="text-gray-600 mt-1">Total requests awaiting your review</p>
                    </div>
                    <div class="text-4xl font-bold text-blue-600">{{ $pendingRequests->count() }}</div>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="max-w-4xl mx-auto">
            @if($pendingRequests->isEmpty())
                <div class="bg-white rounded-2xl shadow-lg p-8 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-inbox text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Pending Requests</h3>
                    <p class="text-gray-500">There are no join requests to review at this time.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($pendingRequests as $request)
                        <div class="bg-white rounded-2xl shadow-lg p-6 transition-all duration-200 hover:shadow-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $request->user->name }}</h3>
                                        <p class="text-sm text-gray-500">Requested {{ $request->join_request_date ? $request->join_request_date->diffForHumans() : $request->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-3">
                                    <button onclick="approveRequest({{ $request->membership_id }})" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                                        <i class="fas fa-check mr-2"></i>Approve
                                    </button>
                                    <button onclick="rejectRequest({{ $request->membership_id }})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                        <i class="fas fa-times mr-2"></i>Reject
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function approveRequest(requestId) {
    Swal.fire({
        title: 'Approve Join Request?',
        text: 'Are you sure you want to approve this join request?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/groups/{{ $group->group_id }}/join-requests/${requestId}/approve`, {
                method: 'PUT',
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
                    Swal.fire('Error!', data.message || 'Failed to approve join request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to approve join request. Please try again.', 'error');
            });
        }
    });
}

function rejectRequest(requestId) {
    Swal.fire({
        title: 'Reject Join Request?',
        text: 'Are you sure you want to reject this join request?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, reject it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/groups/{{ $group->group_id }}/join-requests/${requestId}/reject`, {
                method: 'PUT',
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
                    Swal.fire('Rejected!', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error!', data.message || 'Failed to reject join request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error!', 'Failed to reject join request. Please try again.', 'error');
            });
        }
    });
}
</script>
@endpush
@endsection 