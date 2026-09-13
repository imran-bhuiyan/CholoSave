@extends('layouts.app')

@section('title', $group->group_name . ' - Group Details')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="mb-3">{{ $group->group_name }}</h2>
            <p><strong>Description:</strong> {{ $group->description }}</p>
            <p><strong>Number of Members:</strong> {{ $group->members }}</p>
            <p><strong>DPS Type:</strong> {{ ucfirst($group->dps_type) }}</p>
            <p><strong>Time Period:</strong> {{ $group->time_period }} months</p>
            <p><strong>Amount per Period:</strong> {{ $group->amount }}</p>
            <p><strong>Start Date:</strong> {{ $group->start_date->format('Y-m-d') }}</p>
            <p><strong>Goal Amount:</strong> {{ $group->goal_amount }}</p>
            <p><strong>Emergency Fund:</strong> {{ $group->emergency_fund }}</p>
            <p><strong>Payment Methods:</strong></p>
            <ul>
                <li><strong>bKash:</strong> {{ $group->bKash ?? 'N/A' }}</li>
                <li><strong>Rocket:</strong> {{ $group->Rocket ?? 'N/A' }}</li>
                <li><strong>Nagad:</strong> {{ $group->Nagad ?? 'N/A' }}</li>
            </ul>
            <p><strong>Admin User ID:</strong> {{ $group->group_admin_id }}</p>
        </div>
    </div>
</div>
@endsection 