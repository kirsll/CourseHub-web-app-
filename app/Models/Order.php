<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'discount_amount',
        'final_amount',
        'currency',
        'status',
        'payment_method',
        'payment_data',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'payment_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return number_format($this->total_amount, 2, '.', ' ') . ' ₽';
    }

    public function getFormattedFinalAmountAttribute(): string
    {
        return number_format($this->final_amount, 2, '.', ' ') . ' ₽';
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount, 2, '.', ' ') . ' ₽';
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_amount > 0;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->has_discount || $this->total_amount == 0) {
            return null;
        }

        return round(($this->discount_amount / $this->total_amount) * 100);
    }

    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Ожидает оплаты',
            'paid' => 'Оплачен',
            'cancelled' => 'Отменен',
            'refunded' => 'Возвращен',
            default => 'Неизвестен'
        };
    }

    public function markAsPaid(): void
    {
        $this->status = 'paid';
        $this->save();
    }

    public function markAsCancelled(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function markAsRefunded(): void
    {
        $this->status = 'refunded';
        $this->save();
    }
}
