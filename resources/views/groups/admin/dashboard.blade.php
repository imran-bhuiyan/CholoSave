@extends('layouts.group_admin')

@section('title', $group->group_name . ' - Admin Dashboard')

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

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring-circle {
        transition: stroke-dashoffset 0.35s;
        transform-origin: 50% 50%;
    }

    .metric-card {
        transition: all 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .health-indicator {
        transition: all 0.3s ease;
    }

    .health-indicator:hover {
        transform: scale(1.02);
    }
</style>
@endpush

@section('content')
<div class="flex-1 overflow-hidden">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold custom-font text-gray-900">
                    <i class="fas fa-chart-line mr-3 text-blue-600"></i>
                    {{ $group->group_name }} - Admin Dashboard
                </h1>
                <p class="text-gray-600 mt-1">Welcome back! Here's your group overview.</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Last updated</p>
                <p class="text-sm font-medium text-gray-900">{{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6 overflow-auto h-[calc(100vh-8rem)]">
        <div class="max-w-7xl mx-auto animate-fade-in">
            
            <!-- Top Section - Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <!-- Total Group Savings -->
                <div class="metric-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Group Savings</p>
                            <p class="text-2xl font-bold text-gray-900">৳{{ number_format($totalSavings, 2) }}</p>
                            <div class="mt-2">
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $group->goal_amount > 0 ? min(100, ($totalSavings / $group->goal_amount) * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $group->goal_amount > 0 ? number_format(($totalSavings / $group->goal_amount) * 100, 1) : '0.0' }}%</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Goal: ৳{{ number_format($group->goal_amount, 2) }}</p>
                            </div>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <i class="fas fa-piggy-bank text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Active Members -->
                <div class="metric-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Members</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $activeMembers }}/{{ $group->members }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $group->members > 0 ? number_format(($activeMembers / $group->members) * 100, 1) : '0.0' }}% participation</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="metric-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Requests</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $pendingRequests }}</p>
                            <div class="mt-2 space-y-1">
                                <p class="text-xs text-gray-500">{{ $pendingLoans }} loans • {{ $pendingWithdrawals }} withdrawals</p>
                                <p class="text-xs text-gray-500">{{ $pendingJoinRequests }} join requests</p>
                            </div>
                        </div>
                        <div class="p-3 bg-orange-100 rounded-lg">
                            <i class="fas fa-clock text-orange-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Emergency Fund -->
                <div class="metric-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Emergency Fund</p>
                            <p class="text-2xl font-bold text-gray-900">৳{{ number_format($group->emergency_fund, 2) }}</p>
                            <div class="mt-2">
                                @php
                                    $emergencyFundPercentage = $totalSavings > 0 ? ($group->emergency_fund / $totalSavings) * 100 : 0;
                                @endphp
                                <div class="flex items-center">
                                    <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $totalSavings > 0 ? min(100, $emergencyFundPercentage) : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $totalSavings > 0 ? number_format($emergencyFundPercentage, 1) : '0.0' }}%</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">of total savings</p>
                            </div>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <i class="fas fa-shield-alt text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Current Balance -->
                <div class="metric-card bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Current Balance</p>
                            <p class="text-2xl font-bold text-gray-900">৳{{ number_format($currentBalance, 2) }}</p>
                            <div class="mt-2 space-y-1">
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Investment P/L:</span>
                                    <span class="font-medium {{ $completedInvestmentReturns >= 0 ? 'text-green-600' : 'text-red-600' }}">৳{{ number_format($completedInvestmentReturns, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Pending Investments:</span>
                                    <span class="font-medium text-yellow-600">৳{{ number_format($totalPendingInvestments, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Total Loans Due:</span>
                                    <span class="font-medium text-red-600">৳{{ number_format($totalLoanDue, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">Total Withdrawals:</span>
                                    <span class="font-medium text-orange-600">৳{{ number_format($totalApprovedWithdrawals, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 bg-indigo-100 rounded-lg">
                            <i class="fas fa-wallet text-indigo-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Section - Charts & Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Payment Trends Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Payment Trends</h3>
                        <select class="text-sm border border-gray-300 rounded-md px-2 py-1">
                            <option>Last 6 Months</option>
                            <option>Last 3 Months</option>
                            <option>Last Year</option>
                        </select>
                    </div>
                    <div class="h-64 flex items-end justify-between space-x-2">
                        @foreach($paymentTrends as $month => $amount)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-blue-100 rounded-t" style="height: {{ $maxPayment > 0 ? ($amount / $maxPayment) * 200 : 0 }}px">
                                <div class="w-full bg-blue-500 rounded-t" style="height: 100%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">{{ $month }}</p>
                            <p class="text-xs font-medium text-gray-900">৳{{ number_format($amount, 0) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Goal Progress Meter -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Goal Progress</h3>
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            <svg class="progress-ring w-32 h-32" viewBox="0 0 120 120">
                                <circle class="progress-ring-circle" stroke="#e5e7eb" stroke-width="12" fill="transparent" r="48" cx="60" cy="60"/>
                                <circle class="progress-ring-circle" stroke="#10b981" stroke-width="12" fill="transparent" r="48" cx="60" cy="60" 
                                        stroke-dasharray="{{ 2 * pi() * 48 }}" 
                                        stroke-dashoffset="{{ $group->goal_amount > 0 ? (2 * pi() * 48 * (1 - min(1, $totalSavings / $group->goal_amount))) : (2 * pi() * 48) }}"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <span class="text-3xl font-bold text-gray-900">{{ $group->goal_amount > 0 ? number_format(($totalSavings / $group->goal_amount) * 100, 0) : '0' }}%</span>
                                    <p class="text-sm text-gray-600">Complete</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Current Savings</span>
                            <span class="text-lg font-bold text-gray-900">৳{{ number_format($totalSavings, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Target Goal</span>
                            <span class="text-lg font-bold text-gray-900">৳{{ number_format($group->goal_amount, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Remaining</span>
                            <span class="text-lg font-bold text-green-600">৳{{ $group->goal_amount > 0 ? number_format($group->goal_amount - $totalSavings, 2) : '0.00' }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Progress Status:</span>
                            @if($group->goal_amount > 0 && ($totalSavings / $group->goal_amount) * 100 >= 100)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Goal Achieved!
                                </span>
                            @elseif($group->goal_amount > 0 && ($totalSavings / $group->goal_amount) * 100 >= 75)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-rocket mr-1"></i>
                                    On Track
                                </span>
                            @elseif($group->goal_amount > 0 && ($totalSavings / $group->goal_amount) * 100 >= 50)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Halfway There
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Needs Attention
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Poll Section - Active Polls -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-poll mr-2 text-blue-600"></i>
                        Active Polls
                    </h3>
                    <a href="{{ route('admin.poll.list', $group->group_id) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View All Polls <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                @if($activePolls->count() > 0)
                    <div class="space-y-4">
                        @foreach($activePolls as $poll)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="text-sm font-medium text-gray-900 flex-1 mr-4">
                                        {{ $poll->poll_question }}
                                    </h4>
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        {{ $poll->total_votes }} votes
                                    </span>
                                </div>

                                <!-- Vote Statistics -->
                                <div class="mb-4">
                                    <div class="flex items-center mb-2">
                                        <span class="text-xs font-medium text-green-600 w-8">Yes:</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 mx-2">
                                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                                                 style="width: {{ $poll->yes_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 w-12">{{ $poll->yes_percentage }}%</span>
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-xs font-medium text-red-600 w-8">No:</span>
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 mx-2">
                                            <div class="bg-red-500 h-2 rounded-full transition-all duration-300" 
                                                 style="width: {{ $poll->no_percentage }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 w-12">{{ $poll->no_percentage }}%</span>
                                    </div>
                                </div>

                                <!-- Voting Buttons -->
                                <div class="flex items-center justify-between">
                                    <div class="flex space-x-2">
                                        <button class="vote-btn px-4 py-2 text-sm font-medium rounded-lg border transition-colors duration-200 {{ $poll->user_vote === 'yes' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-white border-gray-300 text-gray-700 hover:bg-green-50 hover:border-green-400' }}"
                                                data-poll-id="{{ $poll->poll_id }}"
                                                data-vote="yes">
                                            <i class="fas fa-thumbs-up mr-1"></i>
                                            Vote Yes
                                        </button>
                                        <button class="vote-btn px-4 py-2 text-sm font-medium rounded-lg border transition-colors duration-200 {{ $poll->user_vote === 'no' ? 'bg-red-100 border-red-500 text-red-700' : 'bg-white border-gray-300 text-gray-700 hover:bg-red-50 hover:border-red-400' }}"
                                                data-poll-id="{{ $poll->poll_id }}"
                                                data-vote="no">
                                            <i class="fas fa-thumbs-down mr-1"></i>
                                            Vote No
                                        </button>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        Created {{ $poll->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-poll text-4xl"></i>
                        </div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No Active Polls</h4>
                        <p class="text-gray-600 mb-4">There are currently no active polls for this group.</p>
                        <a href="{{ route('admin.poll.create', $group->group_id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Create New Poll
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </main>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Add any interactive JavaScript here
    document.addEventListener('DOMContentLoaded', function() {
        // Animate progress rings
        const progressRings = document.querySelectorAll('.progress-ring-circle');
        progressRings.forEach(ring => {
            const radius = ring.r.baseVal.value;
            const circumference = radius * 2 * Math.PI;
            ring.style.strokeDasharray = `${circumference} ${circumference}`;
        });

        // Poll voting functionality
        document.querySelectorAll('.vote-btn').forEach(button => {
            button.addEventListener('click', function() {
                const pollId = this.dataset.pollId;
                const voteOption = this.dataset.vote;
                
                // Show loading state
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Voting...';

                // Send vote request
                fetch(`/admin/poll/vote/${pollId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        vote_option: voteOption
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Vote Recorded!',
                            text: 'Your vote has been recorded successfully.',
                            confirmButtonColor: '#2563eb',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Reload the page to show updated vote statistics
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to record vote');
                    }
                })
                .catch(error => {
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = voteOption === 'yes' ? 
                        '<i class="fas fa-thumbs-up mr-1"></i>Vote Yes' : 
                        '<i class="fas fa-thumbs-down mr-1"></i>Vote No';

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.message || 'Failed to record vote. Please try again.',
                        confirmButtonColor: '#2563eb'
                    });
                });
            });
        });
    });
</script>
@endpush

@endsection
