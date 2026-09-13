<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentReturn;
use App\Models\MyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    public function create(MyGroup $group)
    {
        // Check if user is authorized to access this group
        if (!$group->isAdmin(Auth::id())) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        return view('groups.admin.new_investment', compact('group'));
    }

    public function store(Request $request, MyGroup $group)
    {
        // Check if user is authorized to access this group
        if (!$group->isAdmin(Auth::id())) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        // Validate the request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'investment_type' => 'required|string',
            'ex_profit' => 'required|numeric|min:0',
            'ex_return_date' => 'required|date|after:today',
        ]);

        try {
            // Create the investment
            $investment = new Investment([
                'group_id' => $group->group_id,
                'amount' => $validated['amount'],
                'investment_type' => $validated['investment_type'],
                'ex_profit' => $validated['ex_profit'],
                'ex_return_date' => $validated['ex_return_date'],
                'status' => 'pending'
            ]);

            $investment->save();

            return redirect()
                ->route('admin.investment.create', $group->group_id)
                ->with('success', 'Investment created successfully!')
                ->with('just_submitted', true);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to create investment. Please try again.')
                ->withInput();
        }
    }

    public function createReturn(MyGroup $group)
    {
        // Check if user is authorized to access this group
        if (!$group->isAdmin(Auth::id())) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        // Get all pending investments for this group
        $pendingInvestments = Investment::where('group_id', $group->group_id)
            ->where('status', 'pending')
            ->get();

        return view('groups.admin.record_return', compact('group', 'pendingInvestments'));
    }

    public function storeReturn(Request $request, MyGroup $group)
    {
        // Check if user is authorized to access this group
        if (!$group->isAdmin(Auth::id())) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        // Validate the request
        $validated = $request->validate([
            'investment_id' => 'required|exists:investments,investment_id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|min:10',
        ]);

        try {
            DB::beginTransaction();

            // Create the investment return
            $return = new InvestmentReturn([
                'investment_id' => $validated['investment_id'],
                'amount' => $validated['amount'],
                'description' => $validated['description']
            ]);

            $return->save();

            // Update the investment status to completed
            $investment = Investment::find($validated['investment_id']);
            $investment->status = 'completed';
            $investment->save();

            DB::commit();

            return redirect()
                ->route('admin.investment.return.create', $group->group_id)
                ->with('success', 'Investment return recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Failed to record investment return. Please try again.')
                ->withInput();
        }
    }

    public function history(MyGroup $group)
    {
        // Check if user is authorized to access this group
        if (!$group->isAdmin(Auth::id())) {
            return redirect()->route('home')->with('error', 'Unauthorized access');
        }

        // Get all investments for this group with their returns
        $investments = Investment::where('group_id', $group->group_id)
            ->with('returns')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate summary statistics
        $totalInvestments = $investments->sum('amount');
        $totalReturns = $investments->sum(function ($investment) {
            return $investment->returns->sum('amount');
        });
        $netProfitLoss = $totalReturns - $totalInvestments;

        return view('groups.admin.investment_history', compact(
            'group',
            'investments',
            'totalInvestments',
            'totalReturns',
            'netProfitLoss'
        ));
    }
} 