<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MyGroup;
use App\Models\GroupMembership;
use App\Models\Savings;
use App\Models\Withdrawal;
use App\Models\Notification;

class WithdrawalController extends Controller
{
    // Admin Methods
    public function adminCreate($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $userId = Auth::id();

        // Calculate available net balance for the admin
        $totalSavings = Savings::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->sum('amount');

        $totalApprovedWithdrawals = Withdrawal::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $totalApprovedLoans = \App\Models\LoanRequest::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $netAvailableBalance = $totalSavings - $totalApprovedWithdrawals - $totalApprovedLoans;

        return view('groups.admin.withdrawal_request', compact('group', 'netAvailableBalance', 'totalSavings', 'totalApprovedWithdrawals', 'totalApprovedLoans'));
    }

    public function adminStore(Request $request, $groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $userId = Auth::id();

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_number' => 'required|string',
            'payment_method' => 'required|in:Bkash,Nagad,Rocket',
        ]);

        // Check if user has sufficient savings (considering approved withdrawals and loans)
        $totalSavings = Savings::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->sum('amount');

        $totalApprovedWithdrawals = Withdrawal::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $totalApprovedLoans = \App\Models\LoanRequest::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $netAvailableBalance = $totalSavings - $totalApprovedWithdrawals - $totalApprovedLoans;

        if ($netAvailableBalance < $request->amount) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Insufficient available balance. Your net available balance is ৳' . number_format($netAvailableBalance, 2) . ' (Total Savings: ৳' . number_format($totalSavings, 2) . ' - Approved Withdrawals: ৳' . number_format($totalApprovedWithdrawals, 2) . ' - Approved Loans: ৳' . number_format($totalApprovedLoans, 2) . ')']);
        }

        // Create withdrawal request
        Withdrawal::create([
            'user_id' => $userId,
            'group_id' => $groupId,
            'amount' => $request->amount,
            'payment_number' => $request->payment_number,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->back()
            ->with('success', 'Withdrawal request submitted successfully.')
            ->with('just_submitted', true);
    }

    /**
     * Display the admin's personal withdrawal history for a specific group.
     */
    public function adminPersonalWithdrawalHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Get only the admin's withdrawals for the group
        $withdrawals = Withdrawal::where('group_id', $groupId)
            ->where('user_id', auth()->id())  // Filter by current user (admin)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.admin.withdrawal_history', compact('group', 'withdrawals'));
    }

    /**
     * Display all member withdrawals for admin to manage.
     */
    public function adminWithdrawalHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Get all withdrawals for the group
        $withdrawals = Withdrawal::where('group_id', $groupId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.admin.member_withdrawals', compact('group', 'withdrawals'));
    }

    /**
     * Approve a withdrawal request
     */
    public function approveWithdrawal($groupId, $withdrawal_id)
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($withdrawal_id);
        
        // Verify the withdrawal belongs to the group
        if ($withdrawal->group_id != $groupId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid withdrawal request'
            ], 400);
        }

        // Check if withdrawal is still pending
        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This withdrawal request has already been processed'
            ], 400);
        }

        // Update withdrawal status
        $withdrawal->update([
            'status' => 'approved',
            'approve_date' => now()
        ]);

        // Create notification for both user and group
        Notification::create([
            'target_user_id' => $withdrawal->user_id,
            'target_group_id' => $groupId,
            'title' => 'Withdrawal Request Approved',
            'message' => "{$withdrawal->user->name}'s withdrawal request of ৳" . number_format($withdrawal->amount, 2) . " has been approved. The amount will be sent to their {$withdrawal->payment_method} account ({$withdrawal->payment_number}).",
            'status' => 'unread',
            'type' => 'withdrawal'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request has been approved successfully'
        ]);
    }

    /**
     * Decline a withdrawal request
     */
    public function declineWithdrawal($groupId, $withdrawal_id)
    {
        $withdrawal = Withdrawal::with('user')->findOrFail($withdrawal_id);
        
        // Verify the withdrawal belongs to the group
        if ($withdrawal->group_id != $groupId) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid withdrawal request'
            ], 400);
        }

        // Check if withdrawal is still pending
        if ($withdrawal->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This withdrawal request has already been processed'
            ], 400);
        }

        // Update withdrawal status
        $withdrawal->update([
            'status' => 'declined'
        ]);

        // Create notification for both user and group
        Notification::create([
            'target_user_id' => $withdrawal->user_id,
            'target_group_id' => $groupId,
            'title' => 'Withdrawal Request Declined',
            'message' => "{$withdrawal->user->name}'s withdrawal request of ৳" . number_format($withdrawal->amount, 2) . " has been declined. Please contact the group admin for more information.",
            'status' => 'unread',
            'type' => 'withdrawal'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request has been declined'
        ]);
    }

    // Member Methods
    public function create($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $userId = Auth::id();

        // Calculate available net balance for the member
        $totalSavings = Savings::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->sum('amount');

        $totalApprovedWithdrawals = Withdrawal::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $totalApprovedLoans = \App\Models\LoanRequest::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $netAvailableBalance = $totalSavings - $totalApprovedWithdrawals - $totalApprovedLoans;

        return view('groups.member.withdrawal_request', compact('group', 'netAvailableBalance', 'totalSavings', 'totalApprovedWithdrawals', 'totalApprovedLoans'));
    }

    public function store(Request $request, $groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $userId = Auth::id();

        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_number' => 'required|string',
            'payment_method' => 'required|in:Bkash,Nagad,Rocket',
        ]);

        // Check if user has sufficient savings (considering approved withdrawals and loans)
        $totalSavings = Savings::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->sum('amount');

        $totalApprovedWithdrawals = Withdrawal::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $totalApprovedLoans = \App\Models\LoanRequest::where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('status', 'approved')
            ->sum('amount');

        $netAvailableBalance = $totalSavings - $totalApprovedWithdrawals - $totalApprovedLoans;

        if ($netAvailableBalance < $request->amount) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['amount' => 'Insufficient available balance. Your net available balance is ৳' . number_format($netAvailableBalance, 2) . ' (Total Savings: ৳' . number_format($totalSavings, 2) . ' - Approved Withdrawals: ৳' . number_format($totalApprovedWithdrawals, 2) . ' - Approved Loans: ৳' . number_format($totalApprovedLoans, 2) . ')']);
        }

        // Create withdrawal request
        Withdrawal::create([
            'user_id' => $userId,
            'group_id' => $groupId,
            'amount' => $request->amount,
            'payment_number' => $request->payment_number,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->back()
            ->with('success', 'Withdrawal request submitted successfully.')
            ->with('just_submitted', true);
    }

    /**
     * Display the member's withdrawal history for a specific group.
     */
    public function withdrawalHistory($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        
        // Get withdrawals for the current user in this group
        $withdrawals = Withdrawal::where('group_id', $groupId)
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('groups.member.withdrawal_history', compact('group', 'withdrawals'));
    }

    public function downloadPdf($withdrawalId)
    {
        $withdrawal = Withdrawal::findOrFail($withdrawalId);
        $group = MyGroup::findOrFail($withdrawal->group_id);
        $user = Auth::user();

        $pdf = PDF::loadView('pdf.withdrawal', [
            'withdrawal' => $withdrawal,
            'group' => $group,
            'user' => $user
        ]);

        return $pdf->download('withdrawal-request-' . $withdrawalId . '.pdf');
    }
} 