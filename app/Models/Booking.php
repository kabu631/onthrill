<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A booking request for a tour package. The team confirms it by phone, then the traveller pays the advance. */
#[Fillable([
    'reference', 'user_id', 'package_id', 'package_name', 'name', 'email', 'phone', 'nationality', 'travelling_from',
    'travel_date', 'adults', 'children', 'currency', 'price_per_person', 'total', 'advance', 'notes', 'status',
    'payment_status', 'payment_method', 'payment_reference', 'admin_notes',
])]
class Booking extends Model
{
    public const NATIONALITIES = [
        'indian' => 'Indian',
        'nepali' => 'Nepali',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'new' => 'New',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const PAYMENT_STATUSES = [
        'pending' => 'Not paid',
        'advance_paid' => 'Advance paid',
        'paid' => 'Paid in full',
        'refunded' => 'Refunded',
    ];

    public const PAYMENT_METHODS = [
        'upi' => 'UPI',
        'esewa' => 'eSewa',
        'khalti' => 'Khalti',
        'bank' => 'Bank transfer',
        'cash' => 'Cash',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'price_per_person' => 'decimal:2',
            'total' => 'decimal:2',
            'advance' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** OT-YYMMDD-XXXXX, unique and not guessable from the previous booking. */
    public static function nextReference(): string
    {
        do {
            $reference = 'OT-'.now()->format('ymd').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        } while (static::where('reference', $reference)->exists());

        return $reference;
    }

    /** Indian travellers pay in rupees by UPI; Nepali travellers in Nepali rupees by eSewa, Khalti or bank. */
    public static function currencyFor(string $nationality): string
    {
        return match ($nationality) {
            'nepali' => 'NPR',
            'indian' => 'INR',
            default => visitor_currency(),
        };
    }

    public function travellers(): int
    {
        return (int) $this->adults + (int) $this->children;
    }

    public function isPaid(): bool
    {
        return in_array($this->payment_status, ['advance_paid', 'paid'], true);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function money(float|int|string|null $amount): string
    {
        return money($amount, $this->currency);
    }
}
