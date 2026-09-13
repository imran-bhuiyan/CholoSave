@extends('layouts.group_admin')

@section('title', 'Group Settings')

@section('content')
<div class="content p-6 overflow-auto h-[calc(100vh-4rem)]">
    <div class="container mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-3">Group Settings</h1>
            <p class="text-gray-600">Manage your group's configuration and payment details</p>
        </div>

        <!-- Settings Form -->
        <div class="max-w-4xl mx-auto">
            <form action="{{ route('groups.admin.settings.update', $group->group_id) }}" method="POST" class="bg-white rounded-2xl shadow-lg p-8">
                @csrf
                @method('PUT')

                <!-- Basic Settings -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Basic Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Installment Amount (৳)</label>
                            <input type="number" name="amount" id="amount" value="{{ $group->amount }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required min="0" step="0.01">
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $group->start_date->format('Y-m-d') }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="goal_amount" class="block text-sm font-medium text-gray-700 mb-2">Goal Amount (৳)</label>
                            <input type="number" name="goal_amount" id="goal_amount" value="{{ $group->goal_amount }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required min="0" step="0.01">
                        </div>
                        <div>
                            <label for="emergency_fund" class="block text-sm font-medium text-gray-700 mb-2">Emergency Fund (৳)</label>
                            <input type="number" name="emergency_fund" id="emergency_fund" value="{{ $group->emergency_fund }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required min="0" step="0.01">
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Payment Methods</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="bKash" class="block text-sm font-medium text-gray-700 mb-2">bKash Number</label>
                            <input type="text" name="bKash" id="bKash" value="{{ $group->bKash }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                maxlength="20">
                        </div>
                        <div>
                            <label for="Rocket" class="block text-sm font-medium text-gray-700 mb-2">Rocket Number</label>
                            <input type="text" name="Rocket" id="Rocket" value="{{ $group->Rocket }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                maxlength="20">
                        </div>
                        <div>
                            <label for="Nagad" class="block text-sm font-medium text-gray-700 mb-2">Nagad Number</label>
                            <input type="text" name="Nagad" id="Nagad" value="{{ $group->Nagad }}" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                maxlength="20">
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>

            <!-- Close Savings Button (SweetAlert2) -->
            <div class="mt-8 text-center">
                <button 
                    id="close-savings-btn"
                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2">
                    <i class="fas fa-lock mr-2"></i>
                    Close Savings
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('close-savings-btn').addEventListener('click', async function(e) {
        e.preventDefault();
        // Step 1
        let result = await Swal.fire({
            title: 'Confirmation Required',
            text: 'Have all members received their savings?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        });
        if (!result.isConfirmed) {
            Swal.fire('Action Cancelled', 'All members must receive their savings before closing.', 'error');
            return;
        }
        // Step 2
        result = await Swal.fire({
            title: 'Confirmation Required',
            text: 'Have all members received their profit?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        });
        if (!result.isConfirmed) {
            Swal.fire('Action Cancelled', 'All members must receive their profit before closing.', 'error');
            return;
        }
        // Step 3
        result = await Swal.fire({
            title: 'Confirmation Required',
            text: 'Do all members agree to close the savings?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        });
        if (!result.isConfirmed) {
            Swal.fire('Action Cancelled', 'All members must agree to close the savings.', 'error');
            return;
        }
        // Step 4
        result = await Swal.fire({
            title: 'Confirmation Required',
            text: 'Is savings successfully completed?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            reverseButtons: true
        });
        if (!result.isConfirmed) {
            Swal.fire('Action Cancelled', 'Savings must be successfully completed before closing.', 'error');
            return;
        }
        // Final irreversible warning
        result = await Swal.fire({
            title: 'Warning!',
            html: "Closing savings means <b class='text-red-600'>all group data will be permanently deleted</b> and you can't recover it. Are you absolutely sure?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Close Savings',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true
        });
        if (!result.isConfirmed) {
            Swal.fire('Cancelled', 'No changes were made.', 'info');
            return;
        }
        // Show loading
        Swal.fire({
            title: 'Processing...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        // Send AJAX request
        try {
            const response = await fetch(`{{ route('groups.admin.close-savings', $group->group_id) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                },
                body: JSON.stringify({})
            });
            const data = await response.json();
            if (data.success) {
                Swal.fire('Success', data.message || 'Savings closed and all group data deleted successfully.', 'success').then(() => {
                    window.location.href = '{{ route('groups.my') }}';
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to close savings. Please try again.', 'error');
            }
        } catch (e) {
            Swal.fire('Error', 'An error occurred. Please try again.', 'error');
        }
    });
});
</script>
@endpush
@endsection 