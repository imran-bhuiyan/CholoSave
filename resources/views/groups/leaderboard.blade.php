@extends('layouts.app')

@section('title', 'Champions')

@section('content')
<div class="container py-5" style="min-height: 80vh;">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="text-center mb-2">
                <h1 class="fw-bold text-primary" style="font-size:2.5rem; letter-spacing:1px;">Champions</h1>
                <div class="text-muted mb-4" style="font-size:1.2rem;">Where Excellence Meets Investment</div>
            </div>
            <div class="bg-white rounded-4 shadow-sm p-3 mb-4 d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-3">
                <div class="flex-grow-1">
                    <input type="text" class="form-control form-control-lg rounded-3" placeholder="🔍 Search champions..." id="searchInput">
                </div>
                <div class="d-flex gap-2 mt-3 mt-md-0">
                    <button class="btn btn-primary d-flex align-items-center gap-2 fw-semibold" id="sortHighest"><i class="fas fa-trophy"></i> Highest</button>
                    <button class="btn btn-outline-dark d-flex align-items-center gap-2 fw-semibold" id="sortLowest"><i class="fas fa-sort-amount-down-alt"></i> Lowest</button>
                </div>
            </div>
            <div class="bg-white rounded-4 shadow-sm p-0 mb-4 overflow-hidden">
                <table class="table align-middle mb-0" id="leaderboardTable">
                    <thead class="bg-light">
                        <tr>
                            <th style="width:70px;">Rank</th>
                            <th>Group Name</th>
                            <th>Points</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $index => $group)
                            <tr>
                                <td class="fw-bold" style="font-size:1.1rem;">
                                    @if($index == 0)
                                        <span class="me-1" style="color:#F7B801;"><i class="fas fa-crown"></i></span>
                                        <span class="text-primary">{{ $index+1 }}</span>
                                    @elseif($index == 1)
                                        <span class="me-1" style="color:#A7A7AD;"><i class="fas fa-medal"></i></span>
                                        <span class="text-primary">{{ $index+1 }}</span>
                                    @elseif($index == 2)
                                        <span class="me-1" style="color:#C97B63;"><i class="fas fa-award"></i></span>
                                        <span class="text-primary">{{ $index+1 }}</span>
                                    @else
                                        {{ $index+1 }}
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $group->group_name }}</td>
                                <td>
                                    <span class="fw-bold" style="color:#5F3DC4; font-size:1.1rem;">{{ number_format($group->points, 0) }}</span>
                                </td>
                                <td class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $group->leaderboard && $group->leaderboard->updated_at ? $group->leaderboard->updated_at->format('M d, Y H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No groups found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-3 mt-4 mt-lg-0">
            <div class="bg-white rounded-4 shadow-sm p-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="me-2" style="color:#F7B801; font-size:1.5rem;"><i class="fas fa-star"></i></span>
                    <span class="fw-bold fs-5">Points System</span>
                </div>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:#F3F4F6;">
                        <span class="text-primary fs-4"><i class="fas fa-users"></i></span>
                        <div>
                            <div class="fw-semibold text-primary">Create Group</div>
                            <div class="small text-muted">+5 points</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:#F3F4F6;">
                        <span class="text-success fs-4"><i class="fas fa-money-bill-wave"></i></span>
                        <div>
                            <div class="fw-semibold text-success">Member Payment</div>
                            <div class="small text-muted">1% of payment amount</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:#F3F4F6;">
                        <span class="text-danger fs-4"><i class="fas fa-user-minus"></i></span>
                        <div>
                            <div class="fw-semibold text-danger">Member Leave</div>
                            <div class="small text-muted">-10 points</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:#F3F4F6;">
                        <span class="text-info fs-4"><i class="fas fa-user-plus"></i></span>
                        <div>
                            <div class="fw-semibold text-info">Member Join</div>
                            <div class="small text-muted">+5 points</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3" style="background:#F3F4F6;">
                        <span class="text-warning fs-4"><i class="fas fa-piggy-bank"></i></span>
                        <div>
                            <div class="fw-semibold text-warning">Emergency Fund</div>
                            <div class="small text-muted">1% of fund amount</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Search filter
    document.getElementById('searchInput').addEventListener('input', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#leaderboardTable tbody tr');
        rows.forEach(row => {
            let groupName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            row.style.display = groupName.includes(filter) ? '' : 'none';
        });
    });
    // Sort buttons
    document.getElementById('sortHighest').addEventListener('click', function() {
        sortTable(true);
    });
    document.getElementById('sortLowest').addEventListener('click', function() {
        sortTable(false);
    });
    function sortTable(desc) {
        let tbody = document.querySelector('#leaderboardTable tbody');
        let rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            let aPoints = parseInt(a.querySelector('td:nth-child(3)').innerText);
            let bPoints = parseInt(b.querySelector('td:nth-child(3)').innerText);
            return desc ? bPoints - aPoints : aPoints - bPoints;
        });
        rows.forEach(row => tbody.appendChild(row));
    }
</script>
@endsection 