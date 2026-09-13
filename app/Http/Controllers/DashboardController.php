<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupMembership;
use App\Models\MySavings;
use App\Models\Investment;
use App\Models\Withdrawal;
use App\Models\LoanRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $joinedGroupsCount = GroupMembership::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $totalSavings = MySavings::where('user_id', $user->id)->sum('total_amount');

        $groupIds = GroupMembership::where('user_id', $user->id)
            ->where('status', 'approved')
            ->pluck('group_id');
        
        $groupInvestments = Investment::whereIn('group_id', $groupIds)->latest()->get();
        
        $totalWithdrawals = Withdrawal::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        $totalLoans = LoanRequest::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

        return view('dashboard', compact(
            'joinedGroupsCount',
            'totalSavings',
            'groupInvestments',
            'totalWithdrawals',
            'totalLoans'
        ));
    }
} 