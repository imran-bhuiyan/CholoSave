@extends('layouts.app')

@section('title', 'My Questions')

@section('content')
<style>
    .forum-bg {
        background: #f8fafc;
        min-height: 100vh;
        padding-bottom: 2rem;
    }
    .forum-card {
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(30,64,175,0.04);
        transition: box-shadow 0.2s, border-color 0.2s;
        cursor: pointer;
    }
    .forum-card:hover {
        box-shadow: 0 4px 16px rgba(30,64,175,0.10);
        border-color: #1e40af;
    }
    .forum-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1E40AF 0%, #16A34A 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        margin-right: 1rem;
        text-transform: uppercase;
    }
    .forum-meta {
        background: #f1f5f9;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        display: flex;
        gap: 1.5rem;
        align-items: center;
        font-size: 0.95rem;
        color: #64748b;
        margin-top: 0.5rem;
    }
    .forum-title-link {
        color: #1e40af;
        font-weight: 600;
        font-size: 1.15rem;
        text-decoration: none;
        transition: color 0.2s;
    }
    .forum-title-link:hover {
        color: #16a34a;
        text-decoration: underline;
    }
    .forum-content-preview {
        color: #334155;
        margin-bottom: 0.5rem;
    }
</style>
<div class="forum-bg">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>My Questions</h3>
            <a href="{{ route('forum.index') }}" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Back to Forum</a>
        </div>
        <div id="my-questions-list">
            @forelse($questions as $question)
                <a href="{{ route('forum.show', $question->id) }}" class="text-decoration-none">
                    <div class="forum-card mb-3 p-3 d-flex align-items-start">
                        <div class="forum-avatar">
                            {{ $question->user ? strtoupper(substr($question->user->name, 0, 1)) : '?' }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="forum-title-link">{{ $question->title }}</span>
                            </div>
                            <div class="forum-content-preview">
                                {{ Str::limit($question->content, 120) }}
                                @if(Str::length($question->content) > 120)
                                    <span class="text-primary">Read more</span>
                                @endif
                            </div>
                            <div class="forum-meta">
                                <span><i class="fas fa-calendar"></i> {{ $question->created_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-comment"></i> {{ $question->replies_count }} replies</span>
                                <span><i class="fas fa-eye"></i> {{ $question->views }} views</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="alert alert-info">You haven't posted any questions yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection 