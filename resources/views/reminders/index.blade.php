@extends('layouts.app')

@section('title', 'Reminders')

@section('content')
<div class="container py-5">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white text-center">
            <h1 class="h4 mb-0">Reminders</h1>
        </div>
        <div class="card-body">
            @foreach($loan_reminders as $reminder)
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-circle fa-lg me-3"></i>
                    <div>
                        <strong>Loan Due Reminder</strong><br>
                        <span class="small">Group: <b>{{ $reminder['group_name'] }}</b></span><br>
                        <span class="small">Return Date: <b>{{ $reminder['return_date'] }}</b></span><br>
                        <span class="small">Amount: <b>BDT {{ number_format($reminder['amount'], 2) }}</b></span>
                    </div>
                </div>
            @endforeach

            @foreach($payment_reminders as $reminder)
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-bell fa-lg me-3"></i>
                    <div>
                        <strong>Payment Reminder</strong><br>
                        <span class="small">Group: <b>{{ $reminder['group_name'] }}</b></span><br>
                        <span class="small">Payment Type: <b>{{ ucfirst($reminder['payment_type']) }}</b></span><br>
                        <span class="small">Next Payment Date: <b>{{ $reminder['next_payment_date'] }}</b></span><br>
                        <span class="small">Amount: <b>BDT {{ number_format($reminder['amount'], 2) }}</b></span>
                    </div>
                </div>
            @endforeach

            @if(count($loan_reminders) == 0 && count($payment_reminders) == 0)
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i> No reminders for today.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 