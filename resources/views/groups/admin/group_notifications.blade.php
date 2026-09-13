@extends('layouts.group_admin')

@section('title', 'Group Notifications')

@push('styles')
<style>
    .notification-card {
        transition: all 0.3s ease;
        animation: slideIn 0.5s ease-out;
    }
    
    .notification-card:hover {
        transform: translateY(-2px);
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .icon-container {
        transition: all 0.3s ease;
    }
    
    .notification-card:hover .icon-container {
        transform: scale(1.1);
    }
</style>
@endpush

@section('content')
    <div class="content p-6 overflow-auto h-[calc(100vh-4rem)]">
        <div class="container mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-3">Group Notifications</h1>
                <div class="flex items-center justify-center gap-4">
                    <span class="inline-flex items-center justify-center px-4 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">
                        {{ count($notifications) }} unread notification{{ count($notifications) !== 1 ? 's' : '' }}
                    </span>
                    @if(count($notifications) > 0)
                        <button onclick="clearAllNotifications()" class="inline-flex items-center justify-center px-4 py-1 bg-red-100 text-red-800 rounded-full font-semibold hover:bg-red-200 transition-colors duration-200">
                            <i class="fas fa-check-double mr-2"></i>
                            Mark All as Read
                        </button>
                    @endif
                </div>
            </div>

            <!-- Notifications List -->
            <div class="max-w-4xl mx-auto mb-12">
                @if (empty($notifications))
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <div class="icon-container mb-6">
                            <i class="fas fa-bell-slash text-6xl text-gray-300"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No Unread Notifications</h3>
                        <p class="text-gray-500">Your group notification center is empty at the moment</p>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach ($notifications as $index => $notification)
                            @php
                                $icon = match($notification['type']) {
                                    'loan_approval' => 'fa-hand-holding-dollar',
                                    'withdrawal' => 'fa-money-bill-transfer',
                                    'join_request' => 'fa-user-plus',
                                    'payment_reminder' => 'fa-clock',
                                    'group_loan_request' => 'fa-money-bill',
                                    'leave_request' => 'fa-user-minus',
                                    'group_withdraw_request' => 'fa-arrow-right-from-bracket',
                                    default => 'fa-bell'
                                };
                                
                                $iconColor = match($notification['type']) {
                                    'loan_approval' => 'text-green-600',
                                    'withdrawal' => 'text-blue-600',
                                    'join_request' => 'text-purple-600',
                                    'payment_reminder' => 'text-orange-600',
                                    'group_loan_request' => 'text-indigo-600',
                                    'leave_request' => 'text-red-600',
                                    'group_withdraw_request' => 'text-yellow-600',
                                    default => 'text-blue-600'
                                };
                            @endphp
                            <div class="notification-card bg-white rounded-2xl shadow-md p-6"
                                 style="animation-delay: {{ $index * 0.1 }}s">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="icon-container w-14 h-14 bg-white rounded-xl shadow-sm flex items-center justify-center">
                                            <i class="fas {{ $icon }} {{ $iconColor }} text-2xl"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow">
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                {{ htmlspecialchars($notification['title']) }}
                                            </h3>
                                            <span class="text-sm font-medium text-gray-500">
                                                {{ date('M j, Y g:i A', strtotime($notification['created_at'])) }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 leading-relaxed">
                                            {{ htmlspecialchars($notification['message']) }}
                                        </p>
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
    <script>
        function clearAllNotifications() {
            if (confirm('Are you sure you want to mark all notifications as read?')) {
                fetch(`{{ route('groups.admin.notifications.clear-all', $group->group_id) }}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to show updated notifications
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while marking notifications as read.');
                });
            }
        }
    </script>
    @endpush
@endsection 