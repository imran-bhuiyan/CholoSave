<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Contribution;
use App\Models\User;

class CampaignController extends Controller
{
    // Show the campaign creation form
    public function create()
    {
        return view('admin.campaign.create');
    }

    // Store a new campaign
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
            'bKash' => 'nullable|string|max:50',
            'Rocket' => 'nullable|string|max:50',
            'Nagad' => 'nullable|string|max:50',
        ]);

        $validated['current_amount'] = 0;
        $validated['status'] = 'active';

        Campaign::create($validated);

        return redirect()->route('admin.campaign.manage')->with('success', 'Campaign created successfully!');
    }

    // List/manage campaigns
    public function index()
    {
        $campaigns = Campaign::orderBy('created_at', 'desc')->get();
        return view('admin.campaign.manage', compact('campaigns'));
    }

    // Show the edit form for a campaign
    public function edit($id)
    {
        $campaign = Campaign::findOrFail($id);
        return view('admin.campaign.edit', compact('campaign'));
    }

    // Update the campaign
    public function update(Request $request, $id)
    {
        $campaign = Campaign::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'goal_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date|after:today',
            'status' => 'required|in:active,completed',
            'bKash' => 'nullable|string|max:50',
            'Rocket' => 'nullable|string|max:50',
            'Nagad' => 'nullable|string|max:50',
        ]);
        $campaign->update($validated);
        return redirect()->route('admin.campaign.manage')->with('success', 'Campaign updated successfully!');
    }

    // Get active campaigns for dashboard/welcome
    public function getActiveCampaigns()
    {
        $campaigns = Campaign::active()->orderBy('created_at', 'desc')->take(1)->get();
        return $campaigns;
    }

    public static function latestActive()
    {
        return self::active()->orderBy('created_at', 'desc')->first();
    }

    public function contributions($id)
    {
        $campaign = \App\Models\Campaign::findOrFail($id);
        $contributions = \App\Models\Contribution::where('campaign_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($c) {
                return [
                    'user_name' => $c->user ? $c->user->name : 'Unknown',
                    'amount' => $c->amount,
                    'date' => $c->created_at ? $c->created_at->format('M d, Y') : '',
                ];
            });
        return response()->json([
            'title' => $campaign->title,
            'description' => $campaign->description,
            'contributions' => $contributions,
        ]);
    }

    // Store a contribution for a campaign
    public function storeContribution(Request $request, $id)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|in:bkash,Rocket,Nagad',
        ]);

        $campaign = Campaign::findOrFail($id);
        
        // Ensure campaign is active
        if ($campaign->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'This campaign is no longer active.'], 400);
        }

        // Create the contribution
        $contribution = \App\Models\Contribution::create([
            'campaign_id' => $id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'created_at' => now(),
        ]);

        // Update campaign's current amount
        $campaign->increment('current_amount', $validated['amount']);

        return response()->json(['success' => true, 'contribution_id' => $contribution->id]);
    }

    // Download contribution receipt as PDF
    public function downloadReceipt($contributionId)
    {
        $contribution = Contribution::with(['campaign', 'user'])->findOrFail($contributionId);
        $campaign = $contribution->campaign;
        $user = $contribution->user;
        // For demo, assume payment_method is not stored, so show as 'Online' (or you can store it if you want)
        $payment_method = 'Online';
        $pdf = Pdf::loadView('receipt', [
            'contribution' => $contribution,
            'campaign' => $campaign,
            'user' => $user,
            'payment_method' => $payment_method,
        ]);
        return $pdf->download('contribution_receipt_'.$contribution->id.'.pdf');
    }
} 