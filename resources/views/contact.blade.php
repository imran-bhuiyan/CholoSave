@extends('layouts.app')

@section('title', 'Contact Us - CholoSave')

@section('content')
<div class="contact-container-enhanced">
    <div class="contact-wrapper-enhanced">
        <!-- Contact Information Section -->
        <div class="contact-info-enhanced">
            <h2 class="contact-heading">Get in Touch</h2>
            <p class="contact-description-lg">
                Have questions or need assistance? We're here to help! Reach out to us through any of the following channels.
            </p>
            <div class="info-cards-enhanced">
                <div class="info-card-enhanced">
                    <div class="icon-wrapper-enhanced">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Our Location</h3>
                    <p>123 Main Street, City, Country</p>
                </div>
                <div class="info-card-enhanced">
                    <div class="icon-wrapper-enhanced">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Phone Number</h3>
                    <p>+1 234 567 890</p>
                </div>
                <div class="info-card-enhanced">
                    <div class="icon-wrapper-enhanced">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Address</h3>
                    <p>info@cholosave.com</p>
                </div>
            </div>
        </div>
        <!-- Divider for desktop -->
        <div class="contact-divider"></div>
        <!-- Contact Form Section -->
        <div class="contact-form-enhanced">
            <h2 class="contact-heading-form">Send Us a Message</h2>
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('contact.submit') }}" class="form">
                @csrf
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email address" required>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Message</label>
                    <div class="input-wrapper">
                        <i class="fas fa-comment"></i>
                        <textarea id="description" name="description" rows="5" placeholder="Type your message here" required>{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="submit-button-enhanced">
                    <i class="fas fa-paper-plane"></i>
                    Send Message
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .contact-container-enhanced {
        min-height: calc(100vh - 5rem);
        background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
        padding: 2.5rem 1rem;
        font-family: 'Segoe UI', 'Roboto', Arial, sans-serif;
    }
    .contact-wrapper-enhanced {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 24px 1fr;
        gap: 2.5rem;
        align-items: stretch;
    }
    .contact-info-enhanced {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px rgba(30,64,175,0.07);
        color: #1E3A8A;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .contact-heading {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 1.2rem;
        font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
        letter-spacing: 1px;
    }
    .contact-description-lg {
        font-size: 1.15rem;
        opacity: 0.92;
        margin-bottom: 2.2rem;
        color: #334155;
    }
    .info-cards-enhanced {
        display: grid;
        gap: 1.5rem;
    }
    .info-card-enhanced {
        background: rgba(30,64,175,0.07);
        padding: 1.5rem;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(30,64,175,0.04);
        transition: box-shadow 0.2s, transform 0.2s;
        text-align: left;
    }
    .info-card-enhanced:hover {
        box-shadow: 0 8px 24px rgba(30,64,175,0.13);
        transform: translateY(-4px) scale(1.03);
    }
    .info-card-enhanced h3 {
        font-size: 1.15rem;
        font-weight: 700;
        margin-bottom: 0.4rem;
        color: #1E40AF;
    }
    .info-card-enhanced p {
        opacity: 0.93;
        color: #334155;
    }
    .icon-wrapper-enhanced {
        width: 48px;
        height: 48px;
        background: #e0e7ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(30,64,175,0.07);
    }
    .icon-wrapper-enhanced i {
        font-size: 1.3rem;
        color: #1E40AF;
    }
    .contact-divider {
        display: block;
        width: 2px;
        background: linear-gradient(180deg, #c7d2fe 0%, #e0e7ff 100%);
        border-radius: 1px;
        margin: 2rem 0;
    }
    .contact-form-enhanced {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px rgba(30,64,175,0.07);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .contact-heading-form {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 1.5rem;
        color: #1E40AF;
        font-family: 'Poppins', 'Segoe UI', Arial, sans-serif;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #1E3A8A;
    }
    .input-wrapper {
        position: relative;
    }
    .input-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9CA3AF;
    }
    .input-wrapper input,
    .input-wrapper textarea {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #E5E7EB;
        border-radius: 0.7rem;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    .input-wrapper textarea {
        padding-left: 2.5rem;
        resize: vertical;
    }
    .input-wrapper input:focus,
    .input-wrapper textarea:focus {
        outline: none;
        border-color: #1E40AF;
        box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.09);
        background: #fff;
    }
    .error-message {
        color: #DC2626;
        font-size: 0.89rem;
        margin-top: 0.25rem;
    }
    .submit-button-enhanced {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #1E40AF 0%, #6366f1 100%);
        color: white;
        border: none;
        border-radius: 0.7rem;
        font-weight: 700;
        font-size: 1.08rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(.4,2,.3,1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        box-shadow: 0 2px 8px rgba(30,64,175,0.07);
    }
    .submit-button-enhanced:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 24px rgba(99,102,241,0.13);
        background: linear-gradient(135deg, #6366f1 0%, #1E40AF 100%);
    }
    .alert {
        padding: 1rem;
        border-radius: 0.7rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .alert-success {
        background-color: #D1FAE5;
        color: #065F46;
        border: 1px solid #6EE7B7;
    }
    @media (max-width: 1024px) {
        .contact-wrapper-enhanced {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        .contact-divider {
            display: none;
        }
    }
    @media (max-width: 600px) {
        .contact-info-enhanced, .contact-form-enhanced {
            padding: 1.2rem 0.7rem;
            border-radius: 1rem;
        }
        .contact-heading, .contact-heading-form {
            font-size: 1.3rem;
        }
    }
</style>
@endsection 