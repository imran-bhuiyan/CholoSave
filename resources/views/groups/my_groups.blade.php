@extends('layouts.app')

@section('title', 'My Groups')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 text-center fw-bold">My Groups</h2>
    <div class="card p-4 mb-4" style="border-radius: 1rem;">
        <div class="row g-3 align-items-center">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Search Groups</label>
                <input type="text" id="groupSearch" class="form-control" placeholder="Search by group name...">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">DPS Type</label>
                <select id="dpsTypeFilter" class="form-select">
                    <option value="">All Types</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="groupsList">
        @forelse($memberships as $membership)
            @php $group = $membership->group; @endphp
            <div class="col-md-6 col-lg-4 mb-4 group-card"
                data-group-name="{{ strtolower($group->group_name) }}"
                data-dps-type="{{ strtolower($group->dps_type) }}">
                <div class="card shadow-sm h-100 modern-group-card">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="card-title mb-0">{{ $group->group_name }}</h4>
                            <span class="badge bg-light text-primary text-capitalize" style="font-size:0.95rem; font-weight:400;">{{ $group->dps_type }}</span>
                        </div>
                        <div class="group-info-list mb-3">
                            <div><span class="info-label">Installment:</span> <span>BDT {{ number_format($group->amount, 2) }}</span></div>
                            <div><span class="info-label">Members:</span> <span>{{ $group->members }}</span></div>
                            <div><span class="info-label">Role:</span> <span>{{ $membership->is_admin ? 'Admin' : 'Member' }}</span></div>
                        </div>
                        <div class="d-flex gap-2 mt-auto">
                            <button onclick="showDetails({!! htmlspecialchars(json_encode([
                                'groupName' => $group->group_name,
                                'adminName' => $group->admin->name,
                                'dpsType' => $group->dps_type,
                                'timePeriod' => $group->time_period,
                                'startDate' => $group->start_date,
                                'goalAmount' => $group->goal_amount,
                                'emergencyFund' => $group->emergency_fund,
                                'members' => $group->members,
                                'amount' => $group->amount,
                                'role' => $membership->is_admin ? 'Admin' : 'Member'
                            ]), ENT_QUOTES, 'UTF-8') !!})" class="btn btn-primary flex-fill">View Details</button>
                            <a href="{{ route('groups.enter', $group->group_id) }}" class="btn btn-success flex-fill">Enter Group</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">You have not joined any groups yet.</div>
            </div>
        @endforelse
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="modalGroupName"></h5>
                <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <p class="mb-2">
                            <span class="fw-semibold">Admin:</span>
                            <span id="modalAdmin" class="text-primary"></span>
                        </p>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <p class="mb-2">
                            <span class="fw-semibold">DPS Type:</span>
                            <span id="modalDpsType" class="text-primary"></span>
                        </p>
                        <p class="mb-0">
                            <span class="fw-semibold">Time Period:</span>
                            <span id="modalTimePeriod" class="text-primary"></span>
                        </p>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <p class="mb-2">
                            <span class="fw-semibold">Start Date:</span>
                            <span id="modalStartDate" class="text-primary"></span>
                        </p>
                        <p class="mb-0">
                            <span class="fw-semibold">Goal Amount:</span>
                            <span id="modalGoalAmount" class="text-primary"></span>
                        </p>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <p class="mb-2">
                            <span class="fw-semibold">Emergency Fund:</span>
                            <span id="modalEmergencyFund" class="text-primary"></span>
                        </p>
                        <p class="mb-0">
                            <span class="fw-semibold">Group Size:</span>
                            <span id="modalMembers" class="text-primary"></span>
                        </p>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="p-3 bg-light rounded">
                        <p class="mb-2">
                            <span class="fw-semibold">Installment Amount:</span>
                            <span id="modalAmount" class="text-primary"></span>
                        </p>
                        <p class="mb-0">
                            <span class="fw-semibold">Your Role:</span>
                            <span id="modalRole" class="text-primary"></span>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
.modern-group-card {
    border-radius: 1.25rem;
    border: none;
    background: #fff;
    transition: box-shadow 0.25s, transform 0.2s;
    box-shadow: 0 6px 24px rgba(30,64,175,0.10);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.card-title {
    color: #1E40AF;
    font-weight: 700;
    font-size: 1.3rem;
    letter-spacing: 0.5px;
}
.card-desc {
    color: #64748b;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}
.group-info-list {
    font-size: 0.98rem;
    color: #334155;
    margin-bottom: 1.2rem;
}
.group-info-list .info-label {
    font-weight: 600;
    color: #1E40AF;
    margin-right: 0.5rem;
}
.btn-primary {
    background: #0026ff;
    border: none;
    border-radius: 0.7rem;
    font-weight: 700;
    color: #fff;
    padding: 0.7rem 1.5rem;
    font-size: 1.08rem;
    box-shadow: 0 2px 8px rgba(30,64,175,0.10);
    transition: background 0.2s, transform 0.15s;
    letter-spacing: 0.5px;
}
.btn-primary:hover {
    background: #001bb5;
    color: #fff;
    transform: translateY(-2px) scale(1.04);
}
.btn-success {
    background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%);
    border: none;
    border-radius: 0.7rem;
    font-weight: 700;
    color: #fff;
    padding: 0.7rem 1.5rem;
    font-size: 1.08rem;
    box-shadow: 0 2px 8px rgba(30,64,175,0.10);
    transition: background 0.2s, transform 0.15s;
    letter-spacing: 0.5px;
}
.btn-success:hover {
    background: linear-gradient(135deg, #16A34A 0%, #22C55E 100%);
    color: #fff;
    transform: translateY(-2px) scale(1.04);
}
.modern-group-card:hover {
    box-shadow: 0 12px 32px rgba(30,64,175,0.18);
    transform: translateY(-4px) scale(1.025);
}
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1050;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.modal.show {
    display: flex;
    opacity: 1;
}
.modal-dialog {
    transform: translateY(-20px);
    transition: transform 0.3s ease;
}
.modal.show .modal-dialog {
    transform: translateY(0);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('groupSearch');
    const dpsTypeFilter = document.getElementById('dpsTypeFilter');
    const groupCards = document.querySelectorAll('.group-card');

    function filterGroups() {
        const search = searchInput.value.trim().toLowerCase();
        const dpsType = dpsTypeFilter.value;

        groupCards.forEach(card => {
            const name = card.getAttribute('data-group-name');
            const type = card.getAttribute('data-dps-type');
            const matchesName = name.includes(search);
            const matchesType = !dpsType || type === dpsType;
            card.style.display = (matchesName && matchesType) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterGroups);
    dpsTypeFilter.addEventListener('change', filterGroups);
});

function showDetails(details) {
    console.log('Showing details:', details);
    const modal = document.getElementById('detailsModal');
    
    // Format the data for display
    const formattedDetails = {
        groupName: details.groupName,
        adminName: details.adminName,
        dpsType: details.dpsType.charAt(0).toUpperCase() + details.dpsType.slice(1),
        timePeriod: `${details.timePeriod} ${details.dpsType}`,
        startDate: new Date(details.startDate).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }),
        goalAmount: new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'BDT',
            minimumFractionDigits: 2
        }).format(details.goalAmount),
        emergencyFund: new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'BDT',
            minimumFractionDigits: 2
        }).format(details.emergencyFund),
        members: details.members,
        amount: new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'BDT',
            minimumFractionDigits: 2
        }).format(details.amount),
        role: details.role
    };

    // Update modal content
    document.getElementById('modalGroupName').textContent = formattedDetails.groupName;
    document.getElementById('modalAdmin').textContent = formattedDetails.adminName;
    document.getElementById('modalDpsType').textContent = formattedDetails.dpsType;
    document.getElementById('modalTimePeriod').textContent = formattedDetails.timePeriod;
    document.getElementById('modalStartDate').textContent = formattedDetails.startDate;
    document.getElementById('modalGoalAmount').textContent = formattedDetails.goalAmount;
    document.getElementById('modalEmergencyFund').textContent = formattedDetails.emergencyFund;
    document.getElementById('modalMembers').textContent = formattedDetails.members;
    document.getElementById('modalAmount').textContent = formattedDetails.amount;
    document.getElementById('modalRole').textContent = formattedDetails.role;

    // Show modal with animation
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Trigger reflow
    modal.offsetHeight;
    
    // Add show class for animation
    modal.classList.add('show');
}

function closeModal() {
    const modal = document.getElementById('detailsModal');
    
    // Remove show class for fade out
    modal.classList.remove('show');
    
    // Wait for animation to complete
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }, 300);
}

// Close modal when clicking outside
document.getElementById('detailsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Close modal with escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
