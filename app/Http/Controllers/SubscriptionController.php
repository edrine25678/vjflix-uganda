<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeSubscription = $user->subscriptions()->active()->first();
        $plans = Plan::active()->get();

        return view('subscriptions.index', compact('activeSubscription', 'plans'));
    }

    public function show(Subscription $subscription)
    {
        $this->authorize('view', $subscription);

        return view('subscriptions.show', compact('subscription'));
    }

    public function plan(Plan $plan)
    {
        $user = Auth::user();
        $activeSubscription = $user->subscriptions()->active()->first();

        return view('subscriptions.plan', compact('plan', 'activeSubscription'));
    }

    public function cancel(Request $request, Subscription $subscription)
    {
        $this->authorize('cancel', $subscription);

        $subscription->update([
            'auto_renew' => false,
            'status' => 'cancelled',
        ]);

        return redirect()->route('subscriptions.index')
            ->with('success', 'Subscription cancelled successfully. You will have access until '.$subscription->expires_at->format('F j, Y'));
    }

    public function history()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('subscriptions.history', compact('subscriptions'));
    }
}
