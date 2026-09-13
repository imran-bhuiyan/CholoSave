<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Savings;
use App\Models\TransactionInfo;
use App\Models\MyGroup;
use App\Models\Investment;
use App\Models\ContactUs;
use Carbon\Carbon;

class SiteAdminController extends Controller
{
    public function index()
    {
        // Statistics
        $totalUsers = User::count();
        $totalSavings = Savings::sum('amount');
        $totalTransactions = TransactionInfo::count();
        $totalGroups = MyGroup::count();
        $totalInvestments = Investment::sum('amount');
        $totalReports = ContactUs::count();

        // Monthly Data for last 6 months
        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('M Y');
        })->reverse()->values();

        $usersPerMonth = $months->map(function ($month) {
            [$m, $y] = explode(' ', $month);
            return User::whereMonth('created_at', Carbon::parse("01 $m $y")->month)
                ->whereYear('created_at', Carbon::parse("01 $m $y")->year)
                ->count();
        });
        $savingsPerMonth = $months->map(function ($month) {
            [$m, $y] = explode(' ', $month);
            return Savings::whereMonth('created_at', Carbon::parse("01 $m $y")->month)
                ->whereYear('created_at', Carbon::parse("01 $m $y")->year)
                ->sum('amount');
        });
        $transactionsPerMonth = $months->map(function ($month) {
            [$m, $y] = explode(' ', $month);
            return TransactionInfo::whereMonth('payment_time', Carbon::parse("01 $m $y")->month)
                ->whereYear('payment_time', Carbon::parse("01 $m $y")->year)
                ->count();
        });

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalSavings' => $totalSavings,
            'totalTransactions' => $totalTransactions,
            'totalGroups' => $totalGroups,
            'totalInvestments' => $totalInvestments,
            'totalReports' => $totalReports,
            'months' => $months,
            'usersPerMonth' => $usersPerMonth,
            'savingsPerMonth' => $savingsPerMonth,
            'transactionsPerMonth' => $transactionsPerMonth,
        ]);
    }

    public function report()
    {
        // Order by pending status first, then by date (newest first)
        $contacts = ContactUs::orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                            ->orderBy('created_at', 'desc')
                            ->get();
        return view('admin.report', compact('contacts'));
    }
} 