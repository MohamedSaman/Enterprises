@php
$activeRole = $role ?? (auth()->user()->role === 'admin' ? 'admin' : 'staff');
$layout = $activeRole === 'admin' ? 'layouts.production.admin' : 'layouts.production.staff';
@endphp

<x-dynamic-component :component="$layout">
    <x-slot name="title">
        Profile
    </x-slot>

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold">My Profile</h4>
                <p class="text-muted mb-0">Manage your account information and security settings</p>
            </div>
        </div>

        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
        <div class="card mb-4 profile-card">
            <div class="card-body">
                @livewire('profile.update-profile-information-form')
            </div>
        </div>
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
        <div class="card mb-4 profile-card">
            <div class="card-body">
                @livewire('profile.update-password-form')
            </div>
        </div>
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <div class="card mb-4 profile-card">
            <div class="card-body">
                @livewire('profile.two-factor-authentication-form')
            </div>
        </div>
        @endif

        <div class="card mb-4 profile-card">
            <div class="card-body">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>
        </div>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
        <div class="card mb-4 profile-card">
            <div class="card-body">
                @livewire('profile.delete-user-form')
            </div>
        </div>
        @endif
    </div>

    @push('styles')
    <style>
        .profile-card {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            border: 1px solid #eef2f6;
        }

        .profile-card .card-body {
            padding: 2rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
        }
    </style>
    @endpush
</x-dynamic-component>