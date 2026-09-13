<?php

namespace App\Http\Controllers;

use App\Models\ExpertTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpertController extends Controller
{
    public function index()
    {
        $experts = ExpertTeam::all();
        
        // If no experts exist, create some sample data
        if ($experts->isEmpty()) {
            $experts = collect([
                (object)[
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'phone' => '+1 234 567 890',
                    'expertise' => 'Financial Advisor',
                    'bio' => 'Experienced financial advisor with over 10 years of experience in wealth management and investment strategies.',
                    'image' => 'https://via.placeholder.com/300x200'
                ],
                (object)[
                    'name' => 'Jane Smith',
                    'email' => 'jane@example.com',
                    'phone' => '+1 234 567 891',
                    'expertise' => 'Investment Specialist',
                    'bio' => 'Specialized in portfolio management and risk assessment with a focus on sustainable investments.',
                    'image' => 'https://via.placeholder.com/300x200'
                ],
                (object)[
                    'name' => 'Mike Johnson',
                    'email' => 'mike@example.com',
                    'phone' => '+1 234 567 892',
                    'expertise' => 'Tax Consultant',
                    'bio' => 'Expert in tax planning and optimization strategies for individuals and businesses.',
                    'image' => 'https://via.placeholder.com/300x200'
                ]
            ]);
        }

        return view('experts', compact('experts'));
    }

    public function create()
    {
        return view('admin.add_expert');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'bio' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uniqid('expert_').'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images/expert'), $imageName);
            $validated['image'] = 'images/expert/' . $imageName;
        }

        ExpertTeam::create($validated);

        return redirect()->route('admin.expert.manage')->with('success', 'Expert added successfully!');
    }

    public function manage()
    {
        $experts = ExpertTeam::all();
        return view('admin.manage_experts', compact('experts'));
    }

    public function edit($id)
    {
        $expert = ExpertTeam::findOrFail($id);
        return view('admin.edit_expert', compact('expert'));
    }

    public function update(Request $request, $id)
    {
        $expert = ExpertTeam::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:255',
            'expertise' => 'required|string|max:255',
            'bio' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20480',
        ]);

        // Handle image upload if present
        if ($request->hasFile('image')) {
            // Delete old image if exists and is local
            if ($expert->image && !Str::startsWith($expert->image, 'http') && file_exists(public_path($expert->image))) {
                unlink(public_path($expert->image));
            }
            $image = $request->file('image');
            $imageName = uniqid('expert_').'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images/expert'), $imageName);
            $validated['image'] = 'images/expert/' . $imageName;
        }

        $expert->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Expert updated successfully!']);
        }
        return redirect()->route('admin.expert.manage')->with('success', 'Expert updated successfully!');
    }

    public function destroy($id)
    {
        $expert = ExpertTeam::findOrFail($id);
        // Delete image file if exists and is local
        if ($expert->image && !Str::startsWith($expert->image, 'http') && file_exists(public_path($expert->image))) {
            unlink(public_path($expert->image));
        }
        $expert->delete();
        return redirect()->route('admin.expert.manage')->with('success', 'Expert deleted successfully!');
    }
}
