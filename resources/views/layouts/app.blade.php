<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CholoSave')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding-top: 5rem;
            background-color: #f4f7f9;
        }

        .header {
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            height: 5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo a {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-item {
            color: #4B5563;
            text-decoration: none;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-item::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #1E40AF;
            transition: width 0.3s ease;
        }

        .nav-item:hover {
            color: #1E40AF;
        }

        .nav-item:hover::after {
            width: 100%;
        }

        .btn-login, .btn-register {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 9999px;
            font-weight: 600;
            padding: 0.625rem 1.25rem;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
        }

        .btn-register {
            background-color: #16A34A;
            color: white;
        }

        .btn-register:hover {
            background-color: #15803D;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .menu-toggle span {
            width: 30px;
            height: 3px;
            background-color: #333;
            transition: all 0.3s ease;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            cursor: pointer;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            min-width: 200px;
            display: none;
            z-index: 1001;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #4B5563;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
            color: #1E40AF;
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            min-width: 180px;
            display: none;
            z-index: 1001;
            margin-top: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
        }

        .dropdown-wrapper {
            position: relative;
        }

        /* Remove automatic hover behavior - now handled by JavaScript */
        /* .dropdown-wrapper:hover .dropdown-content {
            display: block;
            opacity: 1;
            visibility: visible;
        } */

        /* Add a small invisible area to bridge the gap */
        .dropdown-wrapper::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            height: 10px;
            background: transparent;
        }

        .notification-icon {
            position: relative;
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #EF4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media screen and (max-width: 768px) {
            .nav {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 5rem;
                left: 0;
                right: 0;
                background-color: white;
                padding: 1rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                align-items: flex-start;
            }

            .nav.active {
                display: flex;
            }

            .menu-toggle {
                display: flex;
            }

            .nav-item::after {
                display: none;
            }

            .user-menu {
                flex-direction: column;
                align-items: flex-start;
                width: 100%;
            }

            .dropdown-menu {
                position: static;
                box-shadow: none;
                width: 100%;
            }
            
            .dropdown-content {
                position: static;
                box-shadow: none;
                width: 100%;
                margin-left: 1.5rem;
            }
            
            .dropdown-wrapper {
                width: 100%;
            }
            
            .dropdown-wrapper:hover .dropdown-content {
                display: none;
            }
            
            .dropdown-wrapper.active .dropdown-content {
                display: block;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container-custom">
            <div class="logo">
                <a href="/">CholoSave</a>
            </div>
            <div class="menu-toggle" id="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <nav class="nav" id="main-nav">
                @auth
                    <a class="nav-item" href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    
                    <div class="dropdown-wrapper" id="groups-dropdown">
                        <div class="nav-item dropdown-trigger" id="groups-trigger">
                            <i class="fas fa-users"></i> Groups
                            <i class="fas fa-chevron-down ml-1"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('groups.create') }}" class="dropdown-item">
                                <i class="fas fa-plus"></i> Create Group
                            </a>
                            <a href="{{ route('groups.my') }}" class="dropdown-item">
                                <i class="fas fa-users"></i> My Groups
                            </a>
                            <a href="{{ route('groups.join') }}" class="dropdown-item">
                                <i class="fas fa-user-plus"></i> Join Group
                            </a>
                        </div>
                    </div>
                    
                    <a class="nav-item" href="/ai-tips">
                        <i class="fas fa-robot"></i> AI Tips
                    </a>
                    
                    <div class="dropdown-wrapper" id="community-dropdown">
                        <div class="nav-item dropdown-trigger" id="community-trigger">
                            <i class="fas fa-users"></i> Community
                            <i class="fas fa-chevron-down ml-1"></i>
                        </div>
                        <div class="dropdown-content">
                            <a href="{{ route('groups.leaderboard') }}" class="dropdown-item">
                                <i class="fas fa-trophy"></i> Leaderboard
                            </a>
                            <a href="{{ route('forum.index') }}" class="dropdown-item">
                                <i class="fas fa-comments"></i> Forum
                            </a>
                        </div>
                    </div>

                    <div class="user-menu">
                        <a href="{{ route('reminders') }}" class="nav-item">
                            <i class="fas fa-money-bill"></i> Reminders
                        </a>
                        <div class="notification-icon" id="notification-icon">
                            <a href="{{ route('notifications') }}" class="text-decoration-none">
                                <i class="fas fa-bell nav-item"></i>
                                @if($unreadNotifications > 0)
                                    <span class="notification-badge">{{ $unreadNotifications }}</span>
                                @endif
                            </a>
                        </div>
                        
                        <div class="user-avatar" id="user-menu-toggle">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="dropdown-menu" id="user-dropdown">
                            <a href="{{ route('profile') }}" class="dropdown-item">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; border: none; background: none;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a class="nav-item" href="/">Home</a>
                    <a class="nav-item" href="/vision">Vision</a>
                    <a class="nav-item" href="/experts">Experts</a>
                    <a class="nav-item" href="/contact">Contact Us</a>
                    <a class="btn-login" href="{{ route('login') }}">Login</a>
                @endauth
            </nav>
        </div>
    </header>

    <div style="width:100vw;max-width:100vw;padding:0;margin:0;">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.getElementById('mobile-menu').addEventListener('click', function () {
            document.getElementById('main-nav').classList.toggle('active');
        });

        // User dropdown toggle
        const userAvatar = document.querySelector('.user-avatar');
        const userDropdown = document.getElementById('user-dropdown');

        if (userAvatar && userDropdown) {
            userAvatar.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            document.addEventListener('click', function() {
                userDropdown.classList.remove('show');
            });
        }
        
        // Mobile dropdown toggles
        const groupsTrigger = document.getElementById('groups-trigger');
        const communityTrigger = document.getElementById('community-trigger');
        
        if (groupsTrigger) {
            groupsTrigger.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    document.getElementById('groups-dropdown').classList.toggle('active');
                }
            });
        }
        
        if (communityTrigger) {
            communityTrigger.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    document.getElementById('community-dropdown').classList.toggle('active');
                }
            });
        }
        
        // Notification icon functionality
        const notificationIcon = document.getElementById('notification-icon');
        if (notificationIcon) {
            notificationIcon.addEventListener('click', function() {
                // Add notification panel functionality here
                console.log('Notifications clicked');
            });
        }

        // Desktop dropdown hover behavior
        const dropdownWrappers = document.querySelectorAll('.dropdown-wrapper');
        let hoverTimeout;

        dropdownWrappers.forEach(wrapper => {
            const dropdownContent = wrapper.querySelector('.dropdown-content');
            
            wrapper.addEventListener('mouseenter', function() {
                clearTimeout(hoverTimeout);
                dropdownContent.style.display = 'block';
                setTimeout(() => {
                    dropdownContent.style.opacity = '1';
                    dropdownContent.style.visibility = 'visible';
                }, 10);
            });

            wrapper.addEventListener('mouseleave', function() {
                hoverTimeout = setTimeout(() => {
                    dropdownContent.style.opacity = '0';
                    dropdownContent.style.visibility = 'hidden';
                    setTimeout(() => {
                        if (dropdownContent.style.opacity === '0') {
                            dropdownContent.style.display = 'none';
                        }
                    }, 200);
                }, 150); // Small delay before hiding
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>