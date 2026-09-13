<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MyGroup;
use App\Models\Poll;
use Illuminate\Support\Facades\Auth;

class PollController extends Controller
{
    public function create($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        return view('groups.admin.create_poll', compact('group'));
    }

    public function store(Request $request, $groupId)
    {
        $request->validate([
            'question' => 'required|string|max:500'
        ]);

        try {
            $poll = Poll::create([
                'group_id' => $groupId,
                'poll_question' => $request->question,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Poll created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create poll. Please try again.'
            ], 500);
        }
    }

    public function list($groupId)
    {
        $group = MyGroup::findOrFail($groupId);
        $polls = Poll::where('group_id', $groupId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($poll) {
                $totalVotes = $poll->votes()->count();
                $yesVotes = $poll->votes()->where('vote_option', 'yes')->count();
                $noVotes = $poll->votes()->where('vote_option', 'no')->count();
                
                $poll->yes_percentage = $totalVotes > 0 ? round(($yesVotes / $totalVotes) * 100) : 0;
                $poll->no_percentage = $totalVotes > 0 ? round(($noVotes / $totalVotes) * 100) : 0;
                
                return $poll;
            });

        return view('groups.admin.poll_list', compact('group', 'polls'));
    }

    public function update(Request $request, $pollId)
    {
        $request->validate([
            'poll_question' => 'required|string|max:500',
            'status' => 'required|in:active,closed'
        ]);

        try {
            $poll = Poll::findOrFail($pollId);
            $poll->update([
                'poll_question' => $request->poll_question,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Poll updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update poll. Please try again.'
            ], 500);
        }
    }

    public function delete($pollId)
    {
        try {
            $poll = Poll::findOrFail($pollId);
            
            // Delete associated votes first
            $poll->votes()->delete();
            
            // Delete the poll
            $poll->delete();

            return response()->json([
                'success' => true,
                'message' => 'Poll deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete poll. Please try again.'
            ], 500);
        }
    }

    public function vote(Request $request, $pollId)
    {
        $request->validate([
            'vote_option' => 'required|in:yes,no'
        ]);

        try {
            $poll = Poll::findOrFail($pollId);
            $userId = Auth::id();

            // Check if user has already voted
            $existingVote = $poll->votes()->where('user_id', $userId)->first();

            if ($existingVote) {
                // Update existing vote
                $existingVote->update(['vote_option' => $request->vote_option]);
            } else {
                // Create new vote
                $poll->votes()->create([
                    'user_id' => $userId,
                    'vote_option' => $request->vote_option
                ]);
            }

            // Get updated vote statistics
            $totalVotes = $poll->votes()->count();
            $yesVotes = $poll->votes()->where('vote_option', 'yes')->count();
            $noVotes = $poll->votes()->where('vote_option', 'no')->count();
            
            $yesPercentage = $totalVotes > 0 ? round(($yesVotes / $totalVotes) * 100) : 0;
            $noPercentage = $totalVotes > 0 ? round(($noVotes / $totalVotes) * 100) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Vote recorded successfully',
                'data' => [
                    'totalVotes' => $totalVotes,
                    'yesVotes' => $yesVotes,
                    'noVotes' => $noVotes,
                    'yesPercentage' => $yesPercentage,
                    'noPercentage' => $noPercentage,
                    'userVote' => $request->vote_option
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record vote. Please try again.'
            ], 500);
        }
    }
} 