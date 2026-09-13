<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="font-poppins overflow-x-hidden">
    <!-- Sidebar -->
    <div class="fixed top-0 left-0 h-screen bg-gray-900 text-gray-300 w-64 transition-all duration-300 ease-in-out z-50">
        <!-- Profile Section -->
        <div class="p-4 border-b border-gray-700 flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-white text-xl"></i>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold text-white">{{ $group->group_name }}</span>
                <span class="text-xs text-gray-400">Group Member</span>
            </div>
        </div>

        <!-- Navigation -->
        <div class="overflow-y-auto h-[calc(100vh-8rem)]">
            <ul class="flex flex-col px-3 py-2">
                <li>
                    <a href="{{ route('groups.member.dashboard', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-chart-line w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.group.notifications', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-bell w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Activity Alert</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.loan.request.create', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-hand-holding-dollar w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Loan Request</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('chat.index', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-comments w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Chats</span>
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full" id="unreadCount" style="display: none;">0</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('groups.members', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-users w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Members</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.installment.payment.create', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-credit-card w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Payment</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('groups.member.leave-request.form', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-calendar-day w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Leave Request</span>
                    </a>
                </li>

                <!-- History Section -->
                <li>
                    <div class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="historyToggle">
                        <i class="fas fa-history w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>History</span>
                        <i class="fas fa-chevron-down ml-auto transition-transform duration-200 ease-in-out" id="historyChevron"></i>
                    </div>
                    <div class="history-submenu ml-8 hidden relative">
                        <a href="{{ route('member.loan.history', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Loan History</span>
                        </a>
                        <a href="{{ route('member.payment.history', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Payment History</span>
                        </a>
                        <a href="{{ route('member.withdrawal.history', $group->group_id) }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Withdrawal History</span>
                        </a>
                    </div>
                </li>

                <li>
                    <a href="{{ route('member.withdrawal.request.create', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-wallet w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Withdraw Request</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('groups.member.investment-details', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-piggy-bank w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
                        <span>Investment Details</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('member.report.generate', $group->group_id) }}" class="flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-file-lines w-5 mr-2 transition-transform duration-200 ease-in-out text-gray-400 hover:text-green-400"></i>
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

        <!-- Theme Toggle -->
        <!-- <div class="p-4 border-t border-gray-700">
            <button id="themeToggle" class="w-full py-2 rounded-md bg-transparent text-gray-300 border-none flex items-center justify-center gap-2 hover:bg-gray-800 transition-colors duration-200 focus:outline-none">
                <i class="fas fa-sun"></i>
                <span>Light Mode</span>
            </button>
        </div> -->
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8 min-h-screen transition-all duration-300 ease-in-out bg-gray-100" id="mainContent">
        @yield('content')
    </div>

    @stack('scripts')
    <script>
        // History toggle functionality (keep this if needed)
        const historyToggle = document.getElementById('historyToggle');
        const historySubmenu = historyToggle.nextElementSibling;
        const historyChevron = document.getElementById('historyChevron'); // Get by ID after updating HTML

        historyToggle.addEventListener('click', () => {
            historySubmenu.classList.toggle('hidden'); // Use Tailwind hidden class
            historyChevron.classList.toggle('rotate-180'); // Use Tailwind rotate class
        });
    </script>
</body>
</html> 