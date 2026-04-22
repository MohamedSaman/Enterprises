<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Production Staff' }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')

    <style>
        :root {
            --bg-main: #f8faff;
            --surface: #ffffff;
            --primary: #00a3e0;
            --primary-soft: #e0f5fe;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --nav-height: 70px;
        }

        body {
            margin: 0;
            background: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .top-nav {
            height: var(--nav-height);
            background: var(--surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand {
            font-size: 1.35rem;
            font-weight: 800;
            color: #1a202c;
            text-decoration: none;
            letter-spacing: -0.04em;
            display: flex;
            align-items: center;
        }

        .brand span {
            color: #64748b;
            font-weight: 500;
            margin-right: 4px;
        }

        .nav-center {
            display: flex;
            gap: 2rem;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }

        .nav-item {
            text-decoration: none;
            color: #4a5568 !important;
            font-weight: 700 !important;
            font-size: 0.9rem;
            padding: 0.5rem 0;
            position: relative;
            transition: all 0.2s;
        }

        .nav-item:hover {
            color: var(--primary) !important;
        }

        .nav-item.active {
            color: #1a202c !important;
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary);
            border-radius: 100px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            position: relative;
            z-index: 1101;
        }

        .notification-btn {
            background: none;
            border: none;
            color: #4a5568 !important;
            font-size: 1.4rem;
            position: relative;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .notification-dot {
            width: 10px;
            height: 10px;
            background: #ef4444;
            border: 2px solid #fff;
            border-radius: 50%;
            position: absolute;
            top: -2px;
            right: -2px;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 5px 12px;
            border-radius: 99px;
            transition: all 0.2s;
            color: #1a202c;
            cursor: pointer;
            border: 1px solid transparent !important;
            background: transparent !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .admin-info:hover {
            background-color: var(--primary-soft) !important;
            border-color: #e0f2fe !important;
        }

        .admin-info:focus,
        .admin-info:focus-visible,
        .admin-info:active {
            outline: none !important;
            box-shadow: none !important;
            border: 1px solid transparent !important;
        }

        .admin-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #1e293b;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .admin-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: #334155;
        }

        .dropdown-item {
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .dropdown-item:hover {
            background-color: #f8fafc;
            transform: translateX(3px);
        }

        .main-content {
            padding: 1.25rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        @media (max-width: 991px) {
            .nav-center {
                display: none;
            }

            .main-content {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <nav class="top-nav">
        <a href="{{ route('production.staff.dashboard') }}" class="brand">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" width="50" height="50">
            <span>{{ config('shop.name') }}</span>
        </a>

        <div class="nav-center">
            <a href="{{ route('production.staff.dashboard') }}" class="nav-item {{ request()->routeIs('production.staff.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('production.staff.batches') }}" class="nav-item {{ request()->routeIs(['production.staff.batches', 'production.staff.batch-details']) ? 'active' : '' }}">Production</a>
            <a href="{{ route('production.staff.settings') }}" class="nav-item {{ request()->routeIs('production.staff.settings') ? 'active' : '' }}">Settings</a>
        </div>

        <div class="nav-right">
            <button class="notification-btn">
                <i class="bi bi-bell-fill"></i>
                <span class="notification-dot"></span>
            </button>

            <div class="dropdown shadow-sm rounded-pill">
                <button type="button" class="admin-info dropdown-toggle text-decoration-none" id="adminDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @if(auth()->user()->profile_photo_path)
                    <img src="{{ route('profile.photo.show', auth()->id()) }}?v={{ md5((string) auth()->user()->profile_photo_path) }}" class="admin-avatar" alt="{{ auth()->user()->name }}" style="object-fit:cover;">
                    @else
                    <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    @endif
                    <div class="admin-name d-none d-lg-block ms-2">{{ auth()->user()->name }}</div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 mt-3" style="border-radius: 12px; min-width: 200px;" aria-labelledby="adminDropdown">
                    <li class="px-3 py-2 border-bottom mb-2 d-lg-none">
                        <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                        <small class="text-muted">Production Staff</small>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 px-3 rounded-2 fw-semibold" href="{{ route('production.staff.profile') }}">
                            <i class="bi bi-person me-2 text-primary"></i>My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 px-3 rounded-2 fw-semibold" href="{{ route('production.staff.settings') }}">
                            <i class="bi bi-gear me-2 text-secondary"></i>Settings
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider opacity-50">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="mb-0">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 px-3 rounded-2 fw-bold text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="main-content">
        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ensure dropdown toggle works on page load
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.getElementById('adminDropdown');
            if (dropdownToggle) {
                dropdownToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdown = new bootstrap.Dropdown(this);
                    dropdown.toggle();
                });
            }
        });
    </script>
    @stack('scripts')
    @livewireScripts
</body>

</html>