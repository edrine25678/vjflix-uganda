<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider()
    {
        if (empty(config('services.google.client_id'))) {
            return redirect()->route('login')->with('error', 'Google OAuth client ID is not configured yet. Please add GOOGLE_CLIENT_ID to your .env file.');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Could not initiate Google login: ' . $e->getMessage());
        }
    }

    /**
     * @return Redirector|RedirectResponse
     */
    public function handleProviderCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('provider_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                $user->update([
                    'provider_id' => $googleUser->getId(),
                    'name' => $user->name ?: $googleUser->getName(),
                ]);
            } else {
                $username = Str::slug($googleUser->getName()) ?: 'user';
                $baseUsername = $username;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername.$counter++;
                }

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'username' => $username,
                    'email' => $googleUser->getEmail(),
                    'provider_id' => $googleUser->getId(),
                    'role' => 'user',
                ]);
            }

            auth()->login($user);

            return redirect('/movies')->with('success', 'Signed in successfully with Google!');
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}
