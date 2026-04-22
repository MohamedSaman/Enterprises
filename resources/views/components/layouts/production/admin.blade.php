<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ $title ?? 'Industrial Alchemist' }}</title>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

	@vite(['resources/css/app.css', 'resources/js/app.js'])
	@livewireStyles
	@stack('styles')

	<style>
		:root {
			--bg-main: linear-gradient(135deg, #f5f7fb 0%, #f0f4fa 100%);
			--surface: #ffffff;
			--primary: #0284c7;
			--primary-dark: #0369a1;
			--primary-soft: #eff6ff;
			--accent: #8a6114;
			--text-main: #0f172a;
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
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
			position: sticky;
			top: 0;
			z-index: 1000;
			border-bottom: 1px solid var(--border);
		}

		.brand {
			font-size: 1.35rem;
			font-weight: 900;
			color: var(--text-main);
			text-decoration: none;
			letter-spacing: -0.04em;
			display: flex;
			align-items: center;
		}

		.brand span {
			color: var(--primary);
			font-weight: 700;
			margin-right: 6px;
		}

		.nav-center {
			display: flex;
			gap: 2.5rem;
			position: absolute;
			left: 50%;
			transform: translateX(-50%);
			z-index: 1000;
		}

		.nav-item {
			text-decoration: none;
			color: var(--text-muted) !important;
			font-weight: 700 !important;
			font-size: 0.9rem;
			padding: 0.5rem 0;
			position: relative;
			transition: all 0.2s ease;
			letter-spacing: -0.01em;
		}

		.nav-item:hover {
			color: var(--primary) !important;
		}

		.nav-item.active {
			color: var(--text-main) !important;
		}

		.nav-item.active::after {
			content: '';
			position: absolute;
			bottom: -8px;
			left: 0;
			width: 100%;
			height: 4px;
			background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
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
			color: var(--text-muted) !important;
			font-size: 1.4rem;
			position: relative;
			padding: 0;
			display: flex;
			align-items: center;
			transition: all 0.2s ease;
		}

		.notification-btn:hover {
			color: var(--primary) !important;
		}

		.notification-dot {
			width: 10px;
			height: 10px;
			background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
			border: 2px solid #fff;
			border-radius: 50%;
			position: absolute;
			top: -2px;
			right: -2px;
		}

		.user-profile {
			display: flex;
			align-items: center;
			gap: 0.85rem;
			text-decoration: none;
		}

		.avatar img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}

		/* Admin Section Styles */
		.admin-info {
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 6px 14px;
			border-radius: 10px;
			transition: all 0.2s ease;
			color: var(--text-main);
			cursor: pointer;
			border: 1px solid transparent !important;
			background: transparent !important;
			outline: none !important;
			box-shadow: none !important;
		}

		.admin-info:hover {
			background-color: var(--primary-soft) !important;
			border-color: #bfdbfe !important;
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
			background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
			color: #ffffff;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 800;
			font-size: 0.9rem;
			border: 2px solid white;
			overflow: hidden;
			box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);
		}

		.admin-name {
			font-weight: 700;
			font-size: 0.9rem;
			color: var(--text-main);
		}

		.dropdown-item {
			transition: all 0.2s ease;
			font-size: 0.85rem;
			font-weight: 600;
		}

		.dropdown-item:hover {
			background-color: linear-gradient(90deg, var(--primary-soft) 0%, rgba(2, 132, 199, 0.05) 100%);
			color: var(--primary);
			transform: translateX(3px);
		}

		/* Dropdown Styles */
		.nav-dropdown {
			position: relative;
			display: inline-block;
			margin-top: 5px;
		}

		.dropdown-content {
			display: none;
			position: absolute;
			background-color: #ffffff;
			min-width: 200px;
			box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
			z-index: 1100;
			border-radius: 12px;
			padding: 0.6rem;
			top: 100%;
			left: 0;
			border: 1px solid var(--border);
		}

		.dropdown-content a {
			color: var(--text-muted);
			padding: 0.8rem 1.2rem;
			text-decoration: none;
			display: flex;
			align-items: center;
			gap: 0.6rem;
			font-size: 0.85rem;
			font-weight: 700;
			border-radius: 8px;
			transition: all 0.2s ease;
		}

		.dropdown-content a:hover {
			background: linear-gradient(90deg, var(--primary-soft) 0%, rgba(2, 132, 199, 0.05) 100%);
			color: var(--primary);
			padding-left: 1.4rem;
		}

		.nav-dropdown:hover .dropdown-content {
			display: block;
			animation: slideDown 0.2s ease;
		}

		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-8px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		/* Active state for dropdown parent */
		.nav-dropdown.active-parent .nav-item {
			color: var(--text-main) !important;
		}

		.nav-dropdown.active-parent .nav-item::after {
			content: '';
			position: absolute;
			bottom: -8px;
			left: 0;
			width: 100%;
			height: 4px;
			background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
			border-radius: 100px;
		}

		.main-content {
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
		<a href="{{ route('production.admin.dashboard') }}" class="brand">
			<img src="{{ asset('images/logo.png') }}" alt="Logo" width="50" height="50">
			<span>{{ config('shop.name') }}</span>
		</a>

		<div class="nav-center">
			<a href="{{ route('production.admin.dashboard') }}" class="nav-item {{ request()->routeIs('production.admin.dashboard') ? 'active' : '' }}">Dashboard</a>
			<div class="nav-dropdown {{ request()->routeIs(['production.admin.purchase-order', 'production.admin.grn', 'production.admin.suppliers']) ? 'active-parent' : '' }}">
				<a href="#" class="nav-item">Purchases <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem"></i></a>
				<div class="dropdown-content">
					<a href="{{ route('production.admin.purchase-order') }}">Purchase List</a>
					<a href="{{ route('production.admin.suppliers') }}">Supplier List</a>
					<a href="{{ route('production.admin.grn') }}">GRN</a>
				</div>
			</div>
			<div class="nav-dropdown {{ request()->routeIs(['production.admin.material-list', 'production.admin.batches', 'production.admin.batch-details', 'production.admin.audit', 'production.admin.expenses']) ? 'active-parent' : '' }}">
				<a href="#" class="nav-item">Production <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem"></i></a>
				<div class="dropdown-content">
					<a href="{{ route('production.admin.material-list') }}">Material List</a>
					<a href="{{ route('production.admin.batches') }}">Production Batch</a>
					<a href="{{ route('production.admin.audit') }}">Audit Transfer</a>
					<a href="{{ route('production.admin.expenses') }}">Expenses</a>
				</div>
			</div>
			<div class="nav-dropdown {{ request()->routeIs(['production.admin.salary', 'production.admin.monthly-salary', 'production.admin.staff']) ? 'active-parent' : '' }}">
				<a href="#" class="nav-item">Salary <i class="bi bi-chevron-down ms-1" style="font-size: 0.7rem"></i></a>
				<div class="dropdown-content">
					<a href="{{ route('production.admin.salary') }}">Batch Salary Report</a>
					<a href="{{ route('production.admin.monthly-salary') }}">Monthly Salary</a>
					<a href="{{ route('production.admin.staff') }}">Staff List</a>
				</div>
			</div>
			<a href="{{ route('production.admin.settings') }}" class="nav-item {{ request()->routeIs('production.admin.settings') ? 'active' : '' }}">Settings</a>
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
						<small class="text-muted">Production Admin</small>
					</li>
					<li>
						<a class="dropdown-item py-2 px-3 rounded-2 fw-semibold" href="{{ route('production.admin.profile') }}">
							<i class="bi bi-person me-2 text-primary"></i>My Profile
						</a>
					</li>
					<li>
						<a class="dropdown-item py-2 px-3 rounded-2 fw-semibold" href="{{ route('production.admin.settings') }}">
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