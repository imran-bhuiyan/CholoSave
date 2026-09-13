@extends('layouts.group_admin')

@section('title', 'View Polls')

@push('styles')
<style>
    .custom-font {
        font-family: 'Poppins', sans-serif;
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

    .editable {
        cursor: pointer;
        border: 1px solid transparent;
        padding: 0.25rem;
        border-radius: 0.25rem;
    }

    .editable:hover {
        border-color: #e5e7eb;
        background-color: #f9fafb;
    }

    .editable:focus {
        outline: none;
        border-color: #3b82f6;
        background-color: white;
    }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-hidden">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-center">
            <h1 class="text-2xl font-semibold custom-font text-black">
                <i class="fa-solid fa-poll mr-2 text-blue-600"></i>
                Group Polls
            </h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6 overflow-auto h-[calc(100vh-4rem)]">
        <div class="max-w-7xl mx-auto animate-fade-in">
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.poll.create', $group->group_id) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Create New Poll
                </a>
            </div>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poll Question</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voting Statistics</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($polls as $index => $poll)
                            <tr data-poll-id="{{ $poll->poll_id }}">
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="editable" 
                                         data-field="poll_question" 
                                         data-poll-id="{{ $poll->poll_id }}"
                                         contenteditable="false">
                                        {{ $poll->poll_question }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1">
                                            <div class="flex items-center">
                                                <span class="text-green-600 font-medium">Yes:</span>
                                                <div class="ml-2 w-32 bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $poll->yes_percentage }}%"></div>
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600">{{ $poll->yes_percentage }}%</span>
                                            </div>
                                            <div class="flex items-center mt-2">
                                                <span class="text-red-600 font-medium">No:</span>
                                                <div class="ml-2 w-32 bg-gray-200 rounded-full h-2.5">
                                                    <div class="bg-red-600 h-2.5 rounded-full" style="width: {{ $poll->no_percentage }}%"></div>
                                                </div>
                                                <span class="ml-2 text-sm text-gray-600">{{ $poll->no_percentage }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select class="status-select bg-transparent border-none focus:ring-0" 
                                            data-poll-id="{{ $poll->poll_id }}">
                                        <option value="active" {{ $poll->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="closed" {{ $poll->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $poll->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="edit-btn text-blue-600 hover:text-blue-900 mr-3" 
                                            data-poll-id="{{ $poll->poll_id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="save-btn text-green-600 hover:text-green-900 mr-3 hidden" 
                                            data-poll-id="{{ $poll->poll_id }}">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <button class="delete-btn text-red-600 hover:text-red-900" 
                                            data-poll-id="{{ $poll->poll_id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No polls found for this group.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make poll question editable
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const pollId = this.dataset.pollId;
            const row = this.closest('tr');
            const questionCell = row.querySelector('[data-field="poll_question"]');
            const saveBtn = row.querySelector('.save-btn');
            
            questionCell.contentEditable = true;
            questionCell.focus();
            this.classList.add('hidden');
            saveBtn.classList.remove('hidden');
        });
    });

    // Save changes
    document.querySelectorAll('.save-btn').forEach(button => {
        button.addEventListener('click', function() {
            const pollId = this.dataset.pollId;
            const row = this.closest('tr');
            const questionCell = row.querySelector('[data-field="poll_question"]');
            const statusSelect = row.querySelector('.status-select');
            const editBtn = row.querySelector('.edit-btn');
            
            const data = {
                poll_question: questionCell.textContent.trim(),
                status: statusSelect.value
            };

            // Show loading state
            Swal.fire({
                title: 'Saving changes...',
                text: 'Please wait while we update the poll.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send update request
            fetch(`/admin/poll/update/${pollId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Poll updated successfully.',
                        confirmButtonColor: '#2563eb'
                    });
                    questionCell.contentEditable = false;
                    this.classList.add('hidden');
                    editBtn.classList.remove('hidden');
                } else {
                    throw new Error(data.message || 'Failed to update poll');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to update poll. Please try again.',
                    confirmButtonColor: '#2563eb'
                });
            });
        });
    });

    // Delete poll
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const pollId = this.dataset.pollId;
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the poll and all its votes!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting poll...',
                        text: 'Please wait while we delete the poll.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Send delete request
                    fetch(`/admin/poll/delete/${pollId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Poll has been deleted successfully.',
                                confirmButtonColor: '#2563eb'
                            }).then(() => {
                                // Remove the row from the table
                                this.closest('tr').remove();
                            });
                        } else {
                            throw new Error(data.message || 'Failed to delete poll');
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: error.message || 'Failed to delete poll. Please try again.',
                            confirmButtonColor: '#2563eb'
                        });
                    });
                }
            });
        });
    });

    // Status change
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const pollId = this.dataset.pollId;
            const row = this.closest('tr');
            const saveBtn = row.querySelector('.save-btn');
            const editBtn = row.querySelector('.edit-btn');
            
            // Show save button
            saveBtn.classList.remove('hidden');
            editBtn.classList.add('hidden');
        });
    });
});
</script>
@endpush

@endsection 