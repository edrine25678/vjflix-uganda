<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login')->with('info', 'Please login to access premium content');
        }

        // Check if user has an active subscription
        $activeSubscription = $user->subscriptions()->active()->first();

        if (! $activeSubscription) {
            return redirect()->route('subscriptions.index')
                ->with('warning', 'Premium content requires an active subscription');
        }

        // Add subscription to request for use in controllers
        $request->merge(['subscription' => $activeSubscription]);

        return $next($request);
    }
}
