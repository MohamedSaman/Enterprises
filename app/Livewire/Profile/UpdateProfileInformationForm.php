<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateProfileInformationForm extends Component
{
    use WithFileUploads;

    public $state = [];
    public $photo;
    public $verificationLinkSent = false;

    public function mount(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        /** @var \App\Models\User $user */

        $this->state = array_merge([
            'email' => $user->email,
        ], $user->toArray());
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater)
    {
        $this->resetErrorBag();

        $updater->update(
            Auth::user(),
            $this->photo
                ? array_merge($this->state, ['photo' => $this->photo])
                : $this->state
        );

        if (isset($this->photo)) {
            // Keep user on the current module profile page instead of redirecting to /user/profile.
            return redirect()->to(url()->previous());
        }

        $this->dispatch('saved');
        $this->dispatch('refresh-navigation-menu');
    }

    public function deleteProfilePhoto(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        /** @var \App\Models\User $user */
        $user->deleteProfilePhoto();

        $this->dispatch('refresh-navigation-menu');
    }

    public function sendEmailVerification(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        /** @var \App\Models\User $user */
        $user->sendEmailVerificationNotification();

        $this->verificationLinkSent = true;
    }

    public function getUserProperty()
    {
        return Auth::user();
    }

    public function render()
    {
        return view('profile.update-profile-information-form');
    }
}
