@extends($isAdmin ? 'layouts.group_admin' : 'layouts.group_member')

@section('content')
<div class="flex justify-center items-center min-h-[80vh] bg-gradient-to-br from-slate-50 to-slate-100">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-xl border border-slate-200/50 h-[85vh] flex flex-col overflow-hidden backdrop-blur-sm">
        <!-- Enhanced Header -->
        <header class="bg-gradient-to-r from-slate-900 to-slate-800 text-white shadow-lg">
            <div class="px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ $isAdmin ? route('groups.admin.dashboard', $groupId) : route('groups.member.dashboard', $groupId) }}" 
                       class="text-slate-300 hover:text-white transition-colors p-2 hover:bg-slate-700 rounded-lg">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-users text-white text-sm"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full animate-pulse"></div>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-white flex items-center gap-3">
                                Group Chat
                                @if($isAdmin)
                                    <span class="text-xs font-medium text-emerald-300 bg-emerald-900/30 px-3 py-1 rounded-full border border-emerald-400/30">
                                        <i class="fas fa-crown mr-1"></i>Admin Mode
                                    </span>
                                @endif
                            </h1>
                            <p class="text-sm text-slate-300 flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                Online now
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        <!-- Enhanced Chat Container -->
        <div class="flex-1 messages overflow-y-auto bg-gradient-to-b from-slate-50/30 to-white p-6 space-y-6 relative">
            <!-- Chat background pattern -->
            <div class="absolute inset-0 opacity-5 bg-[url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60"><g fill="none" fill-rule="evenodd"><g fill="%23000000" fill-opacity="0.1"><circle cx="30" cy="30" r="1"/></g></g></svg>')] pointer-events-none"></div>
            
            @foreach($messages as $message)
                <div class="message {{ $message->user_id === auth()->id() ? 'flex justify-end' : 'flex justify-start' }} relative z-10">
                    <div class="max-w-[75%] group">
                        @if($message->user_id !== auth()->id())
                            <!-- Other user's message -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-semibold shadow-md flex-shrink-0 mt-1">
                                    {{ substr($message->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-sm font-semibold text-slate-700">
                                            {{ $message->user->name }}
                                        </span>
                                        @if($message->is_admin)
                                            <span class="bg-gradient-to-r from-amber-400 to-amber-600 text-white text-xs px-2 py-0.5 rounded-full font-medium shadow-sm">
                                                <i class="fas fa-crown mr-1"></i>Admin
                                            </span>
                                        @endif
                                        <span class="text-xs text-slate-400">
                                            {{ $message->created_at->format('h:i A') }}
                                        </span>
                                    </div>
                                    <div class="message-bubble bg-white text-slate-800 rounded-2xl rounded-tl-md px-4 py-3 shadow-md border border-slate-200/50 hover:shadow-lg transition-all duration-200 relative">
                                        <div class="absolute -left-2 top-4 w-0 h-0 border-r-8 border-r-white border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
                                        {{ $message->message }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Current user's message -->
                            <div class="flex items-end gap-3 justify-end">
                                <div class="text-right">
                                    <div class="flex items-center gap-2 mb-2 justify-end">
                                        <span class="text-xs text-slate-400">
                                            {{ $message->created_at->format('h:i A') }}
                                        </span>
                                        @if($message->is_admin)
                                            <span class="bg-gradient-to-r from-amber-400 to-amber-600 text-white text-xs px-2 py-0.5 rounded-full font-medium shadow-sm">
                                                <i class="fas fa-crown mr-1"></i>Admin
                                            </span>
                                        @endif
                                        <span class="text-sm font-semibold text-slate-700">
                                            {{ $message->user->name }}
                                        </span>
                                    </div>
                                    <div class="message-bubble bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-lg hover:shadow-xl transition-all duration-200 relative">
                                        <div class="absolute -right-2 top-4 w-0 h-0 border-l-8 border-l-blue-500 border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
                                        {{ $message->message }}
                                    </div>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-sm font-semibold shadow-md flex-shrink-0">
                                    {{ substr($message->user->name, 0, 1) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Enhanced Message Input Area -->
        <div class="border-t border-slate-200 bg-white p-6 shadow-lg">
            <form class="message-form">
                <div class="flex gap-4 items-end">
                    <div class="flex-1 relative">
                        <textarea 
                            name="message" 
                            class="message-input w-full px-4 py-4 border-2 border-slate-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none bg-slate-50 focus:bg-white transition-all duration-200 text-slate-800 placeholder-slate-400"
                            placeholder="Type your message..."
                            rows="1"
                        ></textarea>
                    </div>
                    <button 
                        type="submit" 
                        class="px-6 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center gap-3 font-semibold shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transform hover:scale-105 active:scale-95"
                    >
                        <i class="fas fa-paper-plane"></i>
                        <span>Send</span>
                    </button>
                </div>
            </form>
            
            <!-- Typing indicator placeholder -->
            <div class="typing-indicator mt-3 text-sm text-slate-500 hidden">
                <div class="flex items-center gap-2">
                    <div class="flex gap-1">
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                    <span>Someone is typing...</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Enhanced scrollbar */
.messages::-webkit-scrollbar {
    width: 8px;
}
.messages::-webkit-scrollbar-track {
    background: transparent;
}
.messages::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #cbd5e1, #94a3b8);
    border-radius: 20px;
    border: 2px solid transparent;
    background-clip: content-box;
}
.messages::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #94a3b8, #64748b);
    background-clip: content-box;
}

/* Enhanced animations */
.message {
    animation: slideInMessage 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

@keyframes slideInMessage {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Message bubble enhancements */
.message-bubble {
    position: relative;
    backdrop-filter: blur(10px);
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.message-bubble:hover {
    transform: translateY(-1px);
}

/* Input enhancements */
.message-input {
    min-height: 56px;
    max-height: 120px;
    line-height: 1.5;
    font-size: 16px;
    transition: all 0.3s ease;
}

.message-input:focus {
    transform: translateY(-1px);
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
}

/* Button enhancements */
button[type="submit"]:hover {
    box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .message {
        margin: 0 -1rem;
    }
    
    .max-w-\[75\%\] {
        max-width: 85%;
    }
    
    .message-input {
        font-size: 16px; /* Prevents zoom on iOS */
    }
}

/* Glassmorphism effect */
.backdrop-blur-sm {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

/* Enhanced gradient effects */
.bg-gradient-to-r {
    background-size: 200% 200%;
    animation: gradientShift 8s ease infinite;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Message timestamp fade in */
.group:hover .text-xs {
    opacity: 1;
    transition: opacity 0.2s ease;
}

.text-xs {
    opacity: 0.7;
}

/* Pulse animation for online indicator */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Enhanced focus states */
*:focus {
    outline: none;
}

button:focus-visible,
textarea:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}
</style>
@endpush

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Define variables first
    const groupId = {{ $groupId }};
    const userId = {{ auth()->id() }};
    const username = "{{ auth()->user()->name }}";
    const isAdmin = {{ $isAdmin ? 'true' : 'false' }};

    // Debug information
    console.log('=== Chat Debug Information ===');
    console.log('Group ID:', groupId);
    console.log('User ID:', userId);
    console.log('Username:', username);
    console.log('Is Admin:', isAdmin);

    // Initialize Pusher
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        forceTLS: true,
        enabledTransports: ['ws', 'wss'],
        disabledTransports: ['xhr_streaming', 'xhr_polling'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }
    });

    // Debug Pusher connection
    pusher.connection.bind('state_change', states => {
        console.log('Pusher connection state changed:', states);
    });

    pusher.connection.bind('connected', () => {
        console.log('Pusher connected successfully');
    });

    pusher.connection.bind('error', (err) => {
        console.error('Pusher connection error:', err);
    });

    // Subscribe to channel
    const channel = pusher.subscribe('group.' + groupId);
    
    channel.bind('pusher:subscription_succeeded', () => {
        console.log('Successfully subscribed to channel: group.' + groupId);
    });

    channel.bind('pusher:subscription_error', (error) => {
        console.error('Error subscribing to channel:', error);
    });

    channel.bind('new-message', function(data) {
        console.log('Received new message event:', data);
        
        // Don't add the message if it's from the current user
        if (data.user_id === userId) {
            console.log('Skipping own message');
            return;
        }

        const messagesDiv = document.querySelector('.messages');
        if (!messagesDiv) {
            console.error('Messages container not found');
            return;
        }

        console.log('Adding new message to chat:', data);

        // Format the time
        const messageTime = data.created_at || new Date().toLocaleTimeString([], { 
            hour: 'numeric', 
            minute: '2-digit', 
            hour12: true 
        });

        const messageElem = document.createElement('div');
        const adminBadge = data.is_admin 
            ? '<span class="bg-gradient-to-r from-amber-400 to-amber-600 text-white text-xs px-2 py-0.5 rounded-full font-medium shadow-sm"><i class="fas fa-crown mr-1"></i>Admin</span>' 
            : '';
        
        const userInitial = data.username ? data.username.charAt(0).toUpperCase() : '?';
        
        messageElem.className = `message flex justify-start relative z-10`;
        messageElem.innerHTML = `
            <div class="max-w-[75%] group">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-semibold shadow-md flex-shrink-0 mt-1">
                        ${userInitial}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm font-semibold text-slate-700">
                                ${data.username}
                            </span>
                            ${adminBadge}
                            <span class="text-xs text-slate-400">${messageTime}</span>
                        </div>
                        <div class="message-bubble bg-white text-slate-800 rounded-2xl rounded-tl-md px-4 py-3 shadow-md border border-slate-200/50 hover:shadow-lg transition-all duration-200 relative">
                            <div class="absolute -left-2 top-4 w-0 h-0 border-r-8 border-r-white border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
                            ${data.message.message}
                        </div>
                    </div>
                </div>
            </div>`;
        
        messagesDiv.appendChild(messageElem);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        console.log('Message added to chat successfully');
    });

    // Handle form submission
    const messageForm = document.querySelector('.message-form');
    if (messageForm) {
        messageForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            console.log('Form submitted!');
            
            const messageInput = document.querySelector('textarea[name="message"]');
            const message = messageInput ? messageInput.value.trim() : '';
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : null;
            
            if (message !== '') {
                try {
                    console.log('Sending message:', message);
                    const response = await fetch(`/groups/${groupId}/chat`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            message: message,
                            group_id: groupId
                        })
                    });

                    const data = await response.json();
                    console.log('Server response:', data);

                    if (data.success) {
                        // Add the message to the chat immediately for the sender
                        const messagesDiv = document.querySelector('.messages');
                        const messageElem = document.createElement('div');
                        const time = new Date().toLocaleTimeString([], { 
                            hour: 'numeric', 
                            minute: '2-digit', 
                            hour12: true 
                        });
                        
                        const adminBadge = isAdmin 
                            ? '<span class="bg-gradient-to-r from-amber-400 to-amber-600 text-white text-xs px-2 py-0.5 rounded-full font-medium shadow-sm"><i class="fas fa-crown mr-1"></i>Admin</span>' 
                            : '';
                        
                        const userInitial = username ? username.charAt(0).toUpperCase() : '?';
                        
                        messageElem.className = `message flex justify-end relative z-10`;
                        messageElem.innerHTML = `
                            <div class="max-w-[75%] group">
                                <div class="flex items-end gap-3 justify-end">
                                    <div class="text-right">
                                        <div class="flex items-center gap-2 mb-2 justify-end">
                                            <span class="text-xs text-slate-400">${time}</span>
                                            ${adminBadge}
                                            <span class="text-sm font-semibold text-slate-700">${username}</span>
                                        </div>
                                        <div class="message-bubble bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl rounded-tr-md px-4 py-3 shadow-lg hover:shadow-xl transition-all duration-200 relative">
                                            <div class="absolute -right-2 top-4 w-0 h-0 border-l-8 border-l-blue-500 border-t-4 border-t-transparent border-b-4 border-b-transparent"></div>
                                            ${message}
                                        </div>
                                    </div>
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-sm font-semibold shadow-md flex-shrink-0">
                                        ${userInitial}
                                    </div>
                                </div>
                            </div>`;
                        
                        messagesDiv.appendChild(messageElem);
                        messagesDiv.scrollTop = messagesDiv.scrollHeight;
                        
                        messageInput.value = '';
                        console.log('Message added to sender\'s chat');
                    } else {
                        console.error('Failed to send message:', data.error);
                        alert('Failed to send message. Please try again.');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    alert('Error sending message. Please try again.');
                }
            }
        });
    }

    // Auto-scroll to bottom on page load
    document.addEventListener('DOMContentLoaded', function() {
        const messagesDiv = document.querySelector('.messages');
        if (messagesDiv) {
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }
    });

    // Auto-resize textarea
    const messageInput = document.querySelector('.message-input');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
</script>
@endpush