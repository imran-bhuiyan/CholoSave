@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Header Section -->
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">
                    <i class="fas fa-bell me-2"></i>
                    Notifications
                </h2>
                <p class="text-muted">Stay updated with your latest activities</p>
            </div>

            <!-- Notifications Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="notification-item p-4 border-bottom {{ $notification->status === 'unread' ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex align-items-start">
                                    <!-- Notification Icon based on type -->
                                    <div class="notification-icon me-3">
                                        @switch($notification->type)
                                            @case('loan_approval')
                                                <i class="fas fa-check-circle text-success fa-lg"></i>
                                                @break
                                            @case('withdrawal')
                                                <i class="fas fa-money-bill-wave text-primary fa-lg"></i>
                                                @break
                                            @case('join_request')
                                                <i class="fas fa-user-plus text-info fa-lg"></i>
                                                @break
                                            @case('payment_reminder')
                                                <i class="fas fa-clock text-warning fa-lg"></i>
                                                @break
                                            @case('group_loan_request')
                                                <i class="fas fa-hand-holding-usd text-success fa-lg"></i>
                                                @break
                                            @case('leave_request')
                                                <i class="fas fa-sign-out-alt text-danger fa-lg"></i>
                                                @break
                                            @case('group_withdraw_request')
                                                <i class="fas fa-money-bill-transfer text-primary fa-lg"></i>
                                                @break
                                            @case('admin_promotion')
                                                <i class="fas fa-user-shield text-info fa-lg"></i>
                                                @break
                                            @case('close_savings')
                                                <i class="fas fa-door-closed text-danger fa-lg"></i>
                                                @break
                                            @default
                                                <i class="fas fa-bell text-secondary fa-lg"></i>
                                        @endswitch
                                    </div>
                                    
                                    <div class="notification-content">
                                        <h6 class="mb-1 fw-semibold">{{ $notification->title }}</h6>
                                        <p class="mb-1 text-muted">{{ $notification->message }}</p>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                                
                                @if($notification->status === 'unread')
                                    <span class="badge bg-primary rounded-pill">New</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="empty-state mb-3">
                                <i class="far fa-bell fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted mb-2">No notifications yet</h5>
                            <p class="text-muted mb-0">We'll notify you when something important happens</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    transition: all 0.3s ease;
    position: relative;
}

.notification-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.notification-item:last-child {
    border-bottom: none !important;
}

.notification-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.05);
}

.notification-content {
    flex: 1;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    font-weight: 500;
}

.empty-state {
    opacity: 0.7;
}

/* Custom scrollbar for notifications */
.card-body {
    max-height: 70vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #dee2e6 #ffffff;
}

.card-body::-webkit-scrollbar {
    width: 6px;
}

.card-body::-webkit-scrollbar-track {
    background: #ffffff;
}

.card-body::-webkit-scrollbar-thumb {
    background-color: #dee2e6;
    border-radius: 3px;
}

/* Animation for new notifications */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-item {
    animation: fadeIn 0.3s ease-out;
}
</style>
@endsection 