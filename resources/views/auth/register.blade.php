@extends('layouts.app')

@section('title', 'Register - CholoSave')

@section('content')
<div class="login-bg-gradient">
    <div class="login-container">
        <div class="login-card">
            <div class="login-image">
                <img src="{{ asset('images/auth/register.png') }}" alt="Register">
                <h2>Join CholoSave Today!</h2>
                <p>Create your account and start your financial journey</p>
            </div>
            <div class="login-form">
                <div class="login-brand">
                    <!-- <img src="{{ asset('favicon.ico') }}" alt="CholoSave Logo" style="height:40px;width:40px;margin-bottom:0.5rem;"> -->
                    <!-- <span>CholoSave</span> -->
                </div>
                <h1 class="login-title">Register with <span>CholoSave</span></h1>
                @if($errors->any())
                    <div class="alert alert-error">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="Enter your full name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="Enter your email" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone_number">Phone Number</label>
                        <input type="tel" id="phone_number" name="phone_number" class="form-input" placeholder="Enter your phone number" value="{{ old('phone_number') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="Create your password" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" placeholder="Confirm your password" required>
                    </div>
                    <button type="submit" class="login-button">
                        Create Account
                    </button>
                </form>
                <div class="register-link">
                    Already have an account?
                    <a href="{{ route('login') }}">Login here</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .login-bg-gradient {
        min-height: calc(100vh - 5rem);
        background: linear-gradient(135deg, #f0f4f8 0%, #e0e7ef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    .login-container {
        width: 100%;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }
    .login-card {
        max-width: 950px;
        width: 100%;
        margin: 0 auto;
        background: #fff;
        border-radius: 1.5rem;
        box-shadow: 0 8px 32px rgba(30, 64, 175, 0.10), 0 1.5px 0 #fff;
        overflow: hidden;
        display: flex;
        transition: box-shadow 0.3s;
        position: relative;
    }
    .login-card:hover {
        box-shadow: 0 12px 40px rgba(30, 64, 175, 0.18), 0 1.5px 0 #fff;
    }
    .login-image {
        width: 48%;
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        padding: 2.5rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
    }
    .login-image img {
        max-width: 80%;
        height: auto;
        margin-bottom: 2rem;
        border-radius: 1rem;
        box-shadow: 0 4px 24px rgba(30, 64, 175, 0.10);
    }
    .login-form {
        width: 52%;
        padding: 3rem 2.5rem 2.5rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .login-brand {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-bottom: 1.2rem;
    }
    .login-brand span {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e40af;
        letter-spacing: 1px;
    }
    .login-title {
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 2rem;
    }
    .login-title span {
        background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        font-size: 0.95rem;
        font-weight: 500;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }
    .form-input {
        width: 100%;
        padding: 0.85rem 1.1rem;
        border: 1px solid #E5E7EB;
        border-radius: 0.7rem;
        font-family: 'Poppins', sans-serif;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.10);
        background: #fff;
    }
    .login-button {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        color: white;
        border: none;
        border-radius: 999px;
        font-weight: 700;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.10);
        margin-top: 0.5rem;
    }
    .login-button:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 8px 32px rgba(59, 130, 246, 0.18);
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
    }
    .register-link {
        text-align: center;
        margin-top: 2rem;
        color: #64748b;
        font-size: 0.97rem;
    }
    .register-link a {
        color: #1e40af;
        text-decoration: none;
        font-weight: 600;
        margin-left: 0.2rem;
    }
    .register-link a:hover {
        text-decoration: underline;
        color: #3b82f6;
    }
    .alert {
        padding: 1rem;
        border-radius: 0.7rem;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }
    .alert-error {
        background-color: #FEE2E2;
        color: #991B1B;
        border: 1px solid #FCA5A5;
    }
    .alert-success {
        background-color: #D1FAE5;
        color: #065F46;
        border: 1px solid #6EE7B7;
    }
    @media (max-width: 900px) {
        .login-card {
            flex-direction: column;
            max-width: 98vw;
        }
        .login-image, .login-form {
            width: 100%;
        }
        .login-image {
            padding: 2rem 1rem;
            border-radius: 1.5rem 1.5rem 0 0;
        }
        .login-form {
            padding: 2rem 1.5rem 2.5rem 1.5rem;
            border-radius: 0 0 1.5rem 1.5rem;
        }
    }
    @media (max-width: 600px) {
        .login-card {
            box-shadow: 0 2px 8px rgba(30, 64, 175, 0.10);
        }
        .login-form {
            padding: 1.2rem 0.5rem 2rem 0.5rem;
        }
        .login-title {
            font-size: 1.3rem;
        }
    }
</style>
@endsection