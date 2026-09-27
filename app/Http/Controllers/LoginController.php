<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback()
    {
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
                $username = $baseUsername . $counter++;
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

        return redirect('/movies')->with('success', 'Signed in successfully');
    }
}
