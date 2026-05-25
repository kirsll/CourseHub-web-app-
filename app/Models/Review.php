<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrollment_id',
        'rating',
        'comment',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_visible' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeLowRating($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function getRatingStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getFormattedRatingAttribute(): string
    {
        return $this->rating . '.0';
    }

    public function getRatingPercentageAttribute(): int
    {
        return ($this->rating / 5) * 100;
    }

    public function getIsPositiveAttribute(): bool
    {
        return $this->rating >= 4;
    }

    public function getIsNegativeAttribute(): bool
    {
        return $this->rating <= 2;
    }

    public function getIsNeutralAttribute(): bool
    {
        return $this->rating === 3;
    }

    public function getRatingLabelAttribute(): string
    {
        return match($this->rating) {
            5 => 'Отлично',
            4 => 'Хорошо',
            3 => 'Нормально',
            2 => 'Плохо',
            1 => 'Ужасно',
            default => 'Без оценки'
        };
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('d.m.Y H:i');
    }

    public function approve(): void
    {
        $this->is_visible = true;
        $this->save();
    }

    public function hide(): void
    {
        $this->is_visible = false;
        $this->save();
    }
}
