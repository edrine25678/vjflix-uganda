<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'subscription_id',
        'transaction_reference',
        'external_reference',
        'network',
        'phone_number',
        'amount',
        'currency',
        'status',
        'failure_reason',
        'metadata',
        'paid_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function formattedAmount(): string
    {
        return ($this->currency ?? 'UGX').' '.number_format($this->amount);
    }

    public function networkLabel(): string
    {
        return match (strtolower($this->network)) {
            'mtn' => 'MTN Mobile Money',
            'airtel' => 'Airtel Money',
            'card' => 'Credit/Debit Card',
            'voucher' => 'Prepaid Voucher',
            default => strtoupper($this->network),
        };
    }

    public function networkColor(): string
    {
        return match (strtolower($this->network)) {
            'mtn' => 'amber',
            'airtel' => 'rose',
            default => 'neutral',
        };
    }
}
