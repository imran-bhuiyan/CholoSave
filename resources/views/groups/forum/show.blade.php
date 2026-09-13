@extends('layouts.app')

@section('title', 'Question Details')

@section('content')
<div class="container">
    <div class="card mb-4 p-4">
        <h4 class="mb-2 text-primary">{{ $question->title }}</h4>
        <p class="mb-2">{{ $question->content }}</p>
        <div class="d-flex align-items-center gap-3 small text-secondary mb-2">
            <span><i class="fas fa-user"></i> {{ $question->user ? $question->user->name : 'Unknown' }}</span>
            <span><i class="fas fa-calendar"></i> {{ $question->created_at->format('M d, Y') }}</span>
            <span><i class="fas fa-eye"></i> {{ $question->views }} views</span>
        </div>
    </div>
    <div class="mb-4">
        <h5>Replies</h5>
        @forelse($question->replies as $reply)
            <div class="card mb-2 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="fw-bold">{{ $reply->user ? $reply->user->name : 'Unknown' }}</span>
                    <span class="small text-secondary">{{ $reply->created_at->format('M d, Y') }}</span>
                </div>
                <div>{{ $reply->content }}</div>
            </div>
        @empty
            <div class="alert alert-info">No replies yet. Be the first to reply!</div>
        @endforelse
    </div>
    <div class="card p-3">
        <h6 class="mb-2">Your Reply</h6>
        <form method="POST" action="{{ route('forum.reply', $question->id) }}">
            @csrf
            <div class="mb-2">
                <textarea class="form-control" name="content" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Reply</button>
        </form>
    </div>
</div>
@endsection 