<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'course_id',
        'amount',
        'commission',
        'instructor_earnings',
        'payment_gateway',
        'transaction_id',
        'status',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'instructor_earnings' => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsRefundedAttribute(): bool
    {
        return $this->status === 'refunded';
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, '.', ' ') . ' ₽';
    }

    public function getFormattedCommissionAttribute(): string
    {
        return number_format($this->commission, 2, '.', ' ') . ' ₽';
    }

    public function getFormattedInstructorEarningsAttribute(): string
    {
        return number_format($this->instructor_earnings, 2, '.', ' ') . ' ₽';
    }

    public function getCommissionPercentageAttribute(): float
    {
        if ($this->amount == 0) {
            return 0;
        }

        return ($this->commission / $this->amount) * 100;
    }

    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'pending' => 'В обработке',
            'completed' => 'Завершен',
            'failed' => 'Ошибка',
            'refunded' => 'Возвращен',
            default => 'Неизвестен'
        };
    }

    public function markAsCompleted(?string $transactionId = null): void
    {
        $this->status = 'completed';
        $this->paid_at = now();
        
        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }
        
        $this->save();
    }

    public function markAsFailed(): void
    {
        $this->status = 'failed';
        $this->save();
    }

    public function markAsRefunded(): void
    {
        $this->status = 'refunded';
        $this->save();
    }
}
