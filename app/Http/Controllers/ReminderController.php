<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupMembership;
use App\Models\MyGroup;
use App\Models\LoanRequest;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();
        $today = Carbon::today();
        $three_days_later = Carbon::today()->addDays(3);

        // Payment Reminders
        $groups = GroupMembership::with('group')
            ->where('user_id', $user_id)
            ->where('status', 'approved')
            ->get();

        $payment_reminders = [];
        foreach ($groups as $membership) {
            $group = $membership->group;
            if (!$group) continue;
            $is_payment_due = false;
            $next_payment_date = null;

            if ($group->dps_type == 'monthly') {
                $next_payment_date = Carbon::parse($group->start_date)->addMonth();
            } elseif ($group->dps_type == 'weekly') {
                $next_payment_date = Carbon::parse($group->start_date)->addDays(7);
            }

            if ($next_payment_date) {
                $days_until_payment = $today->diffInDays($next_payment_date, false);
                if ($days_until_payment <= 2 && $days_until_payment >= 0) {
                    $is_payment_due = true;
                }
            }

            if ($is_payment_due) {
                $payment_reminders[] = [
                    'group_id' => $group->group_id,
                    'group_name' => $group->group_name,
                    'payment_type' => $group->dps_type,
                    'next_payment_date' => $next_payment_date ? $next_payment_date->toDateString() : null,
                    'amount' => $group->amount,
                ];
            }
        }

        // Loan Reminders
        $loan_reminders = LoanRequest::with('group')
            ->where('user_id', $user_id)
            ->where('status', 'approved')
            ->whereBetween('return_time', [$today->toDateString(), $three_days_later->toDateString()])
            ->get()
            ->map(function ($loan) {
                return [
                    'group_id' => $loan->group_id,
                    'group_name' => $loan->group ? $loan->group->group_name : '',
                    'return_date' => $loan->return_time,
                    'amount' => $loan->amount,
                ];
            });

        return view('reminders.index', [
            'payment_reminders' => $payment_reminders,
            'loan_reminders' => $loan_reminders,
        ]);
    }
}
