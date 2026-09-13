<?php

namespace App\Http\Controllers;

use App\Models\MyGroup;
use App\Models\GroupMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Savings;
use App\Models\Investment;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LoanRequest;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function create()
    {
        return view('groups.create');
    }

    public function store(Request $request)
    {
        // Validate all data
        $validated = $request->validate([
            'group_name' => 'required|string|max:255',
            'description' => 'required|string',
            'members' => 'required|integer|min:2',
            'dps_type' => 'required|in:weekly,monthly',
            'time_period' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date|after:today',
            'bKash' => 'nullable|string|max:20',
            'Rocket' => 'nullable|string|max:20',
            'Nagad' => 'nullable|string|max:20',
            'goal_amount' => 'required|numeric|min:0',
            'emergency_fund' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Create the group
            $group = MyGroup::create([
                'group_name' => $validated['group_name'],
                'description' => $validated['description'],
                'members' => $validated['members'],
                'group_admin_id' => Auth::id(),
                'dps_type' => $validated['dps_type'],
                'time_period' => $validated['time_period'],
                'amount' => $validated['amount'],
                'start_date' => $validated['start_date'],
                'bKash' => $validated['bKash'],
                'Rocket' => $validated['Rocket'],
                'Nagad' => $validated['Nagad'],
                'goal_amount' => $validated['goal_amount'],
                'emergency_fund' => $validated['emergency_fund']
            ]);

            // Create the admin membership
            GroupMembership::create([
                'group_id' => $group->group_id,
                'user_id' => Auth::id(),
                'status' => 'approved',
                'is_admin' => true,
                'leave_request' => 'no',
                'join_date' => now(),
                'time_period_remaining' => $validated['time_period']
            ]);

            // Create leaderboard entry with 20 points
            \App\Models\Leaderboard::create([
                'group_id' => $group->group_id,
                'points' => 20.00
            ]);

            DB::commit();

            return redirect()->route('groups.admin.dashboard', $group->group_id)
                ->with('success', 'Group created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating group: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function adminDashboard($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Calculate total savings
        $totalSavings = \App\Models\Savings::where('group_id', $groupId)->sum('amount');

        // Get active members count
        $activeMembers = \App\Models\GroupMembership::where('group_id', $groupId)
            ->where('status', 'approved')
            ->count();

        // Get pending requests
        $pendingLoans = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('status', 'pending')
            ->count();

        $pendingWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)
            ->where('status', 'pending')
            ->count();

        $pendingJoinRequests = \App\Models\GroupMembership::where('group_id', $groupId)
            ->where('status', 'pending')
            ->count();

        $pendingRequests = $pendingLoans + $pendingWithdrawals + $pendingJoinRequests;

        // Generate payment trends for last 6 months
        $paymentTrends = [];
        $maxPayment = 0;
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $amount = \App\Models\TransactionInfo::where('group_id', $groupId)
                ->whereMonth('payment_time', $month->month)
                ->whereYear('payment_time', $month->year)
                ->sum('amount');
            $paymentTrends[$month->format('M')] = $amount;
            $maxPayment = max($maxPayment, $amount);
        }

        // Calculate time remaining
        $timeRemaining = $membership->time_period_remaining;
        $timeRemainingText = $timeRemaining > 1 ? 'months' : 'month';

        // Calculate average monthly collection
        $averageMonthlyCollection = \App\Models\TransactionInfo::where('group_id', $groupId)
            ->where('payment_time', '>=', now()->subMonths(6))
            ->sum('amount') / 6;

        // Calculate Current Balance: (Total Savings + Completed Investment Returns) - Total Loan Due - Total Pending Investments - Total Approved Withdrawals
        // Get completed investment returns and calculate profit/loss
        $completedInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'completed')
            ->with('returns')
            ->get();

        $completedInvestmentReturns = 0;
        foreach ($completedInvestments as $investment) {
            $totalReturns = $investment->returns->sum('amount');
            $profitLoss = $totalReturns - $investment->amount; // Positive = profit, negative = loss
            $completedInvestmentReturns += $profitLoss;
        }

        // Get total loan due (all approved loans in the group)
        $totalLoanDue = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        // Get total pending investments
        $totalPendingInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'pending')
            ->sum('amount');

        // Get total approved withdrawals from the group
        $totalApprovedWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        // Calculate current balance
        $currentBalance = ($totalSavings + $completedInvestmentReturns) - $totalLoanDue - $totalPendingInvestments - $totalApprovedWithdrawals;

        // Get active polls with vote statistics
        $activePolls = \App\Models\Poll::where('group_id', $groupId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($poll) {
                $totalVotes = $poll->votes()->count();
                $yesVotes = $poll->votes()->where('vote_option', 'yes')->count();
                $noVotes = $poll->votes()->where('vote_option', 'no')->count();
                
                $poll->yes_percentage = $totalVotes > 0 ? round(($yesVotes / $totalVotes) * 100) : 0;
                $poll->no_percentage = $totalVotes > 0 ? round(($noVotes / $totalVotes) * 100) : 0;
                $poll->total_votes = $totalVotes;
                $poll->yes_votes = $yesVotes;
                $poll->no_votes = $noVotes;
                
                // Check if current user has voted
                $poll->user_vote = $poll->votes()->where('user_id', auth()->id())->value('vote_option');
                
                return $poll;
            });

        return view('groups.admin.dashboard', compact(
            'group',
            'membership',
            'totalSavings',
            'activeMembers',
            'pendingRequests',
            'pendingLoans',
            'pendingWithdrawals',
            'pendingJoinRequests',
            'paymentTrends',
            'maxPayment',
            'timeRemaining',
            'timeRemainingText',
            'averageMonthlyCollection',
            'currentBalance',
            'completedInvestmentReturns',
            'totalLoanDue',
            'totalPendingInvestments',
            'totalApprovedWithdrawals',
            'activePolls'
        ));
    }

    public function memberDashboard($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', false)
            ->firstOrFail();

        // Calculate personal savings progress
        $personalSavings = \App\Models\Savings::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->sum('amount');

        // Calculate total group savings
        $totalGroupSavings = \App\Models\Savings::where('group_id', $groupId)->sum('amount');

        // Calculate withdraw amount (approved withdrawals)
        $withdrawAmount = \App\Models\Withdrawal::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->sum('amount');

        // Calculate actual available savings (total savings - withdraw amount)
        $availableSavings = $personalSavings - $withdrawAmount;

        // Calculate installments completed
        $installmentsCompleted = \App\Models\TransactionInfo::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->count();

        // Calculate time remaining
        $timeRemaining = $membership->time_period_remaining;
        $timeRemainingText = $timeRemaining > 1 ? 'months' : 'month';

        // Calculate loan due amount
        $loanDue = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->sum('amount');

        // Calculate group goal progress
        $groupGoalProgress = $group->goal_amount > 0 ? ($totalGroupSavings / $group->goal_amount) * 100 : 0;

        // Calculate Current Balance: (Total Savings + Completed Investment Returns) - Total Loan Due - Total Pending Investments
        // Get completed investment returns and calculate profit/loss
        $completedInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'completed')
            ->with('returns')
            ->get();

        $completedInvestmentReturns = 0;
        foreach ($completedInvestments as $investment) {
            $totalReturns = $investment->returns->sum('amount');
            $profitLoss = $totalReturns - $investment->amount; // Positive = profit, negative = loss
            $completedInvestmentReturns += $profitLoss;
        }

        // Get total loan due (all approved loans in the group)
        $totalLoanDue = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        // Get total pending investments
        $totalPendingInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'pending')
            ->sum('amount');

        // Get total approved withdrawals from the group
        $totalApprovedWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        // Calculate current balance
        $currentBalance = ($totalGroupSavings + $completedInvestmentReturns) - $totalLoanDue - $totalPendingInvestments - $totalApprovedWithdrawals;

        // Get payment history for last 6 months
        $paymentHistory = [];
        $maxPayment = 0;
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $amount = \App\Models\TransactionInfo::where('group_id', $groupId)
                ->where('user_id', auth()->id())
                ->whereMonth('payment_time', $month->month)
                ->whereYear('payment_time', $month->year)
                ->sum('amount');
            $paymentHistory[$month->format('M')] = $amount;
            $maxPayment = max($maxPayment, $amount);
        }

        // Get next payment due date (simplified calculation)
        $lastPayment = \App\Models\TransactionInfo::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->orderBy('payment_time', 'desc')
            ->first();
        
        $nextPaymentDue = $lastPayment ? 
            $lastPayment->payment_time->addMonth() : 
            now()->addMonth();

        // Get active polls with vote statistics
        $activePolls = \App\Models\Poll::where('group_id', $groupId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($poll) {
                $totalVotes = $poll->votes()->count();
                $yesVotes = $poll->votes()->where('vote_option', 'yes')->count();
                $noVotes = $poll->votes()->where('vote_option', 'no')->count();
                
                $poll->yes_percentage = $totalVotes > 0 ? round(($yesVotes / $totalVotes) * 100) : 0;
                $poll->no_percentage = $totalVotes > 0 ? round(($noVotes / $totalVotes) * 100) : 0;
                $poll->total_votes = $totalVotes;
                $poll->yes_votes = $yesVotes;
                $poll->no_votes = $noVotes;
                
                // Check if current user has voted
                $poll->user_vote = $poll->votes()->where('user_id', auth()->id())->value('vote_option');
                
                return $poll;
            });

        return view('groups.member.dashboard', compact(
            'group',
            'membership',
            'personalSavings',
            'totalGroupSavings',
            'withdrawAmount',
            'availableSavings',
            'installmentsCompleted',
            'timeRemaining',
            'timeRemainingText',
            'loanDue',
            'groupGoalProgress',
            'currentBalance',
            'completedInvestmentReturns',
            'totalLoanDue',
            'totalPendingInvestments',
            'totalApprovedWithdrawals',
            'paymentHistory',
            'maxPayment',
            'nextPaymentDue',
            'activePolls'
        ));
    }

    public function show($groupId)
    {
        $group = \App\Models\MyGroup::findOrFail($groupId);
        return view('groups.show', compact('group'));
    }

    public function myGroups()
    {
        $userId = auth()->id();
        $memberships = \App\Models\GroupMembership::where('user_id', $userId)
            ->where('status', 'approved')
            ->with('group')
            ->get();

        return view('groups.my_groups', compact('memberships'));
    }

    public function joinGroups()
    {
        $userId = auth()->id();
        
        // Get all groups that the user hasn't joined or has pending requests for
        $groups = \App\Models\MyGroup::whereDoesntHave('memberships', function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'approved');
        })
        ->with(['memberships' => function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('status', 'pending');
        }])
        ->get();

        // Add membership status to each group
        $groups->each(function($group) {
            $group->membership_status = $group->memberships->isNotEmpty() ? 'pending' : 'not_joined';
        });

        \Log::info('Join groups page loaded', [
            'userId' => $userId,
            'groupsCount' => $groups->count(),
            'groups' => $groups->toArray()
        ]);

        return view('groups.join_groups', compact('groups'));
    }

    public function joinGroup(Request $request, $groupId)
    {
        try {
            \Log::info('Join group request received', ['groupId' => $groupId, 'userId' => auth()->id()]);
            
            $userId = auth()->id();
            $group = \App\Models\MyGroup::findOrFail($groupId);

            \Log::info('Group found', ['group' => $group->toArray()]);

            // Check if group is full
            $currentMembers = \App\Models\GroupMembership::where('group_id', $groupId)
                ->where('status', 'approved')
                ->count();

            if ($currentMembers >= $group->members) {
                return response()->json([
                    'success' => false,
                    'message' => 'This group has reached its maximum member limit'
                ]);
            }

            // Check if user already has a pending request
            $existingMembership = \App\Models\GroupMembership::where('group_id', $groupId)
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->first();

            if ($existingMembership) {
                \Log::info('Existing pending membership found', ['membership' => $existingMembership->toArray()]);
                return response()->json([
                    'success' => false,
                    'message' => 'You already have a pending request for this group'
                ]);
            }

            // Create new membership request with all required fields
            $membership = \App\Models\GroupMembership::create([
                'group_id' => $groupId,
                'user_id' => $userId,
                'status' => 'pending',
                'is_admin' => false,
                'leave_request' => 'no',
                'join_date' => null,
                'time_period_remaining' => $group->time_period,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Create a poll for join approval
            $userName = auth()->user()->name;
            $pollQuestion = "$userName wants to join your group. Do you approve?";
            \App\Models\Poll::create([
                'group_id' => $groupId,
                'poll_question' => $pollQuestion,
                'status' => 'active'
            ]);

            \Log::info('New membership created', [
                'membership' => $membership->toArray(),
                'group_time_period' => $group->time_period
            ]);

            if ($membership) {
                return response()->json([
                    'success' => true,
                    'message' => 'Join request sent successfully'
                ]);
            } else {
                \Log::error('Failed to create membership');
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create join request'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in joinGroup: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'groupId' => $groupId,
                'userId' => auth()->id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ], 500);
        }
    }

    public function enterGroup($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->firstOrFail();

        if ($membership->is_admin) {
            return redirect()->route('groups.admin.dashboard', $group->group_id);
        } else {
            return redirect()->route('groups.member.dashboard', $group->group_id);
        }
    }

    public function members($groupId)
    {
        $memberships = GroupMembership::with('user')
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->get();

        $currentUserId = auth()->id();

        $members = $memberships->map(function($membership) use ($groupId, $currentUserId) {
            $contribution = Savings::where('group_id', $groupId)
                ->where('user_id', $membership->user_id)
                ->sum('amount');

            // Calculate current balance for this member
            $totalSavings = Savings::where('group_id', $groupId)
                ->where('user_id', $membership->user_id)
                ->sum('amount');

            $totalApprovedWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)
                ->where('user_id', $membership->user_id)
                ->where('status', 'approved')
                ->sum('amount');

            $totalApprovedLoans = \App\Models\LoanRequest::where('group_id', $groupId)
                ->where('user_id', $membership->user_id)
                ->where('status', 'approved')
                ->sum('amount');

            $currentBalance = $totalSavings - $totalApprovedWithdrawals - $totalApprovedLoans;

            return [
                'user_id' => $membership->user_id,
                'name' => $membership->user->name,
                'join_date' => $membership->created_at,
                'contribution' => $contribution,
                'remaining_installment' => $membership->time_period_remaining,
                'current_balance' => $currentBalance,
                'is_current_user' => $membership->user_id == $currentUserId,
            ];
        });

        $group = \App\Models\MyGroup::findOrFail($groupId);

        return view('groups.member.members', compact('group', 'members'));
    }

    public function investmentDetails($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->firstOrFail();

        $investments = Investment::with('returns')
            ->where('group_id', $groupId)
            ->orderBy('investment_id', 'desc')
            ->get();

        return view('groups.member.investment-details', compact('group', 'membership', 'investments'));
    }

    public function exportInvestmentDetails($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('status', 'approved')
            ->firstOrFail();

        $investments = Investment::with('returns')
            ->where('group_id', $groupId)
            ->orderBy('investment_id', 'desc')
            ->get();

        $pdf = PDF::loadView('groups.member.investment-details-pdf', [
            'group' => $group,
            'investments' => $investments,
            'totalInvestment' => $investments->sum('amount'),
            'totalExpected' => $investments->sum('ex_profit'),
            'totalActual' => $investments->sum(function($investment) {
                return $investment->returns->sum('amount');
            })
        ]);

        return $pdf->download('investment-details-' . $group->group_name . '.pdf');
    }

    public function adminNotifications($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Fetch only unread notifications for the group
        $notifications = \App\Models\Notification::where('target_group_id', $groupId)
            ->where('status', 'unread')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.admin.group_notifications', compact('notifications', 'group'));
    }

    public function markNotificationAsRead($groupId, $notificationId)
    {
        $notification = \App\Models\Notification::where('notification_id', $notificationId)
            ->where('target_group_id', $groupId)
            ->firstOrFail();

        $notification->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }

    public function clearAllNotifications($groupId)
    {
        // Mark all notifications as read instead of deleting them
        \App\Models\Notification::where('target_group_id', $groupId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);
        
        return response()->json(['success' => true]);
    }

    public function adminMembers($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Get all members with their details
        $members = GroupMembership::where('group_id', $groupId)
            ->where('status', 'approved')
            ->with('user')
            ->get()
            ->map(function ($membership) use ($groupId) {
                // Calculate total contribution from savings
                $contribution = Savings::where('group_id', $groupId)
                    ->where('user_id', $membership->user_id)
                    ->sum('amount');

                // Calculate current balance for this member
                $totalSavings = Savings::where('group_id', $groupId)
                    ->where('user_id', $membership->user_id)
                    ->sum('amount');

                $totalApprovedWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)
                    ->where('user_id', $membership->user_id)
                    ->where('status', 'approved')
                    ->sum('amount');

                $totalApprovedLoans = \App\Models\LoanRequest::where('group_id', $groupId)
                    ->where('user_id', $membership->user_id)
                    ->where('status', 'approved')
                    ->sum('amount');

                $currentBalance = $totalSavings - $totalApprovedWithdrawals - $totalApprovedLoans;

                return [
                    'user_id' => $membership->user_id,
                    'name' => $membership->user->name,
                    'join_date' => $membership->created_at,
                    'is_admin' => $membership->is_admin,
                    'contribution' => $contribution,
                    'remaining_installment' => $membership->time_period_remaining,
                    'current_balance' => $currentBalance,
                ];
            });

        return view('groups.admin.members', compact('group', 'members'));
    }

    public function memberLoans($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Get all loan requests with user details
        $loans = LoanRequest::with('user')
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.admin.member_loans', compact('group', 'loans'));
    }

    public function approveLoan($groupId, $loanId)
    {
        try {
            $group = MyGroup::findOrFail($groupId);
            
            // Verify user is admin
            $membership = GroupMembership::where('group_id', $groupId)
                ->where('user_id', auth()->id())
                ->where('is_admin', true)
                ->firstOrFail();

            $loan = LoanRequest::findOrFail($loanId);

            // Check if loan is already processed
            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This loan request has already been processed.'
                ]);
            }

            // Check emergency fund balance
            $emergencyFund = $group->emergency_fund;
            if ($emergencyFund < $loan->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient emergency fund balance to approve this loan.'
                ]);
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Update loan status
                $loan->update([
                    'status' => 'approved',
                    'approve_date' => now()
                ]);

                // Deduct loan amount from emergency fund
                $group->update([
                    'emergency_fund' => $emergencyFund - $loan->amount
                ]);

                // Create single notification for both user and group
                Notification::create([
                    'target_user_id' => $loan->user_id,
                    'target_group_id' => $groupId,
                    'title' => 'Loan Request Approved',
                    'message' => "Loan request of ৳{$loan->amount} by {$loan->user->name} has been approved. Emergency fund reduced by ৳{$loan->amount}.",
                    'type' => 'loan_approval',
                    'status' => 'unread'
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Loan request has been approved successfully. Emergency fund has been reduced by ৳' . number_format($loan->amount, 2) . '.'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ]);
        }
    }

    public function declineLoan($groupId, $loanId)
    {
        try {
            $group = MyGroup::findOrFail($groupId);
            
            // Verify user is admin
            $membership = GroupMembership::where('group_id', $groupId)
                ->where('user_id', auth()->id())
                ->where('is_admin', true)
                ->firstOrFail();

            $loan = LoanRequest::findOrFail($loanId);

            // Check if loan is already processed
            if ($loan->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This loan request has already been processed.'
                ]);
            }

            // Update loan status
            $loan->update([
                'status' => 'declined'
            ]);

            // Create single notification for both user and group
            Notification::create([
                'target_user_id' => $loan->user_id,
                'target_group_id' => $groupId,
                'title' => 'Loan Request Declined',
                'message' => "Loan request of ৳{$loan->amount} by {$loan->user->name} has been declined.",
                'type' => 'loan_approval',
                'status' => 'unread'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Loan request has been declined successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ]);
        }
    }

    public function adminSettings($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        return view('groups.admin.settings', compact('group'));
    }

    public function updateSettings(Request $request, $groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Validate the request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'goal_amount' => 'required|numeric|min:0',
            'emergency_fund' => 'required|numeric|min:0',
            'bKash' => 'nullable|string|max:20',
            'Rocket' => 'nullable|string|max:20',
            'Nagad' => 'nullable|string|max:20',
        ]);

        // Update group settings
        $group->update($validated);

        return redirect()->back()->with('success', 'Group settings updated successfully.');
    }

    public function closeSavings($groupId)
    {
        $user = auth()->user();
        $group = MyGroup::find($groupId);
        if (!$group) {
            return response()->json([
                'success' => false,
                'message' => 'Group not found.'
            ]);
        }
        // Check admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->where('is_admin', true)
            ->first();
        if (!$membership) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only group admin can close savings.'
            ]);
        }
        // Check all withdrawals = all savings
        $totalSavings = \App\Models\Savings::where('group_id', $groupId)->sum('amount');
        $totalWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)->where('status', 'approved')->sum('amount');
        if (abs($totalSavings - $totalWithdrawals) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'All savings have not been withdrawn by members.'
            ]);
        }
        // Check no loans with status = approved
        $activeLoans = \App\Models\LoanRequest::where('group_id', $groupId)->where('status', 'approved')->count();
        if ($activeLoans > 0) {
            return response()->json([
                'success' => false,
                'message' => 'There are still active (approved) loans. All loans must be settled before closing.'
            ]);
        }
        // All checks passed, delete all group-related data in a transaction
        try {
            DB::beginTransaction();
            // Delete poll votes
            $pollIds = \App\Models\Poll::where('group_id', $groupId)->pluck('poll_id');
            \App\Models\PollVote::whereIn('poll_id', $pollIds)->delete();
            // Delete polls
            \App\Models\Poll::where('group_id', $groupId)->delete();
            // Delete investments and returns
            $investmentIds = \App\Models\Investment::where('group_id', $groupId)->pluck('investment_id');
            \App\Models\InvestmentReturn::whereIn('investment_id', $investmentIds)->delete();
            \App\Models\Investment::where('group_id', $groupId)->delete();
            // Delete savings
            \App\Models\Savings::where('group_id', $groupId)->delete();
            \App\Models\MySavings::where('group_id', $groupId)->delete();
            // Delete transactions
            \App\Models\TransactionInfo::where('group_id', $groupId)->delete();
            // Delete memberships
            \App\Models\GroupMembership::where('group_id', $groupId)->delete();
            // Delete withdrawals
            \App\Models\Withdrawal::where('group_id', $groupId)->delete();
            // Delete loan requests
            \App\Models\LoanRequest::where('group_id', $groupId)->delete();
            // Delete leaderboard
            \App\Models\Leaderboard::where('group_id', $groupId)->delete();
            // Delete notifications
            \App\Models\Notification::where('target_group_id', $groupId)->delete();
            // Delete messages
            \App\Models\Message::where('group_id', $groupId)->delete();
            // Delete payment otps
            \App\Models\PaymentOtp::where('group_id', $groupId)->delete();
            // Delete the group itself
            $group->delete();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Savings closed and all group data deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to close savings: ' . $e->getMessage()
            ]);
        }
    }

    public function joinRequests(MyGroup $group)
    {
        // Verify if user is admin
        if (!$group->isAdmin(auth()->id())) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }

        // Get pending join requests
        $pendingRequests = GroupMembership::with('user')
            ->where('group_id', $group->group_id)
            ->where('status', 'pending')
            ->get();

        return view('groups.admin.join_requests', compact('group', 'pendingRequests'));
    }

    public function approveJoinRequest(MyGroup $group, GroupMembership $request)
    {
        // Verify if user is admin
        if (!$group->isAdmin(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Update membership status
        $request->update([
            'status' => 'approved',
            'join_date' => now(),
            'time_period_remaining' => $group->time_period
        ]);

        // Create notification for the group (targeted_group_id set, targeted_user_id null)
        Notification::create([
            'target_group_id' => $group->group_id,
            'target_user_id' => null,
            'title' => 'New Member Joined',
            'message' => "{$request->user->name} has joined the group",
            'type' => 'join_request',
            'status' => 'unread'
        ]);

        // Create notification for the user (targeted_user_id set, targeted_group_id null)
        Notification::create([
            'target_user_id' => $request->user_id,
            'target_group_id' => null,
            'title' => 'Join Request Approved',
            'message' => "Your request to join {$group->name} has been approved!",
            'type' => 'join_request',
            'status' => 'unread'
        ]);

        // Add points to leaderboard
        $leaderboard = \App\Models\Leaderboard::firstOrCreate(
            ['group_id' => $group->group_id],
            ['points' => 0]
        );
        $leaderboard->increment('points', 5);

        return response()->json([
            'success' => true,
            'message' => 'Join request approved successfully'
        ]);
    }

    public function rejectJoinRequest(MyGroup $group, GroupMembership $request)
    {
        // Verify if user is admin
        if (!$group->isAdmin(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Update membership status
        $request->update([
            'status' => 'declined'
        ]);

        // Create notification for the user (targeted_user_id set, targeted_group_id null)
        Notification::create([
            'target_user_id' => $request->user_id,
            'target_group_id' => null,
            'title' => 'Join Request Declined',
            'message' => "Your request to join {$group->name} has been declined.",
            'type' => 'join_request',
            'status' => 'unread'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Join request declined successfully'
        ]);
    }

    // Admin Payment Methods
    public function createAdminInstallmentPayment($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $user = auth()->user();

        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Generate a transaction ID
        $transactionId = 'CHS' . Str::upper(Str::random(2)) . Str::lower(Str::random(2)) . rand(1000, 9999);

        return view('groups.admin.initiate_installment_payment', compact('group', 'user', 'transactionId'));
    }

    public function initiateAdminInstallmentPayment(Request $request, $groupId)
    {
        $user = auth()->user();
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        $amount = $group->amount;
        $selectedMethod = $request->input('payment_method');
        $transactionId = 'TRX' . time() . rand(1000, 9999);

        // Generate OTP
        $otp = rand(100000, 999999);
        $otpExpiry = now()->addMinutes(2);

        // Store OTP in database
        \App\Models\PaymentOtp::create([
            'user_id' => $user->id,
            'group_id' => $groupId,
            'otp' => $otp,
            'otp_expiry' => $otpExpiry,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'payment_method' => $selectedMethod
        ]);

        // Send OTP via email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\PaymentOtp($otp, $amount, $group->group_name));
        } catch (\Exception $e) {
            \Log::error('Failed to send admin payment OTP email: ' . $e->getMessage());
        }

        return redirect()->route('admin.installment.payment.verify-otp', [
            'groupId' => $groupId,
            'transactionId' => $transactionId
        ]);
    }

    public function showAdminInstallmentVerifyOtpForm($groupId, $transactionId)
    {
        $group = MyGroup::findOrFail($groupId);
        $user = auth()->user();
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();
        
        // Check if there's a valid OTP in the database
        $paymentOtp = \App\Models\PaymentOtp::where('user_id', $user->id)
            ->where('group_id', $groupId)
            ->where('transaction_id', $transactionId)
            ->where('otp_expiry', '>', now())
            ->first();

        if (!$paymentOtp) {
            return redirect()->route('admin.installment.payment.create', $groupId)
                ->with('error', 'OTP expired or not generated. Please try again.');
        }

        return view('groups.admin.verify_installment_otp', compact('groupId', 'transactionId', 'group'));
    }

    public function verifyAdminInstallmentOtp(Request $request, $groupId, $transactionId)
    {
        $user = auth()->user();
        $enteredOtp = $request->input('otp');

        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Get the OTP record from database
        $paymentOtp = \App\Models\PaymentOtp::where('user_id', $user->id)
            ->where('group_id', $groupId)
            ->where('transaction_id', $transactionId)
            ->where('otp_expiry', '>', now())
            ->first();

        if (!$paymentOtp || $paymentOtp->otp !== $enteredOtp) {
            return redirect()->back()->with('error', 'Invalid or expired OTP. Please try again.');
        }

        // Start database transaction
        DB::beginTransaction();
        try {
            // Create transaction record
            \App\Models\TransactionInfo::create([
                'user_id' => $user->id,
                'group_id' => $groupId,
                'amount' => $paymentOtp->amount,
                'transaction_id' => $transactionId,
                'payment_method' => $paymentOtp->payment_method,
                'payment_time' => now(),
            ]);

            // Create savings record
            \App\Models\Savings::create([
                'user_id' => $user->id,
                'group_id' => $groupId,
                'amount' => $paymentOtp->amount,
            ]);

            // Add 1% of payment amount as points to leaderboard
            $pointsToAdd = round($paymentOtp->amount * 0.01, 2);
            $leaderboard = \App\Models\Leaderboard::firstOrCreate(
                ['group_id' => $groupId],
                ['points' => 0]
            );
            $leaderboard->increment('points', $pointsToAdd);

            // Update group membership
            $membership->decrement('time_period_remaining');

            // Delete the used OTP
            $paymentOtp->delete();

            DB::commit();

            return redirect()->route('admin.installment.payment.success', [
                'groupId' => $groupId,
                'transactionId' => $transactionId
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin payment processing failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function showAdminInstallmentPaymentSuccess($groupId, $transactionId)
    {
        // Fetch the group
        $group = MyGroup::findOrFail($groupId);

        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Fetch the transaction details using the transactionId
        $transaction = \App\Models\TransactionInfo::where('transaction_id', $transactionId)
            ->where('user_id', auth()->id())
            ->where('group_id', $groupId)
            ->first();

        return view('groups.admin.payment_success', compact('groupId', 'group', 'transaction'));
    }

    public function adminPaymentHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        $transactions = \App\Models\TransactionInfo::where('user_id', auth()->id())
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.admin.payment_history', compact('group', 'transactions'));
    }

    public function adminMemberPayment($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Start with base query for all transactions in the group
        $query = \App\Models\TransactionInfo::with('user')
            ->where('group_id', $groupId);

        // Apply search filters
        if (request('transaction_search')) {
            $searchTerm = request('transaction_search');
            $query->where('transaction_id', 'LIKE', "%{$searchTerm}%");
        }

        if (request('payment_method_filter')) {
            $query->where('payment_method', request('payment_method_filter'));
        }

        // Apply date filters
        if (request('date_filter')) {
            $dateFilter = request('date_filter');
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('payment_time', today());
                    break;
                case 'week':
                    $query->whereBetween('payment_time', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('payment_time', now()->month)
                          ->whereYear('payment_time', now()->year);
                    break;
                case 'year':
                    $query->whereYear('payment_time', now()->year);
                    break;
            }
        }

        // Get paginated results
        $transactions = $query->orderBy('payment_time', 'desc')
                             ->paginate(15);

        // Calculate statistics
        $totalPayments = \App\Models\TransactionInfo::where('group_id', $groupId)->count();
        $totalAmount = \App\Models\TransactionInfo::where('group_id', $groupId)->sum('amount');
        $activeMembers = \App\Models\GroupMembership::where('group_id', $groupId)
            ->where('status', 'approved')
            ->count();
        $averagePayment = $totalPayments > 0 ? $totalAmount / $totalPayments : 0;

        return view('groups.admin.member_payment', compact(
            'group', 
            'transactions', 
            'totalPayments', 
            'totalAmount', 
            'activeMembers', 
            'averagePayment'
        ));
    }

    public function exportMemberPayment($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is admin
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->where('is_admin', true)
            ->firstOrFail();

        // Get all transactions for the group
        $transactions = \App\Models\TransactionInfo::with('user')
            ->where('group_id', $groupId)
            ->orderBy('payment_time', 'desc')
            ->get();

        // Generate CSV content
        $filename = "group_{$groupId}_payment_history_" . date('Y-m-d_H-i-s') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Serial',
                'Member Name',
                'Transaction ID',
                'Amount (BDT)',
                'Payment Method',
                'Payment Time',
                'Status'
            ]);

            // Add data rows
            foreach ($transactions as $index => $transaction) {
                fputcsv($file, [
                    $index + 1,
                    $transaction->user->name ?? 'N/A',
                    $transaction->transaction_id,
                    number_format($transaction->amount, 2),
                    $transaction->payment_method ?? 'N/A',
                    $transaction->payment_time ? $transaction->payment_time->format('M d, Y H:i') : 'Not Set',
                    'Completed'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function transferAdmin(Request $request, $groupId)
    {
        $request->validate([
            'new_admin_user_id' => 'required|exists:users,id',
        ]);

        $currentAdminId = auth()->id();
        $newAdminId = $request->input('new_admin_user_id');

        if ($currentAdminId == $newAdminId) {
            return response()->json(['error' => 'You are already the admin.'], 422);
        }

        DB::beginTransaction();
        try {
            // Update group admin
            $group = MyGroup::findOrFail($groupId);
            $group->group_admin_id = $newAdminId;
            $group->save();

            // Update memberships
            GroupMembership::where('group_id', $groupId)
                ->where('user_id', $currentAdminId)
                ->update(['is_admin' => false]);
            GroupMembership::where('group_id', $groupId)
                ->where('user_id', $newAdminId)
                ->update(['is_admin' => true]);

            // Notify new admin (user notification)
            Notification::create([
                'target_user_id' => $newAdminId,
                'target_group_id' => null,
                'title' => 'You are now the Group Admin',
                'message' => 'You have been made the admin of the group "' . $group->group_name . '".',
                'status' => 'unread',
                'type' => 'admin_promotion',
            ]);

            // Notify the group (group notification)
            Notification::create([
                'target_user_id' => null,
                'target_group_id' => $groupId,
                'title' => 'Group Admin Changed',
                'message' => 'The admin of the group "' . $group->group_name . '" has been changed.',
                'status' => 'unread',
                'type' => 'admin_promotion',
            ]);

            DB::commit();

            // Log out the current user
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            return response()->json(['success' => true, 'redirect' => route('login')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to transfer admin: ' . $e->getMessage()], 500);
        }
    }

    public function showAdminLeaveRequestForm($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        return view('groups.admin.leave_request', compact('group'));
    }

    public function adminLeaveRequest(Request $request, $groupId)
    {
        $userId = auth()->id();
        $group = MyGroup::findOrFail($groupId);

        // Check for outstanding loans
        $outstandingLoans = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->count();
        if ($outstandingLoans > 0) {
            return redirect()->back()->with('error', 'You cannot leave the group as you have outstanding loans.');
        }

        // Check if user is admin
        if ($group->group_admin_id == $userId) {
            return redirect()->back()->with('error', 'You are the admin. Please assign another admin before leaving the group.');
        }

        // (No success path for admin)
        return redirect()->back()->with('error', 'You cannot leave the group as an admin.');
    }

    public function showMemberLeaveRequestForm($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        return view('groups.member.leave_request', compact('group'));
    }

    public function memberLeaveRequest(Request $request, $groupId)
    {
        $userId = auth()->id();
        $group = MyGroup::findOrFail($groupId);

        // Check for outstanding loans
        $outstandingLoans = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->count();
        if ($outstandingLoans > 0) {
            return redirect()->back()->with('error', 'You cannot leave the group while you have outstanding loans. Please clear all loans before requesting to leave.');
        }

        // Check for remaining savings
        $totalSavings = \App\Models\Savings::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->sum('amount');
        $totalWithdrawn = \App\Models\Withdrawal::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('amount');
        $netBalance = $totalSavings - $totalWithdrawn;
        if ($netBalance > 0) {
            return redirect()->back()->with('error', 'First withdraw the money you saved before leaving the group.');
        }

        // Check for existing pending leave request
        $membership = \App\Models\GroupMembership::where('group_id', $groupId)
            ->where('user_id', $userId)
            ->first();
        if ($membership && $membership->leave_request === 'pending') {
            return redirect()->back()->with('error', 'You already have a pending leave request');
        }

        // Submit leave request
        if ($membership) {
            $membership->leave_request = 'pending';
            $membership->save();
        }
        return redirect()->back()->with('success', 'Leave request submitted successfully. Please wait for approval.');
    }

    public function memberLeaveRequests($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $leaveRequests = \App\Models\GroupMembership::where('group_id', $groupId)
            ->where('leave_request', 'pending')
            ->with('user')
            ->get()
            ->map(function ($membership) use ($groupId) {
                $totalContribution = \App\Models\Savings::where('group_id', $groupId)
                    ->where('user_id', $membership->user_id)
                    ->sum('amount');
                $totalWithdrawn = \App\Models\Withdrawal::where('group_id', $groupId)
                    ->where('user_id', $membership->user_id)
                    ->where('status', 'approved')
                    ->sum('amount');
                return [
                    'membership_id' => $membership->membership_id,
                    'name' => $membership->user->name,
                    'join_date' => $membership->join_date,
                    'installments_left' => $membership->time_period_remaining,
                    'total_contribution' => $totalContribution,
                    'total_withdrawn' => $totalWithdrawn,
                    'leave_request' => $membership->leave_request,
                ];
            });
        return view('groups.admin.member_leave_requests', compact('group', 'leaveRequests'));
    }

    public function approveMemberLeaveRequest($groupId, $membershipId)
    {
        DB::beginTransaction();
        try {
            $membership = \App\Models\GroupMembership::where('group_id', $groupId)
                ->where('membership_id', $membershipId)
                ->firstOrFail();
            $userId = $membership->user_id;
            $group = \App\Models\MyGroup::findOrFail($groupId);

            // Delete all financial records for this user in this group
            \App\Models\Savings::where('group_id', $groupId)->where('user_id', $userId)->delete();
            \App\Models\LoanRequest::where('group_id', $groupId)->where('user_id', $userId)->delete();
            \App\Models\Withdrawal::where('group_id', $groupId)->where('user_id', $userId)->delete();
            \App\Models\TransactionInfo::where('group_id', $groupId)->where('user_id', $userId)->delete();

            // Remove from group membership
            $membership->delete();

            // Deduct 10 points from leaderboard
            $leaderboard = \App\Models\Leaderboard::where('group_id', $groupId)->first();
            if ($leaderboard) {
                $leaderboard->points = max(0, $leaderboard->points - 10);
                $leaderboard->save();
            }

            // Notify the user
            \App\Models\Notification::create([
                'target_user_id' => $userId,
                'target_group_id' => null,
                'title' => 'Leave Request Approved',
                'message' => 'Your leave request from group "' . $group->group_name . '" has been approved. You have been removed from the group.',
                'status' => 'unread',
                'type' => 'leave_request',
            ]);

            // Notify remaining group members
            $memberIds = \App\Models\GroupMembership::where('group_id', $groupId)
                ->where('status', 'approved')
                ->pluck('user_id');
            foreach ($memberIds as $memberId) {
                \App\Models\Notification::create([
                    'target_user_id' => $memberId,
                    'target_group_id' => $groupId,
                    'title' => 'Member Left Group',
                    'message' => 'A member has left the group "' . $group->group_name . '".',
                    'status' => 'unread',
                    'type' => 'leave_request',
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Leave request approved, member removed, and records cleaned up.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process leave request: ' . $e->getMessage());
        }
    }

    public function rejectMemberLeaveRequest($groupId, $membershipId)
    {
        $membership = \App\Models\GroupMembership::where('group_id', $groupId)
            ->where('membership_id', $membershipId)
            ->firstOrFail();
        $membership->leave_request = 'no';
        $membership->save();
        // Optionally, send notification here
        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    public function leaderboardPage()
    {
        // Fetch all groups with their leaderboard points
        $groups = \App\Models\MyGroup::with('leaderboard')
            ->get()
            ->map(function ($group) {
                $group->points = $group->leaderboard ? $group->leaderboard->points : 0;
                return $group;
            })
            ->sortByDesc('points')
            ->values();

        // Points rules
        $rules = [
            ['action' => 'Create Group', 'points' => '+20'],
            ['action' => 'Join Group', 'points' => '+5'],
            ['action' => 'Leave Group', 'points' => '-10'],
            ['action' => 'Payment', 'points' => '+1% of payment amount'],
        ];

        return view('groups.leaderboard', compact('groups', 'rules'));
    }

    public function payLoan(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loan_request,id',
            'payment_method' => 'required|in:bkash,Rocket,Nagad',
            'repayment_amount' => 'required|numeric|min:1'
        ]);

        $loan = LoanRequest::findOrFail($request->loan_id);

        // Only allow payment if loan is approved and not already repaid
        if ($loan->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Loan is not approved or already repaid.']);
        }

        $loan->status = 'repaid';
        $loan->payment_method = $request->payment_method;
        $loan->transaction_id = (string) \Illuminate\Support\Str::uuid();
        $loan->payment_time = now();
        $loan->repayment_date = now();
        $loan->repayment_amount = $request->repayment_amount;
        $loan->save();

        // Add the paid amount back to the group's emergency fund
        $group = \App\Models\MyGroup::find($loan->group_id);
        if ($group) {
            $group->emergency_fund += $loan->repayment_amount;
            $group->save();
        }

        return response()->json(['success' => true]);
    }
} 