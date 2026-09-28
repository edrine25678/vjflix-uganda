<?php

namespace App\Services;

interface PaymentServiceInterface
{
    /**
     * Initiate a payment request.
     */
    public function initiatePayment(array $data): array;

    /**
     * Check the status of a payment.
     */
    public function checkPaymentStatus(string $transactionReference): array;

    /**
     * Validate a payment callback/webhook.
     */
    public function validateCallback(array $payload): bool;

    /**
     * Process a payment callback/webhook.
     */
    public function processCallback(array $payload): array;

    /**
     * Get the payment provider name.
     */
    public function getProviderName(): string;

    /**
     * Validate phone number format for this provider.
     */
    public function validatePhoneNumber(string $phoneNumber): bool;
}
