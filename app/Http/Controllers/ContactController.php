<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'description' => 'required|string',
        ]);

        ContactUs::create([
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return redirect()->route('contact')->with('success', 'Thank you for your message. We will get back to you soon!');
    }

    /**
     * Mark a contact as done (admin function)
     */
    public function markAsDone($id)
    {
        $contact = ContactUs::findOrFail($id);
        $contact->update(['status' => 'done']);

        return response()->json([
            'success' => true,
            'message' => 'Contact marked as done successfully'
        ]);
    }

    /**
     * Get all contacts for admin report (ordered by date and pending status)
     */
    public function getContactsForReport()
    {
        $contacts = ContactUs::orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
                            ->orderBy('created_at', 'desc')
                            ->get();

        return $contacts;
    }
}
