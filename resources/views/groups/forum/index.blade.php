@extends('layouts.app')

@section('title', 'Forum')

@section('content')
<style>
    * {
        box-sizing: border-box;
    }
    
    .forum-bg {
        background: #fafbfc;
        min-height: 100vh;
        padding: 1.5rem 0;
    }
    
    .forum-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f3f4;
    }
    
    .forum-header h3 {
        color: #1a1a1a;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }
    
    .forum-header p {
        color: #6b7280;
        font-size: 1rem;
        margin-bottom: 1.5rem;
        font-weight: 400;
    }
    
    .forum-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        border: 1px solid #f1f3f4;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .forum-card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border-color: #e5e7eb;
        transform: translateY(-1px);
    }
    
    .forum-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 1.1rem;
        margin-right: 1rem;
        text-transform: uppercase;
        flex-shrink: 0;
    }
    
    .forum-title-link {
        color: #1a1a1a;
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
        line-height: 1.4;
        display: block;
        margin-bottom: 0.5rem;
    }
    
    .forum-title-link:hover {
        color: #667eea;
    }
    
    .forum-content-preview {
        color: #6b7280;
        line-height: 1.5;
        margin-bottom: 1rem;
        font-size: 0.95rem;
    }
    
    .forum-meta {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        font-size: 0.85rem;
        color: #9ca3af;
        flex-wrap: wrap;
    }
    
    .forum-meta span {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    
    .forum-meta i {
        font-size: 0.8rem;
        opacity: 0.7;
    }
    
    .btn {
        border-radius: 12px;
        padding: 0.7rem 1.2rem;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: #667eea;
        color: white;
    }
    
    .btn-primary:hover {
        background: #5a6fd8;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .btn-success {
        background: #10b981;
        color: white;
    }
    
    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-secondary {
        background: #f9fafb;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
    
    .btn-secondary:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-header {
        background: white;
        color: #1a1a1a;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f1f3f4;
    }
    
    .modal-title {
        font-weight: 700;
        font-size: 1.3rem;
        color: #1a1a1a;
    }
    
    .modal-body {
        padding: 2rem;
        background: white;
    }
    
    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #f1f3f4;
        background: #fafbfc;
    }
    
    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .form-control {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.8rem 1rem;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        background: white;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .alert {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        margin-bottom: 1.5rem;
    }
    
    .alert-info {
        background: #f0f9ff;
        color: #0369a1;
        border-left: 4px solid #0ea5e9;
    }
    
    .alert-danger {
        background: #fef2f2;
        color: #dc2626;
        border-left: 4px solid #ef4444;
    }
    
    .alert ul {
        margin: 0;
        padding-left: 1.2rem;
    }
    
    .btn-close {
        background: none;
        border: none;
        color: #6b7280;
        opacity: 1;
        font-size: 1.1rem;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .btn-close:hover {
        background: #f3f4f6;
        color: #374151;
    }
    
    .container {
        max-width: 1000px;
    }
    
    .text-primary {
        color: #667eea !important;
        font-weight: 500;
    }
    
    .d-flex.gap-2 {
        gap: 0.8rem;
    }
    
    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        background: white;
        border-radius: 16px;
        border: 1px solid #f1f3f4;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .forum-bg {
            padding: 1rem 0;
        }
        
        .forum-header {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .forum-header h3 {
            font-size: 1.5rem;
        }
        
        .forum-card {
            padding: 1.2rem;
        }
        
        .forum-meta {
            gap: 1rem;
            font-size: 0.8rem;
        }
        
        .d-flex.gap-2 {
            flex-direction: column;
            gap: 0.5rem !important;
        }
        
        .btn {
            justify-content: center;
        }
        
        .modal-dialog {
            margin: 1rem;
        }
        
        .modal-body, .modal-header, .modal-footer {
            padding: 1.5rem;
        }
    }
    
    /* Smooth animations */
    .forum-card, .btn, .form-control {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Focus styles for accessibility */
    .btn:focus,
    .form-control:focus {
        outline: 2px solid #667eea;
        outline-offset: 2px;
    }
</style>

<div class="forum-bg">
    <div class="container">
        <div class="forum-header">
            <h3>Welcome back, {{ Auth::user()->name }}! 👋</h3>
            <p>Share your thoughts and connect with the community</p>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#askQuestionModal">
                    <i class="fas fa-plus"></i> Ask Question
                </button>
                <a href="{{ route('forum.my_questions') }}" class="btn btn-success">
                    <i class="fas fa-user"></i> My Questions
                </a>
            </div>
        </div>
        
        <div id="forum-questions-list">
            @forelse($questions as $question)
                <a href="{{ route('forum.show', $question->id) }}" class="text-decoration-none">
                    <div class="forum-card d-flex align-items-start">
                        <div class="forum-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="forum-title-link">{{ $question->title }}</span>
                            <div class="forum-content-preview">
                                {{ Str::limit($question->content, 120) }}
                                @if(Str::length($question->content) > 120)
                                    <span class="text-primary">Read more</span>
                                @endif
                            </div>
                            <div class="forum-meta">
                                <span><i class="fas fa-user"></i> {{ $question->user ? $question->user->name : 'Unknown' }}</span>
                                <span><i class="fas fa-calendar"></i> {{ $question->created_at->format('M d, Y') }}</span>
                                <span><i class="fas fa-comment"></i> {{ $question->replies_count }} replies</span>
                                <span><i class="fas fa-eye"></i> {{ $question->views }} views</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h4 style="color: #6b7280; font-weight: 600; margin-bottom: 0.5rem;">No discussions yet</h4>
                    <p style="color: #9ca3af; margin: 0;">Be the first to start a conversation!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Ask Question Modal -->
    <div class="modal fade" id="askQuestionModal" tabindex="-1" aria-labelledby="askQuestionModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="askQuestionModalLabel">Ask a Question</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
          </div>
          <form method="POST" action="{{ route('forum.store') }}">
            @csrf
            <div class="modal-body">
              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              <div class="mb-3">
                <label for="modal-title" class="form-label">Question Title</label>
                <input type="text" class="form-control" id="modal-title" name="title" value="{{ old('title') }}" placeholder="What's your question about?" required>
              </div>
              <div class="mb-3">
                <label for="modal-content" class="form-label">Description</label>
                <textarea class="form-control" id="modal-content" name="content" rows="5" placeholder="Provide more details about your question..." required>{{ old('content') }}</textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Post Question</button>
            </div>
          </form>
        </div>
      </div>
    </div>
</div>
@endsection