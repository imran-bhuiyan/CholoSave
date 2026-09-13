<?php

// app/Http/Controllers/AiTipsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AiTipsController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->id;

        // 1. Total Individual Savings
        $individual_savings = DB::table('savings')
            ->where('user_id', $user_id)
            ->sum('amount');

        // 2. Monthly Savings
        $monthly_savings = DB::table('savings')
            ->where('user_id', $user_id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // 3. Group Contributions
        $group_contributions = DB::table('group_membership as gm')
            ->join('my_group as g', 'gm.group_id', '=', 'g.group_id')
            ->leftJoin('savings as s', function ($join) use ($user_id) {
                $join->on('g.group_id', '=', 's.group_id')
                     ->where('s.user_id', '=', $user_id);
            })
            ->select(
                'g.group_id',
                'g.group_name',
                DB::raw('COALESCE(SUM(s.amount), 0) as user_contribution'),
                DB::raw('(SELECT COALESCE(SUM(s1.amount), 0) FROM savings s1 WHERE s1.group_id = g.group_id) as total_contribution'),
                'g.goal_amount',
                'g.emergency_fund'
            )
            ->where('gm.user_id', $user_id)
            ->where('gm.status', 'approved')
            ->groupBy('g.group_id', 'g.group_name', 'g.goal_amount', 'g.emergency_fund')
            ->get()
            ->map(function($item) {
                return [
                    'group_id' => $item->group_id,
                    'group_name' => $item->group_name,
                    'user_contribution' => (float) $item->user_contribution,
                    'total_contribution' => (float) $item->total_contribution,
                    'goal_amount' => (float) $item->goal_amount,
                    'emergency_fund' => (float) $item->emergency_fund,
                ];
            })->toArray();

        // 4. Emergency Fund (sum of all user's groups)
        $emergency_fund = array_sum(array_column($group_contributions, 'emergency_fund'));
        $emergency_fund_goal = array_sum(array_column($group_contributions, 'goal_amount'));

        // 5. Loan Status (example: count active loans)
        $loan = DB::table('loan_request')
            ->where('user_id', $user_id)
            ->where('status', 'approved')
            ->selectRaw('COUNT(*) as active_loans, COALESCE(SUM(amount - repayment_amount), 0) as outstanding_amount')
            ->first();
        $loan_status = ($loan->active_loans > 0)
            ? "Active loans with outstanding amount: BDT " . number_format($loan->outstanding_amount, 2)
            : "No active loans";

        // 6. Investments (optional, left empty for now)

        $financialData = [
            'individual_savings' => (float) $individual_savings,
            'monthly_savings' => (float) $monthly_savings,
            'group_contributions' => $group_contributions,
            'investments' => [], // Fill if needed
            'loan_status' => $loan_status,
            'emergency_fund' => (float) $emergency_fund,
            'emergency_fund_goal' => (float) $emergency_fund_goal,
        ];

        return view('ai_tips', ['financialData' => $financialData]);
    }
}