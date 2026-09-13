@extends('layouts.app')

@section('title', 'My Profile - CholoSave')

@section('content')
<div class="profile-container">
    <div class="profile-wrapper">
        <!-- Profile Information Section -->
        <div class="profile-info">
            <div class="profile-header">
                <div class="profile-avatar">
                    <span class="avatar-text">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <h1>{{ Auth::user()->name }}</h1>
                <p class="profile-email">{{ Auth::user()->email }}</p>
            </div>

            <div class="profile-details">
                <h2>Personal Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Full Name</span>
                        <span class="info-value">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone Number</span>
                        <span class="info-value">{{ Auth::user()->phone_number ?? 'Not provided' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Member Since</span>
                        <span class="info-value">{{ Auth::user()->created_at->format('F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="password-section">
            <h2>Change Password</h2>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update-password') }}" class="password-form">
                @csrf
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="form-input"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        class="form-input"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation" 
                        class="form-input"
                        required
                    >
                </div>

                <button type="submit" class="update-button">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    min-height: calc(100vh - 5rem);
}

.profile-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.profile-info {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.profile-header {
    text-align: center;
    margin-bottom: 2rem;
}

.profile-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.avatar-text {
    color: white;
    font-size: 2.5rem;
    font-weight: 600;
}

.profile-header h1 {
    font-size: 1.5rem;
    color: #1E40AF;
    margin-bottom: 0.5rem;
}

.profile-email {
    color: #6B7280;
    font-size: 1rem;
}

.profile-details h2 {
    font-size: 1.25rem;
    color: #1E40AF;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #E5E7EB;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-label {
    color: #6B7280;
    font-size: 0.875rem;
}

.info-value {
    color: #1F2937;
    font-weight: 500;
}

.password-section {
    background: white;
    border-radius: 1rem;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.password-section h2 {
    font-size: 1.25rem;
    color: #1E40AF;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #E5E7EB;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #4B5563;
    font-weight: 500;
}

.form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: #1E40AF;
}

.update-button {
    width: 100%;
    padding: 0.75rem 1rem;
    background: #1E40AF;
    color: white;
    border: none;
    border-radius: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.update-button:hover {
    background: #1E3A8A;
    transform: translateY(-2px);
}

.alert {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #D1FAE5;
    color: #065F46;
    border: 1px solid #A7F3D0;
}

.alert-error {
    background: #FEE2E2;
    color: #991B1B;
    border: 1px solid #FECACA;
}

@media (max-width: 768px) {
    .profile-wrapper {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 