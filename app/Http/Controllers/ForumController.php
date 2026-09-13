<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Reply;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    // Show all questions
    public function index()
    {
        $questions = Question::withCount('replies')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('groups.forum.index', compact('questions'));
    }

    // Show form to ask a question
    public function create()
    {
        return view('groups.forum.ask');
    }

    // Store a new question
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);
        $question = Question::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
        return redirect()->route('forum.show', $question->id)->with('success', 'Question posted successfully!');
    }

    // Show only the current user's questions
    public function myQuestions()
    {
        $questions = Question::withCount('replies')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('groups.forum.my_questions', compact('questions'));
    }

    // Show a single question and its replies
    public function show($id)
    {
        $question = Question::with(['user', 'replies.user'])->findOrFail($id);
        // Increment view count
        $question->increment('views');
        return view('groups.forum.show', compact('question'));
    }

    // Store a reply to a question
    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        $question = Question::findOrFail($id);
        Reply::create([
            'question_id' => $question->id,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);
        return redirect()->route('forum.show', $question->id)->with('success', 'Reply posted successfully!');
    }
} 