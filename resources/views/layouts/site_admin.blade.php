<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Site Admin - CholoSave')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
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
        <!-- Sidebar Navigation -->
        <div class="overflow-y-auto h-full flex flex-col">
            <div class="h-20 flex items-center justify-center border-b border-gray-700">
                <span class="text-2xl font-bold text-indigo-400">CholoSave Admin</span>
            </div>
            <ul class="flex flex-col px-3 py-2 flex-1">
                <li>
                    <a href="{{ route('site_admin.dashboard') }}" class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                        <i class="fas fa-chart-line w-5 mr-2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Expert Team</div>
                <li>
                    <div class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="expertTeamToggle">
                        <i class="fas fa-user-tie w-5 mr-2"></i>
                        <span>Expert Team</span>
                        <i class="fas fa-chevron-down ml-auto chevron" id="expertTeamChevron"></i>
                    </div>
                    <div class="submenu ml-8" id="expertTeamSubmenu">
                        <a href="{{ route('admin.expert.create') }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Add Expert</span>
                        </a>
                        <a href="{{ route('admin.expert.manage') }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Manage Experts</span>
                        </a>
                    </div>
                </li>
                <div class="px-4 py-2 text-xs text-gray-400 uppercase tracking-wider">Campaign</div>
                <li>
                    <div class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 cursor-pointer" id="campaignToggle">
                        <i class="fas fa-bullhorn w-5 mr-2"></i>
                        <span>Campaign</span>
                        <i class="fas fa-chevron-down ml-auto chevron" id="campaignChevron"></i>
                    </div>
                    <div class="submenu ml-8" id="campaignSubmenu">
                        <a href="{{ route('admin.campaign.create') }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Create Campaign</span>
                        </a>
                        <a href="{{ route('admin.campaign.manage') }}" class="flex items-center px-4 py-2 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200">
                            <span>Manage Campaigns</span>
                        </a>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.report') }}" class="sidebar-link flex items-center px-4 py-3 my-0.5 rounded-md text-gray-300 hover:bg-gray-800 hover:text-green-400 transition-colors duration-200 @if(request()->routeIs('admin.report')) active @endif">
                        <i class="fas fa-file-alt w-5 mr-2"></i>
                        <span>Report</span>
                    </a>
                </li>
            </ul>
            <div class="p-4 border-t border-gray-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    <main class="ml-64 min-h-screen bg-gray-100 p-8">
        @yield('content')
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('expertTeamToggle');
            const submenu = document.getElementById('expertTeamSubmenu');
            const chevron = document.getElementById('expertTeamChevron');
            if (toggle && submenu && chevron) {
                toggle.addEventListener('click', function() {
                    submenu.classList.toggle('active');
                    chevron.classList.toggle('active');
                });
            }
            const campaignToggle = document.getElementById('campaignToggle');
            const campaignSubmenu = document.getElementById('campaignSubmenu');
            const campaignChevron = document.getElementById('campaignChevron');
            if (campaignToggle && campaignSubmenu && campaignChevron) {
                campaignToggle.addEventListener('click', function() {
                    campaignSubmenu.classList.toggle('active');
                    campaignChevron.classList.toggle('active');
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html> 