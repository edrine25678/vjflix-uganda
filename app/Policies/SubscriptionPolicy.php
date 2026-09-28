<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    /**
     * Determine if the user can view the subscription.
     */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id || $user->can('admin');
    }

    /**
     * Determine if the user can update the subscription.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id || $user->can('admin');
    }

    /**
     * Determine if the user can cancel the subscription.
     */
    public function cancel(User $user, Subscription $subscription): bool
    {
        return $user->id === $subscription->user_id && $subscription->isActive();
    }

    /**
     * Determine if the user can delete the subscription.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->can('admin');
    }
}
