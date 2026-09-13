@extends('layouts.app')

@section('title', 'Dashboard - CholoSave')

@section('content')
<style>
    .dashboard-container {
        padding: 2rem;
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .dashboard-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .dashboard-header h1 {
        font-weight: 600;
        font-size: 2rem;
        color: #343a40;
    }
    .dashboard-header p {
        font-size: 1rem;
        color: #6c757d;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-info p {
        margin: 0;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .stat-info span {
        font-size: 1.75rem;
        font-weight: 700;
        color: #343a40;
    }
    .stat-info .savings { color: #28a745; }
    .stat-info .loans { color: #dc3545; }
    .stat-icon {
        font-size: 1.5rem;
        padding: 1rem;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .icon-groups { background-color: #e7f3ff; color: #007bff; }
    .icon-savings { background-color: #eaf6ec; color: #28a745; }
    .icon-loans { background-color: #fbebee; color: #dc3545; }

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 992px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }

    .content-card {
        background: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }
    .content-card h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #343a40;
    }

    .financial-summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    .financial-summary-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .financial-summary-item:first-child {
        padding-top: 0;
    }
    .summary-text {
        display: flex;
        align-items: center;
        color: #495057;
    }
    .summary-text .fa-arrow-down { color: #dc3545; }
    .summary-text .fa-arrow-up { color: #28a745; }
    .summary-text span {
        margin-left: 0.75rem;
    }
    .summary-amount {
        font-weight: 600;
        color: #343a40;
    }
    .quick-actions-list a {
        display: flex;
        align-items: center;
        padding: 1rem;
        margin: 0 -1rem;
        border-radius: 0.5rem;
        color: #495057;
        text-decoration: none;
        transition: background-color 0.2s;
    }
    .quick-actions-list a:hover {
        background-color: #f8f9fa;
    }
    .quick-actions-list i {
        width: 20px;
        margin-right: 1rem;
        color: #6c757d;
    }
    .quick-actions-list .fa-plus { color: #007bff; }
    .quick-actions-list .fa-trophy { color: #ffc107; }
    .quick-actions-list .fa-info-circle { color: #17a2b8; }
    .quick-actions-list .fa-comments { color: #6c757d; }
    
    .quick-actions-list .action-text {
        flex-grow: 1;
    }
    .quick-actions-list .fa-chevron-right {
        color: #ced4da;
    }
    .investment-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
    }
    .investment-item i {
        color: #007bff;
        margin-right: 1rem;
    }
</style>

<div class="dashboard-container">
    <header class="dashboard-header">
        <h1>Welcome Back, {{ Auth::user()->name }}</h1>
        <p>Monitor Your Portfolio Performance & Group Analytics</p>
    </header>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Groups</p>
                <span>{{ $joinedGroupsCount }}</span>
            </div>
            <div class="stat-icon icon-groups">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Total Savings</p>
                <span class="savings">BDT {{ number_format($totalSavings, 2) }}</span>
            </div>
            <div class="stat-icon icon-savings">
                <i class="fas fa-piggy-bank"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <p>Outstanding Loans</p>
                <span class="loans">BDT {{ number_format($totalLoans, 2) }}</span>
            </div>
            <div class="stat-icon icon-loans">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
        </div>
    </div>

    <div class="main-grid">
        <div class="main-column">
            <div class="content-card">
                <h2>Financial Summary</h2>
                <div class="financial-summary-list">
                    <div class="financial-summary-item">
                        <div class="summary-text">
                            <i class="fas fa-arrow-down"></i>
                            <span>Withdrawn Amount</span>
                        </div>
                        <span class="summary-amount">BDT {{ number_format($totalWithdrawals, 2) }}</span>
                    </div>
                    <div class="financial-summary-item">
                        <div class="summary-text">
                            <i class="fas fa-arrow-up"></i>
                            <span>Total Contributions</span>
                        </div>
                        <span class="summary-amount">BDT {{ number_format($totalSavings, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <h2>Group Investments</h2>
                @forelse ($groupInvestments as $investment)
                    <div class="investment-item">
                        <i class="fas fa-building-columns"></i>
                        <span>{{ $investment->investment_type }} - BDT {{ number_format($investment->amount, 2) }}</span>
                    </div>
                @empty
                    <p>No group investments yet.</p>
                @endforelse
            </div>
        </div>

        <div class="side-column">
            <div class="content-card">
                <h2>Quick Actions</h2>
                <div class="quick-actions-list">
                    <a href="{{ route('groups.join') }}">
                        <i class="fas fa-plus"></i>
                        <span class="action-text">Join Groups</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="{{ route('groups.leaderboard') }}">
                        <i class="fas fa-trophy"></i>
                        <span class="action-text">Leaderboard</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#">
                        <i class="fas fa-info-circle"></i>
                        <span class="action-text">View Details</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    <a href="#">
                        <i class="fas fa-comments"></i>
                        <span class="action-text">Forum</span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 