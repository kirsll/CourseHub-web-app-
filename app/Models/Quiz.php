<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
        'time_limit_minutes',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'show_correct_answers',
        'is_active',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
        'shuffle_questions' => 'boolean',
        'show_correct_answers' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)
            ->orderBy('sort_order');
    }

    public function activeQuestions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function userAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->activeQuestions->sum('points');
    }

    public function getFormattedTimeLimitAttribute(): string
    {
        if (!$this->time_limit_minutes) {
            return 'Без ограничений';
        }

        $hours = floor($this->time_limit_minutes / 60);
        $minutes = $this->time_limit_minutes % 60;

        if ($hours > 0) {
            return "{$hours}ч {$minutes}мин";
        }

        return "{$minutes}мин";
    }

    public function getFormattedPassingScoreAttribute(): string
    {
        return $this->passing_score . '%';
    }

    public function getQuestionsCountAttribute(): int
    {
        return $this->activeQuestions->count();
    }

    public function getHasTimeLimitAttribute(): bool
    {
        return !is_null($this->time_limit_minutes) && $this->time_limit_minutes > 0;
    }

    public function getIsPassingScorePercentageAttribute(): bool
    {
        return $this->passing_score <= 100;
    }

    public function getMinimumPassingPointsAttribute(): int
    {
        $totalPoints = $this->total_points;
        
        if ($totalPoints === 0) {
            return 0;
        }

        return ceil(($this->passing_score / 100) * $totalPoints);
    }

    public function canUserAttempt(?User $user = null): bool
    {
        if (!$user) {
            return false;
        }

        $userAttempts = $this->attempts()->where('user_id', $user->id)->count();
        
        return $userAttempts < $this->max_attempts;
    }

    public function getUserAttemptsCount(?User $user = null): int
    {
        if (!$user) {
            return 0;
        }

        return $this->attempts()->where('user_id', $user->id)->count();
    }

    public function getUserRemainingAttempts(?User $user = null): int
    {
        if (!$user) {
            return $this->max_attempts;
        }

        return max(0, $this->max_attempts - $this->getUserAttemptsCount($user));
    }

    public function getBestUserScore(?User $user = null): ?float
    {
        if (!$user) {
            return null;
        }

        $bestAttempt = $this->attempts()
            ->where('user_id', $user->id)
            ->orderBy('percentage', 'desc')
            ->first();

        return $bestAttempt?->percentage;
    }

    public function hasUserPassed(?User $user = null): bool
    {
        $bestScore = $this->getBestUserScore($user);
        
        return $bestScore && $bestScore >= $this->passing_score;
    }
}
