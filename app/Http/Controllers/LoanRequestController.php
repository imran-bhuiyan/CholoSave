<?php

namespace App\Http\Controllers;

use App\Models\LoanRequest;
use App\Models\MyGroup;
use App\Models\Poll;
use App\Models\Notification;
use App\Models\GroupMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Group;

class LoanRequestController extends Controller
{
    public function create($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Verify user is a member of the group
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        // Check for pending leave request
        $hasLeaveRequest = GroupMembership::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->where('leave_request', 1)
            ->exists();

        if ($hasLeaveRequest) {
            return redirect()->route('groups.member.dashboard', $groupId)
                ->with('error', 'Cannot request loan while having a pending leave request.');
        }

        // Calculate group current balance
        $totalGroupSavings = \App\Models\Savings::where('group_id', $groupId)->sum('amount');
        $totalApprovedWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');
        $totalApprovedLoans = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');
        $totalPendingInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'pending')
            ->sum('amount');
        $completedInvestmentReturns = 0;
        $completedInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'completed')
            ->with('returns')
            ->get();
        foreach ($completedInvestments as $investment) {
            $totalReturns = $investment->returns->sum('amount');
            $profitLoss = $totalReturns - $investment->amount;
            $completedInvestmentReturns += $profitLoss;
        }

        $groupCurrentBalance = ($totalGroupSavings + $completedInvestmentReturns) - $totalApprovedWithdrawals - $totalApprovedLoans - $totalPendingInvestments;

        // Determine which view to use based on the route
        $view = request()->route()->getName() === 'admin.loan.request.create' 
            ? 'groups.admin.loan_request' 
            : 'groups.member.loan_request';

        return view($view, compact('group', 'groupCurrentBalance', 'totalGroupSavings', 'totalApprovedWithdrawals', 'totalApprovedLoans', 'totalPendingInvestments', 'completedInvestmentReturns'));
    }

    public function store(Request $request, $groupId)
    {
        $group = MyGroup::findOrFail($groupId);

        // Check if user has any outstanding loans
        $existingLoan = LoanRequest::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingLoan) {
            $redirectRoute = request()->route()->getName() === 'admin.loan.request.store' 
                ? 'admin.loan.request.create' 
                : 'member.loan.request.create';
            
            return redirect()->route($redirectRoute, $groupId)
                ->with('error', 'You already have a pending or active loan request.');
        }

        // Check for pending leave request
        $hasLeaveRequest = GroupMembership::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->where('leave_request', 1)
            ->exists();

        if ($hasLeaveRequest) {
            return redirect()->back()
                ->with('error', 'Cannot request loan while having a pending leave request.');
        }

        // Check if group has sufficient current balance to fulfill the loan
        $totalGroupSavings = \App\Models\Savings::where('group_id', $groupId)->sum('amount');
        $totalApprovedWithdrawals = \App\Models\Withdrawal::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');
        $totalApprovedLoans = \App\Models\LoanRequest::where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');
        $totalPendingInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'pending')
            ->sum('amount');
        $completedInvestmentReturns = 0;
        $completedInvestments = \App\Models\Investment::where('group_id', $groupId)
            ->where('status', 'completed')
            ->with('returns')
            ->get();
        foreach ($completedInvestments as $investment) {
            $totalReturns = $investment->returns->sum('amount');
            $profitLoss = $totalReturns - $investment->amount;
            $completedInvestmentReturns += $profitLoss;
        }

        $groupCurrentBalance = ($totalGroupSavings + $completedInvestmentReturns) - $totalApprovedWithdrawals - $totalApprovedLoans - $totalPendingInvestments;

        if ($groupCurrentBalance < $request->amount) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Insufficient group funds. Group current balance is ৳' . number_format($groupCurrentBalance, 2) . ' which is less than the requested loan amount.']);
        }

        // Validate request
        $request->validate([
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:' . $group->emergency_fund,
                function ($attribute, $value, $fail) use ($group) {
                    if ($value > $group->emergency_fund) {
                        $fail('Loan amount cannot exceed the available emergency fund.');
                    }
                },
            ],
            'reason' => 'required|string|min:10',
            'return_date' => 'required|date|after:today',
            'terms' => 'required|accepted'
        ]);

        // Verify user is a member of the group
        $membership = GroupMembership::where('group_id', $groupId)
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Create loan request
            $loanRequest = LoanRequest::create([
                'group_id' => $groupId,
                'user_id' => Auth::id(),
                'amount' => $request->amount,
                'reason' => $request->reason,
                'return_time' => $request->return_date,
                'status' => 'pending'
            ]);

            // Create poll for loan approval
            $userName = Auth::user()->name;
            $pollQuestion = "$userName has requested a loan of BDT {$request->amount}. Do you approve?";
            
            Poll::create([
                'group_id' => $groupId,
                'poll_question' => $pollQuestion
            ]);

            DB::commit();

            // Determine which route to redirect to based on the current route
            $redirectRoute = request()->route()->getName() === 'admin.loan.request.store' 
                ? 'admin.loan.request.create' 
                : 'member.loan.request.create';

            return redirect()->route($redirectRoute, $groupId)
                ->with('success', 'Loan request submitted successfully. Waiting for admin approval.')
                ->with('just_submitted', true);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error processing loan request: ' . $e->getMessage());
        }
    }

    public function adminLoanHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $loans = LoanRequest::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $paymentMethods = [];
        if (!empty($group->bKash)) $paymentMethods[] = 'bkash';
        if (!empty($group->Rocket)) $paymentMethods[] = 'Rocket';
        if (!empty($group->Nagad)) $paymentMethods[] = 'Nagad';

        return view('groups.admin.loan_history', compact('group', 'loans', 'paymentMethods'));
    }
} 