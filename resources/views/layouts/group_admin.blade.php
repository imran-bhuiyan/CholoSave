<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Group Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Sidebar Animation Styles */
        .sidebar-link {
            position: relative;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 0;
            background: rgba(16, 185, 129, 0.1);
            transition: width 0.3s ease;
            z-index: 0;
        }

        .sidebar-link:hover::before {
            width: 100%;
        }

        .sidebar-link i {
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .sidebar-link:hover i {
            transform: scale(1.1);
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .submenu.active {
            max-height: 500px;
            transition: max-height 0.5s ease-in;
        }

        .chevron {
            transition: transform 0.3s ease;
        }

        .chevron.active {
            transform: rotate(180deg);
        }

        /* Profile Section Animation */
        .profile-section {
            transition: background-color 0.3s ease;
        }

        .profile-section:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Active Link Styles */
        .sidebar-link.active {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }

        .sidebar-link.active i {
            color: #10B981;
        }
    </style>
    @stack('styles')
</head>
<body class="font-poppins overflow-x-hidden">
    <div class="fixed top-0 left-0 h-screen bg-gray-900 text-gray-300 w-64 transition-all duration-300 ease-in-out z-50">
        <!-- Profile Section -->
        <div class="profile-section p-4 border-b border-gray-700 flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center transform transition-transform duration-300 hover:scale-110">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-white">Test</span>
                <span class="text-xs text-gray-400">Group Admin</span>
            </div>
        </div>

        <!-- Navigation -->
        <div class="overflow-y-auto h-[calc(100vh-8rem)]">
            <ul class="flex flex-col px-3 py-2">
                <li>
                    <a href="{{ route('groups.admin.dashboard', $group->group_id) }}" class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-chart-line w-5 mr-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('groups.admin.notifications', $group->group_id) }}" class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-bell w-5 mr-2"></i>
                        <span>Activity Alert</span>
                    </a>
                </li>

                <!-- Financial Management Section -->
                <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Financial Management</div>
                
                <li>
                    <div class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="loansToggle">
                        <i class="fas fa-hand-holding-dollar w-5 mr-2"></i>
                        <span>Loans</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 ease-in-out" id="loansChevron"></i>
                    </div>
                    <div class="loans-submenu ml-8 hidden relative">
                        <a href="{{ route('admin.loan.request.create', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Request Loan</span>
                        </a>
                        <a href="{{ route('admin.loan.history', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>My Loans</span>
                        </a>
                        <a href="{{ route('admin.member.loans', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Member Loans</span>
                        </a>
                    </div>
                </li>

                <li>
                    <div class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="paymentsToggle">
                        <i class="fas fa-credit-card w-5 mr-2"></i>
                        <span>Payments</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 ease-in-out" id="paymentsChevron"></i>
                    </div>
                    <div class="payments-submenu ml-8 hidden relative">
                        <a href="{{ route('admin.installment.payment.create', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Make Payment</span>
                        </a>
                        <a href="{{ route('admin.payment.history', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>My Payment History</span>
                        </a>
                        <a href="{{ route('admin.member.payment', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Member Payment</span>
                        </a>
                    </div>
                </li>

                <li>
                    <div class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="investmentsToggle">
                        <i class="fas fa-piggy-bank w-5 mr-2"></i>
                        <span>Investments</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 ease-in-out" id="investmentsChevron"></i>
                    </div>
                    <div class="investments-submenu ml-8 hidden relative">
                        <a href="{{ route('admin.investment.create', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>New Investment</span>
                        </a>
                        <a href="{{ route('admin.investment.return.create', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Record Return</span>
                        </a>
                        <a href="{{ route('admin.investment.history', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Investment History</span>
                        </a>
                    </div>
                </li>

                <!-- Group Management Section -->
                <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Group Management</div>

                <li>
                    <a href="{{ route('groups.admin.members', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-users w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Members</span>
                    </a>
                </li>

                <li>
                    <div class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="leaveToggle">
                        <i class="fas fa-calendar-day w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Leave</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 ease-in-out" id="leaveChevron"></i>
                    </div>
                    <div class="leave-submenu ml-8 hidden relative">
                        <a href="{{ route('groups.admin.leave-request.form', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Request For Me</span>
                        </a>
                        <a href="{{ route('groups.admin.member-leave-requests', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Member Requests</span>
                        </a>
                    </div>
                </li>

                <li>
                    <div class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="withdrawToggle">
                        <i class="fas fa-wallet w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Withdrawal</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 ease-in-out" id="withdrawChevron"></i>
                    </div>
                    <div class="withdraw-submenu ml-8 hidden relative">
                        <a href="{{ route('admin.withdrawal.request.create', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Request For Me</span>
                        </a>
                        <a href="{{ route('admin.withdrawal.history', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>My Withdrawals</span>
                        </a>
                        <a href="{{ route('admin.withdrawals.index', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Member Requests</span>
                        </a>
                    </div>
                </li>

                <li>
                    <a href="{{ route('chat.index', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-comments w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Chats</span>
                    </a>
                </li>

                <li>
                    <div class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="pollsToggle">
                        <i class="fas fa-poll w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Polls</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 ease-in-out" id="pollsChevron"></i>
                    </div>
                    <div class="polls-submenu ml-8 hidden relative">
                        <a href="{{ route('admin.poll.create', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Create Poll</span>
                        </a>
                        <a href="{{ route('admin.poll.list', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>View Polls</span>
                        </a>
                    </div>
                </li>

                <li>
                    <a href="{{ route('groups.admin.join-requests', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-user-plus w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Join Request</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('groups.admin.settings', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-cogs w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Settings</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('member.report.generate', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fa-solid fa-file-lines w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Generate Report</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('home') }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-red-400 hover:bg-gray-800 hover:text-red-300 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt w-5 mr-2 transition-transform duration-200 ease-in-out text-red-400"></i>
                        <span>Exit</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8 min-h-screen transition-all duration-300 ease-in-out bg-gray-100">
        @yield('content')
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submenuToggles = ['loansToggle', 'paymentsToggle', 'investmentsToggle', 'leaveToggle', 'withdrawToggle', 'pollsToggle'];
            
            submenuToggles.forEach(toggleId => {
                const toggle = document.getElementById(toggleId);
                const submenu = toggle.nextElementSibling;
                const chevron = document.getElementById(toggleId.replace('Toggle', 'Chevron'));

                toggle.addEventListener('click', () => {
                    submenu.classList.toggle('hidden');
                    chevron.classList.toggle('rotate-180');
                });
            });
        });
    </script>
</body>
</html>
