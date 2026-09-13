@extends('layouts.app')

@section('title', 'Join Groups')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);
            min-height: 100vh;
        }
        .group-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .group-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }
        .modal-overlay { backdrop-filter: blur(4px); }
        .primary-button {
            background-color: rgb(0, 42, 196);
            transition: background-color 0.3s ease;
        }
        .primary-button:hover { background-color: #333; }
        .secondary-button {
            background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%);
        }
        .secondary-button:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        }
        .filter-section {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        function showDetails(details) {
            console.log('Showing details:', details); // Debug log
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
                members: details.members
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

            // Show modal with animation
            modal.style.display = 'flex';
            modal.style.opacity = '0';
            document.body.style.overflow = 'hidden';
            
            // Fade in animation
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('detailsModal');
            
            // Fade out animation
            modal.style.opacity = '0';
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        function joinGroup(groupId) {
            console.log('Joining group:', groupId); // Debug log
            const button = event.target;
            const originalText = button.textContent;
            
            // Disable button and show loading state
            button.disabled = true;
            button.textContent = 'Processing...';
            button.classList.add('opacity-75');

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').content;

            fetch(`/groups/${groupId}/join`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status); // Debug log
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data); // Debug log
                if (data.success) {
                    // Update button to show pending state
                    button.textContent = 'Request Pending';
                    button.classList.remove('secondary-button', 'opacity-75');
                    button.classList.add('bg-yellow-100', 'text-yellow-700', 'cursor-not-allowed');
                    
                    // Show success message
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'fixed top-4 right-4 bg-green-100 text-green-700 px-4 py-2 rounded-lg shadow-lg z-50';
                    messageDiv.textContent = data.message || 'Join request sent successfully! Please wait for approval.';
                    document.body.appendChild(messageDiv);
                    
                    // Remove message after 3 seconds
                    setTimeout(() => {
                        messageDiv.remove();
                    }, 3000);

                    // Reload the page after 2 seconds to show updated status
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    throw new Error(data.message || 'Failed to join group');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Reset button state
                button.textContent = originalText;
                button.disabled = false;
                button.classList.remove('opacity-75');
                
                // Show error message
                const messageDiv = document.createElement('div');
                messageDiv.className = 'fixed top-4 right-4 bg-red-100 text-red-700 px-4 py-2 rounded-lg shadow-lg z-50';
                messageDiv.textContent = error.message || 'An error occurred. Please try again.';
                document.body.appendChild(messageDiv);
                
                // Remove message after 3 seconds
                setTimeout(() => {
                    messageDiv.remove();
                }, 3000);
            });
        }

        // Initialize event listeners when the document is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking outside
            document.getElementById('detailsModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Add escape key listener to close modal
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            // Filter functionality
            const searchInput = document.getElementById('search');
            const dpsTypeSelect = document.getElementById('dpsType');

            // Add event listeners
            searchInput.addEventListener('input', applyFilters);
            dpsTypeSelect.addEventListener('change', applyFilters);

            // Initial apply on page load
            applyFilters();
        });

        function applyFilters() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const dpsType = document.getElementById('dpsType').value;

            // Get all group cards
            const groupCards = document.querySelectorAll('.group-card');

            groupCards.forEach(card => {
                let show = true;

                // Group name filter
                const groupName = card.querySelector('h3').textContent.toLowerCase();
                if (!groupName.includes(searchTerm)) {
                    show = false;
                }

                // DPS type filter
                if (dpsType !== '') {
                    const cardDpsType = card.querySelector('.bg-blue-50').textContent.trim();
                    if (cardDpsType !== dpsType) {
                        show = false;
                    }
                }

                // Show/hide card with animation
                if (show) {
                    card.style.display = '';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.opacity = '1';
                    }, 10);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        }
    </script>

    <div class="container mx-auto px-4 py-8">
        {{-- Message Display --}}
        @if (session('message'))
            <div class="mb-6 p-4 rounded-lg
                {{ session('message_type') === 'success' ? 'bg-green-100 text-green-700 border border-green-200' :
                    (session('message_type') === 'warning' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' :
                        'bg-blue-100 text-blue-700 border border-blue-200') }}">
                {{ session('message') }}
            </div>
        @endif

        <!-- Header -->
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Available Groups</h1>

        <!-- filter and search -->
        <div class="filter-section">
            <div class="flex space-x-4">
                <!-- Search Input -->
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Groups</label>
                    <input type="text" id="search" name="search" placeholder="Search by group name..."
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>

                <!-- DPS Type Filter -->
                <div class="w-48">
                    <label for="dpsType" class="block text-sm font-medium text-gray-700 mb-2">DPS Type</label>
                    <select id="dpsType" name="dpsType"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Groups Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($groups as $group)
                <div class="group-card rounded-xl p-6 flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-gray-800">{{ $group->group_name }}</h3>
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-sm font-medium">
                            {{ $group->dps_type }}
                        </span>
                    </div>
                    <div class="space-y-3 mb-6">
                        <p class="text-gray-600">
                            <span class="font-medium text-gray-700">Installment:</span>
                            <span class="text-blue-600 font-medium">BDT {{ number_format($group->amount, 2) }}</span>
                        </p>
                        <p class="text-gray-600">
                            <span class="font-medium text-gray-700">Members:</span>
                            <span class="text-blue-600 font-medium">{{ $group->members }}</span>
                        </p>
                    </div>
                    <div class="flex space-x-3 mt-auto">
                        <button
                            type="button"
                            onclick="showDetails({!! htmlspecialchars(json_encode([
                                'groupName' => $group->group_name,
                                'adminName' => $group->admin->name,
                                'dpsType' => $group->dps_type,
                                'timePeriod' => $group->time_period,
                                'startDate' => $group->start_date,
                                'goalAmount' => $group->goal_amount,
                                'emergencyFund' => $group->emergency_fund,
                                'members' => $group->members
                            ]), ENT_QUOTES, 'UTF-8') !!})"
                            class="w-1/2 px-4 py-2.5 text-white rounded-lg primary-button"
                        >
                            View Details
                        </button>
                        @if ($group->membership_status === 'pending')
                            <button type="button" class="w-1/2 px-4 py-2.5 bg-yellow-100 text-yellow-700 rounded-lg cursor-not-allowed font-medium">
                                Request Pending
                            </button>
                        @else
                            <button 
                                type="button"
                                onclick="joinGroup({{ $group->group_id }})"
                                class="w-1/2 px-4 py-2.5 text-white rounded-lg secondary-button"
                            >
                                Join Group
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="bg-white rounded-xl p-8 shadow-sm">
                        <p class="text-gray-600 text-lg">No groups available to join at the moment.</p>
                        <p class="text-gray-500 mt-2">Check back later for new groups!</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Details Modal -->
        <div id="detailsModal"
            class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center modal-overlay z-50">
            <div class="bg-white rounded-xl p-8 max-w-md w-full mx-4 shadow-xl">
                <div class="flex justify-between items-start mb-6">
                    <h2 class="text-2xl font-bold text-gray-800" id="modalGroupName"></h2>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">
                            <span class="font-medium text-gray-700">Admin:</span>
                            <span id="modalAdmin" class="text-blue-600"></span>
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">
                            <span class="font-medium text-gray-700">DPS Type:</span>
                            <span id="modalDpsType" class="text-blue-600"></span>
                        </p>
                        <p class="text-gray-600 mt-2">
                            <span class="font-medium text-gray-700">Time Period:</span>
                            <span id="modalTimePeriod" class="text-blue-600"></span>
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">
                            <span class="font-medium text-gray-700">Start Date:</span>
                            <span id="modalStartDate" class="text-blue-600"></span>
                        </p>
                        <p class="text-gray-600 mt-2">
                            <span class="font-medium text-gray-700">Goal Amount:</span>
                            BDT <span id="modalGoalAmount" class="text-blue-600"></span>
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-600">
                            <span class="font-medium text-gray-700">Emergency Fund:</span>
                            BDT <span id="modalEmergencyFund" class="text-blue-600"></span>
                        </p>
                        <p class="text-gray-600 mt-2">
                            <span class="font-medium text-gray-700">Group Size:</span>
                            <span id="modalMembers" class="text-blue-600"></span>
                        </p>
                    </div>
                </div>
                <div class="mt-8">
                    <button onclick="closeModal()" class="w-full px-4 py-2.5 text-white rounded-lg primary-button">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection 