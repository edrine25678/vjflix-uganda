<?php

namespace App\Http\Controllers;

use App\Services\Newsletter;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    /**
     * @return Redirector|RedirectResponse
     */
    public function __invoke(Newsletter $newsletter)
    {
        request()->validate(['email' => 'required|email']);

        try {
            $newsletter->subscribe(request('email'));
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'email' => 'THis email could not be added to our newsletter.',
            ]);
        }

        return redirect('/')->with('success', 'You are now signed up to our newsletter!');
    }
}
