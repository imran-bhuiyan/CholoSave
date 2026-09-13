@extends('layouts.group_member')

@section('title', 'Verify OTP')

@push('styles')
<style>
    /* Add any custom styles for the OTP form if needed */
</style>
@endpush

@section('content')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded shadow-lg">
        <h2 class="text-2xl font-bold text-center">OTP Verification</h2>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('member.installment.payment.verify-otp.post', ['groupId' => $groupId, 'transactionId' => $transactionId]) }}" class="space-y-6">
            @csrf
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700">Enter OTP</label>
                <input type="text" name="otp" id="otp" required
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       maxlength="6" pattern="\d{6}">
                <p class="text-sm text-gray-500 mt-2">OTP sent to your registered email. Valid for 2 minutes.</p>
            </div>
            <button type="submit"
                    class="w-full px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Verify OTP
            </button>
        </form>

        <div class="text-center">
            {{-- You might want a way to resend OTP or cancel --}}
             <a href="{{ route('member.installment.payment.create', $groupId) }}" class="text-sm text-blue-600 hover:underline">
                Cancel Payment
            </a>
        </div>
    </div>
</div>
@endsection 