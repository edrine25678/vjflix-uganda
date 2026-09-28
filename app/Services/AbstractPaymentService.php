<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class AbstractPaymentService implements PaymentServiceInterface
{
    protected array $config = [];

    public function __construct()
    {
        $this->config = $this->loadConfig();
    }

    /**
     * Load provider-specific configuration.
     */
    abstract protected function loadConfig(): array;

    /**
     * Create a payment record.
     */
    protected function createPaymentRecord(array $data): Payment
    {
        return Payment::create([
            'user_id' => $data['user_id'],
            'plan_id' => $data['plan_id'] ?? null,
            'subscription_id' => $data['subscription_id'] ?? null,
            'transaction_reference' => $data['transaction_reference'],
            'external_reference' => $data['external_reference'] ?? null,
            'network' => $this->getProviderName(),
            'phone_number' => $data['phone_number'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'UGX',
            'status' => 'pending',
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    /**
     * Update payment status.
     */
    protected function updatePaymentStatus(Payment $payment, string $status, array $additionalData = []): Payment
    {
        $payment->status = $status;
        $payment->paid_at = $status === 'completed' ? now() : null;

        if (isset($additionalData['failure_reason'])) {
            $payment->failure_reason = $additionalData['failure_reason'];
        }

        if (isset($additionalData['external_reference'])) {
            $payment->external_reference = $additionalData['external_reference'];
        }

        if (isset($additionalData['metadata'])) {
            $payment->metadata = array_merge($payment->metadata ?? [], $additionalData['metadata']);
        }

        $payment->save();

        return $payment;
    }

    /**
     * Activate subscription after successful payment.
     */
    protected function activateSubscription(Payment $payment): Subscription
    {
        return DB::transaction(function () use ($payment) {
            $plan = $payment->plan;
            $user = $payment->user;

            // Check if user has an active subscription
            $subscription = $user->subscriptions()
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->first();

            if ($subscription) {
                // Extend existing subscription
                $subscription->expires_at = $subscription->expires_at->addDays($plan->duration_days);
                $subscription->plan_id = $plan->id;
                $subscription->payment_method = $this->getProviderName();
                $subscription->external_reference = $payment->external_reference;
                $subscription->save();
            } else {
                // Create new subscription
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'expires_at' => now()->addDays($plan->duration_days),
                    'auto_renew' => false,
                    'payment_method' => $this->getProviderName(),
                    'external_reference' => $payment->external_reference,
                ]);
            }

            // Link payment to subscription
            $payment->subscription_id = $subscription->id;
            $payment->save();

            return $subscription;
        });
    }

    /**
     * Generate a unique transaction reference.
     */
    protected function generateTransactionReference(): string
    {
        return 'VJF-'.strtoupper(uniqid()).'-'.time();
    }

    /**
     * Log payment activity.
     */
    protected function logPayment(string $message, array $context = []): void
    {
        Log::channel('payments')->info(
            "[{$this->getProviderName()}] {$message}",
            array_merge(['provider' => $this->getProviderName()], $context)
        );
    }

    /**
     * Log payment error.
     */
    protected function logPaymentError(string $message, array $context = []): void
    {
        Log::channel('payments')->error(
            "[{$this->getProviderName()}] {$message}",
            array_merge(['provider' => $this->getProviderName()], $context)
        );
    }
}
