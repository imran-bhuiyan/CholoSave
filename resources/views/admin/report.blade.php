@extends('layouts.site_admin')

@section('title', 'Contact Reports')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Centered Header Section -->
        <div class="mb-10 flex flex-col items-center justify-center text-center">
            <div class="flex items-center justify-center space-x-3 mb-3">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-600 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Contact Reports</h1>
            </div>
            <p class="text-lg text-gray-500 font-medium">Manage and review customer inquiries and feedback</p>
        </div>

        @if($contacts->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No contact messages</h3>
                <p class="text-gray-500">There are currently no contact form submissions to review.</p>
            </div>
        @else
            <!-- Clean Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Messages -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Messages</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $contacts->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Completed -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $contacts->where('status', 'done')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $contacts->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clean Table -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Table Header -->
                <div class="px-8 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">Contact Messages</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800 shadow-sm">
                            {{ $contacts->count() }} messages
                        </span>
                    </div>
                </div>
                
                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-blue-50/70">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Contact</th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Message</th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date & Status</th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @foreach($contacts as $contact)
                            <tr class="hover:bg-blue-50/40 transition-colors duration-200 group">
                                <!-- Contact Info -->
                                <td class="px-8 py-5 align-top">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4 shadow-sm group-hover:scale-105 transition-transform">
                                            <span class="text-lg font-bold text-blue-700">
                                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-base font-semibold text-gray-900">{{ $contact->name }}</div>
                                            <div class="text-sm text-gray-500">
                                                <a href="mailto:{{ $contact->email }}" class="hover:text-blue-600 transition-colors duration-150 underline">
                                                    {{ $contact->email }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Message -->
                                <td class="px-8 py-5 align-top">
                                    <div class="max-w-xs">
                                        <div class="text-sm text-gray-900" id="short-message-{{ $contact->id }}" style="display: block;">
                                            {{ Str::limit($contact->description, 100) }}
                                            @if(strlen($contact->description) > 100)
                                                <button type="button" class="text-blue-600 hover:text-blue-700 font-medium ml-1 transition-colors duration-150 focus:outline-none underline" onclick="toggleMessage('{{ $contact->id }}')">
                                                    Read more
                                                </button>
                                            @endif
                                        </div>
                                        <div id="full-message-{{ $contact->id }}" class="hidden mt-2 p-4 bg-blue-50 rounded-lg text-sm text-gray-700 border-l-4 border-blue-400 shadow-inner">
                                            {{ $contact->description }}
                                            <button type="button" class="text-blue-600 hover:text-blue-700 font-medium ml-1 mt-1 transition-colors duration-150 focus:outline-none underline" onclick="toggleMessage('{{ $contact->id }}')">
                                                Show less
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Date & Status -->
                                <td class="px-8 py-5 align-top">
                                    <div class="space-y-2">
                                        <div class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($contact->created_at)->format('M d, Y \a\t g:i A') }}
                                        </div>
                                        <div>
                                            @if($contact->status === 'done')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 shadow-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 shadow-sm">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Actions -->
                                <td class="px-8 py-5 align-top">
                                    <div class="flex items-center space-x-2">
                                        @if($contact->status !== 'done')
                                            <button onclick="markAsDone({{ $contact->id }})" class="inline-flex items-center px-4 py-2 border border-transparent shadow-md text-xs font-bold rounded-lg text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400 transition-colors duration-150">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Mark Done
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function toggleMessage(contactId) {
    const shortMessage = document.getElementById(`short-message-${contactId}`);
    const fullMessage = document.getElementById(`full-message-${contactId}`);
    if (fullMessage.classList.contains('hidden')) {
        shortMessage.style.display = 'none';
        fullMessage.classList.remove('hidden');
    } else {
        shortMessage.style.display = 'block';
        fullMessage.classList.add('hidden');
    }
}

function markAsDone(contactId) {
    Swal.fire({
        title: 'Mark as completed?',
        text: 'Are you sure you want to mark this contact as completed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, mark as done',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'rounded-lg',
            cancelButton: 'rounded-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait while we update the status',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/contact/${contactId}/mark-done`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Contact has been marked as completed',
                        icon: 'success',
                        confirmButtonColor: '#059669',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-lg'
                        }
                    }).then(() => {
                        // Refresh the page to show updated status
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to update status. Please try again.',
                        icon: 'error',
                        confirmButtonColor: '#DC2626',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-lg'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#DC2626',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'rounded-lg'
                    }
                });
            });
        }
    });
}
</script>
@endsection