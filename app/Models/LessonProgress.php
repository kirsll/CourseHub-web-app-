<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'enrollment_id',
        'is_completed',
        'watch_time_seconds',
        'started_at',
        'completed_at',
        'last_accessed_at',
        'completion_percentage',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'watch_time_seconds' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completion_percentage' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    public function getIsStartedAttribute(): bool
    {
        return !is_null($this->started_at);
    }

    public function getFormattedWatchTimeAttribute(): string
    {
        $seconds = $this->watch_time_seconds ?? 0;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    public function getFormattedCompletionPercentageAttribute(): string
    {
        return number_format((float) ($this->completion_percentage ?? 0), 1) . '%';
    }

    public function getRemainingTimeAttribute(): int
    {
        $totalSeconds = ($this->lesson->duration_minutes ?? 0) * 60;
        return max(0, $totalSeconds - ($this->watch_time_seconds ?? 0));
    }

    public function getFormattedRemainingTimeAttribute(): string
    {
        $seconds = $this->remaining_time ?? 0;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    public function markAsStarted(): void
    {
        if (!$this->is_started) {
            $this->started_at = now();
            $this->last_accessed_at = now();
            $this->save();
        }
    }

    public function markAsCompleted(): void
    {
        if (!$this->is_completed) {
            $this->is_completed = true;
            $this->completed_at = now();
            $this->completion_percentage = 100.00;
            $this->save();
        }
    }

    public function updateWatchTime(int $seconds): void
    {
        $this->watch_time_seconds = min($this->watch_time_seconds + $seconds, $this->lesson->duration_minutes * 60);
        $this->last_accessed_at = now();
        
        // Update completion percentage based on watch time
        $totalSeconds = $this->lesson->duration_minutes * 60;
        if ($totalSeconds > 0) {
            $this->completion_percentage = min(100.00, ($this->watch_time_seconds / $totalSeconds) * 100);
        }
        
        $this->save();
    }

    public function updateProgress(float $percentage): void
    {
        $this->completion_percentage = min(100.00, max(0.00, $percentage));
        $this->last_accessed_at = now();
        
        if ($this->completion_percentage >= 100.00 && !$this->is_completed) {
            $this->markAsCompleted();
        } else {
            $this->save();
        }
    }
}
