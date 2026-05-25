<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'order_id',
        'paid_amount',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
        'certificate_url',
    ];

    protected $casts = [
        'paid_amount' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    public function scopeInProgress($query)
    {
        return $query->whereNull('completed_at');
    }

    public function getIsCompletedAttribute(): bool
    {
        return !is_null($this->completed_at);
    }

    public function getCompletedLessonsCountAttribute(): int
    {
        return $this->lessonProgress()->where('is_completed', true)->count();
    }

    public function getTotalLessonsCountAttribute(): int
    {
        return $this->course->lessons()->where('lessons.is_published', true)->count();
    }

    public function getRemainingLessonsCountAttribute(): int
    {
        return $this->total_lessons_count - $this->completed_lessons_count;
    }

    public function getFormattedProgressAttribute(): string
    {
        return number_format((float) ($this->progress_percentage ?? 0), 1) . '%';
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return number_format((float) ($this->paid_amount ?? 0), 2, '.', ' ') . ' ₽';
    }

    public function updateProgress(): void
    {
        $totalLessons = $this->total_lessons_count;
        
        if ($totalLessons === 0) {
            return;
        }

        $completedLessons = $this->completed_lessons_count;
        $progress = ($completedLessons / $totalLessons) * 100;

        $this->progress_percentage = round($progress, 2);
        
        if ($progress >= 100 && !$this->is_completed) {
            $this->completed_at = now();
        }

        $this->save();
    }
}
