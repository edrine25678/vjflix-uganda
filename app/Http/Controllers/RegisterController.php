<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * @return View|Factory
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * @return Redirector|RedirectResponse
     */
    public function store()
    {
        // return request()->all();

        // create the user
        $attributes = request()->validate([
            'name' => ['required', 'max:255'],
            'username' => ['required', 'min:3', 'max:255', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'min:7', 'max:255'],
        ]);

        $user = User::create($attributes);

        // log the user in
        auth()->login($user);

        // dd('success validation succeded');
        return redirect('/movies')->with('success', 'Your account has been created');
    }
}
