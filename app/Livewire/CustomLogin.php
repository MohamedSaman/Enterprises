<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class CustomLogin extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $selectedModule = 'invontery';

    public function render()
    {
        return view('livewire.custom-login');
    }

    public function setModule($module)
    {
        $this->selectedModule = $module;
    }

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $this->email)->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            // Check if user has access to the selected module
            if (!$user->hasModuleAccess($this->selectedModule)) {
                $this->addError('email', "This user doesn't have access to the {$this->selectedModule} module.");
                $this->password = '';
                return;
            }

            // Check if Two-Factor Authentication is enabled for the user
            $hasTwoFactor = in_array(\Laravel\Fortify\Features::twoFactorAuthentication(), config('fortify.features', [])) &&
                !empty($user->two_factor_secret);

            if ($hasTwoFactor) {
                // Store required session variables for Fortify's two-factor challenge
                request()->session()->put([
                    'login.id' => $user->getKey(),
                    'login.remember' => $this->remember,
                    'login.module' => $this->selectedModule,
                ]);

                return redirect()->route('two-factor.login');
            }

            // Normal login if no 2FA
            Auth::login($user, $this->remember);
            session()->put('active_module', $this->selectedModule);
            session()->regenerate();

            // Redirect based on module and role
            if ($this->selectedModule === 'production') {
                if ($user->role === 'admin') {
                    return redirect()->route('production.admin.dashboard');
                } else if ($user->role === 'staff') {
                    return redirect()->route('production.staff.dashboard');
                }
            } else {
                // invontery module
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } else if ($user->role === 'staff') {
                    return redirect()->route('staff.dashboard');
                }
            }

            return redirect()->route('dashboard');
        }

        // When authentication fails, add an error to Livewire's error bag
        $this->addError('email', 'These credentials do not match our records.');
        // Clear the password field for security and UX
        $this->password = '';
    }

    /**
     * Clear validation/error for a property when it is updated.
     * This helps remove the red border / shaking animation as soon as user starts typing.
     */
}
