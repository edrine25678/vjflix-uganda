<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;

class AirtelMoneyService extends AbstractPaymentService
{
    protected function loadConfig(): array
    {
        return [
            'client_id' => config('services.airtel_money.client_id'),
            'client_secret' => config('services.airtel_money.client_secret'),
            'environment' => config('services.airtel_money.environment', 'sandbox'),
            'callback_url' => config('services.airtel_money.callback_url'),
            'base_url' => config('services.airtel_money.environment') === 'production'
                ? 'https://www.airtel.com'
                : 'https://sandbox.airtel.com',
        ];
    }

    public function getProviderName(): string
    {
        return 'airtel';
    }

    public function validatePhoneNumber(string $phoneNumber): bool
    {
        // Airtel Uganda numbers: +2567XX or 07XX (10 digits starting with 07)
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Check if it's a valid Uganda number starting with 07 (10 digits) or +2567XX
        if (strlen($cleaned) === 10 && str_starts_with($cleaned, '07')) {
            return true;
        }

        if (strlen($cleaned) === 12 && str_starts_with($cleaned, '2567')) {
            return true;
        }

        return false;
    }

    public function initiatePayment(array $data): array
    {
        $this->logPayment('Initiating payment', $data);

        // Validate phone number
        if (! isset($data['phone_number']) || ! $this->validatePhoneNumber($data['phone_number'])) {
            return [
                'success' => false,
                'message' => 'Invalid Airtel phone number format',
                'error_code' => 'INVALID_PHONE',
            ];
        }

        // Generate transaction reference
        $transactionReference = $this->generateTransactionReference();

        // Create payment record
        $payment = $this->createPaymentRecord([
            'user_id' => $data['user_id'],
            'plan_id' => $data['plan_id'] ?? null,
            'subscription_id' => $data['subscription_id'] ?? null,
            'transaction_reference' => $transactionReference,
            'phone_number' => $data['phone_number'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'UGX',
            'metadata' => [
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
            ],
        ]);

        // In production, this would make an actual API call to Airtel Money
        // For now, we'll simulate the response
        if ($this->config['environment'] === 'sandbox' || empty($this->config['client_id'])) {
            $this->logPayment('Sandbox mode - simulating payment initiation', [
                'transaction_reference' => $transactionReference,
            ]);

            return [
                'success' => true,
                'message' => 'Payment initiated successfully',
                'transaction_reference' => $transactionReference,
                'payment_id' => $payment->id,
                'status' => 'pending',
                'instructions' => 'Please enter your Airtel Money PIN to complete the payment',
            ];
        }

        // Production API call (placeholder - requires actual Airtel Money API integration)
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->generateAuthToken(),
                'X-Reference-Id' => $transactionReference,
            ])->post($this->config['base_url'].'/ug/v1/payments', [
                'transaction' => $transactionReference,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'UGX',
                'phone' => $this->formatPhoneNumber($data['phone_number']),
                'customer' => [
                    'name' => $data['customer_name'] ?? '',
                    'email' => $data['customer_email'] ?? '',
                ],
                'callback' => $this->config['callback_url'],
            ]);

            if ($response->successful()) {
                $this->updatePaymentStatus($payment, 'pending', [
                    'external_reference' => $response->json('transaction_id'),
                ]);

                return [
                    'success' => true,
                    'message' => 'Payment initiated successfully',
                    'transaction_reference' => $transactionReference,
                    'payment_id' => $payment->id,
                    'status' => 'pending',
                ];
            }

            $this->updatePaymentStatus($payment, 'failed', [
                'failure_reason' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to initiate payment',
                'error_code' => 'API_ERROR',
                'details' => $response->body(),
            ];
        } catch (\Exception $e) {
            $this->logPaymentError('Payment initiation failed', [
                'error' => $e->getMessage(),
                'transaction_reference' => $transactionReference,
            ]);

            $this->updatePaymentStatus($payment, 'failed', [
                'failure_reason' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed',
                'error_code' => 'EXCEPTION',
                'details' => $e->getMessage(),
            ];
        }
    }

    public function checkPaymentStatus(string $transactionReference): array
    {
        $payment = Payment::where('transaction_reference', $transactionReference)->first();

        if (! $payment) {
            return [
                'success' => false,
                'message' => 'Payment not found',
                'error_code' => 'NOT_FOUND',
            ];
        }

        // In sandbox mode, return the current status from database
        if ($this->config['environment'] === 'sandbox' || empty($this->config['client_id'])) {
            return [
                'success' => true,
                'status' => $payment->status,
                'transaction_reference' => $payment->transaction_reference,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
            ];
        }

        // Production API call (placeholder)
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->generateAuthToken(),
            ])->get($this->config['base_url']."/ug/v1/payments/{$transactionReference}");

            if ($response->successful()) {
                $status = $response->json('status');
                $mappedStatus = match ($status) {
                    'SUCCESS', 'successful' => 'completed',
                    'FAILED', 'failed' => 'failed',
                    'PENDING', 'pending' => 'pending',
                    default => 'pending',
                };

                $this->updatePaymentStatus($payment, $mappedStatus);

                if ($mappedStatus === 'completed' && $payment->plan_id) {
                    $this->activateSubscription($payment);
                }

                return [
                    'success' => true,
                    'status' => $mappedStatus,
                    'transaction_reference' => $transactionReference,
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to check payment status',
                'error_code' => 'API_ERROR',
            ];
        } catch (\Exception $e) {
            $this->logPaymentError('Status check failed', [
                'error' => $e->getMessage(),
                'transaction_reference' => $transactionReference,
            ]);

            return [
                'success' => false,
                'message' => 'Status check failed',
                'error_code' => 'EXCEPTION',
            ];
        }
    }

    public function validateCallback(array $payload): bool
    {
        // In production, validate the callback signature
        // For now, return true in sandbox mode
        if ($this->config['environment'] === 'sandbox') {
            return true;
        }

        // Placeholder for signature validation
        return isset($payload['transaction']) && isset($payload['status']);
    }

    public function processCallback(array $payload): array
    {
        if (! $this->validateCallback($payload)) {
            return [
                'success' => false,
                'message' => 'Invalid callback',
            ];
        }

        $payment = Payment::where('transaction_reference', $payload['transaction'])->first();

        if (! $payment) {
            return [
                'success' => false,
                'message' => 'Payment not found',
            ];
        }

        $status = match ($payload['status']) {
            'SUCCESS', 'successful', 'completed' => 'completed',
            'FAILED', 'failed' => 'failed',
            'PENDING', 'pending' => 'pending',
            default => 'pending',
        };

        $this->updatePaymentStatus($payment, $status, [
            'metadata' => $payload['metadata'] ?? [],
        ]);

        if ($status === 'completed' && $payment->plan_id) {
            $this->activateSubscription($payment);
        }

        $this->logPayment('Callback processed', [
            'transaction_reference' => $payload['transaction'],
            'status' => $status,
        ]);

        return [
            'success' => true,
            'message' => 'Callback processed successfully',
        ];
    }

    protected function formatPhoneNumber(string $phoneNumber): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Convert 07XX to 2567XX
        if (strlen($cleaned) === 10 && str_starts_with($cleaned, '07')) {
            return '256'.substr($cleaned, 1);
        }

        return $cleaned;
    }

    protected function generateAuthToken(): string
    {
        // Placeholder for actual auth token generation
        // In production, this would call Airtel's auth endpoint
        return 'sandbox_auth_token';
    }
}
