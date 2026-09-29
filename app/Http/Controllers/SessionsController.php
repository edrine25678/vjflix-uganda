<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

class SessionsController extends Controller
{
    /**
     * @return View|Factory
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * @return Redirector|RedirectResponse
     */
    public function store()
    {
        // Validate the request
        $attributes = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // attempt to authenticate and log in the user
        // based on the provided credentials
        if (auth()->attempt($attributes, request()->boolean('remember'))) {
            session()->regenerate();

            return redirect('/movies')->with('success', 'Welcome back to VJFlix Uganda.');
        }

        // auth filed
        throw ValidationException::withMessages([
            'email' => 'The provided credentials could not be verified.',
        ]);
    }

    /**
     * @return Redirector|RedirectResponse
     */
    public function destroy()
    {
        // ddd('log the use out');
        auth()->logout();

        return redirect('/')->with('success', 'you\'re out');
    }
}
