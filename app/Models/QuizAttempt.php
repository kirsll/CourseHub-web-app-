<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'enrollment_id',
        'attempt_number',
        'answers',
        'score',
        'total_points',
        'percentage',
        'is_passed',
        'started_at',
        'completed_at',
        'time_taken_seconds',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_points' => 'integer',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'time_taken_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
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

    public function getIsInProgressAttribute(): bool
    {
        return is_null($this->completed_at);
    }

    public function getFormattedScoreAttribute(): string
    {
        return "{$this->score} / {$this->total_points}";
    }

    public function getFormattedPercentageAttribute(): string
    {
        return number_format($this->percentage, 1) . '%';
    }

    public function getFormattedTimeTakenAttribute(): string
    {
        if (!$this->time_taken_seconds) {
            return '—';
        }

        $seconds = $this->time_taken_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02d:%02d', $minutes, $remainingSeconds);
    }

    public function getFormattedStartedAtAttribute(): string
    {
        return $this->started_at->format('d.m.Y H:i');
    }

    public function getFormattedCompletedAtAttribute(): ?string
    {
        return $this->completed_at?->format('d.m.Y H:i');
    }

    public function getRemainingTimeAttribute(): ?int
    {
        if (!$this->quiz->has_time_limit || $this->is_completed) {
            return null;
        }

        $timeLimitSeconds = $this->quiz->time_limit_minutes * 60;
        $elapsedSeconds = now()->diffInSeconds($this->started_at);
        
        return max(0, $timeLimitSeconds - $elapsedSeconds);
    }

    public function getFormattedRemainingTimeAttribute(): ?string
    {
        $remainingSeconds = $this->remaining_time;
        
        if ($remainingSeconds === null) {
            return null;
        }

        $hours = floor($remainingSeconds / 3600);
        $minutes = floor(($remainingSeconds % 3600) / 60);
        $seconds = $remainingSeconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    public function getIsTimeExpiredAttribute(): bool
    {
        $remainingTime = $this->remaining_time;
        
        return $remainingTime !== null && $remainingTime <= 0;
    }

    public function getCorrectAnswersCountAttribute(): int
    {
        $correctCount = 0;
        
        foreach ($this->answers as $questionId => $answer) {
            $question = $this->quiz->questions()->find($questionId);
            
            if ($question && $question->validateAnswer($answer)) {
                $correctCount++;
            }
        }
        
        return $correctCount;
    }

    public function getTotalQuestionsCountAttribute(): int
    {
        return $this->quiz->questions_count;
    }

    public function getIncorrectAnswersCountAttribute(): int
    {
        return $this->total_questions_count - $this->correct_answers_count;
    }

    public function calculateScore(): void
    {
        $totalScore = 0;
        $totalPoints = 0;
        
        foreach ($this->answers as $questionId => $answer) {
            $question = $this->quiz->questions()->find($questionId);
            
            if ($question) {
                $totalPoints += $question->points;
                $totalScore += $question->calculateScore($answer);
            }
        }
        
        $this->score = $totalScore;
        $this->total_points = $totalPoints;
        $this->percentage = $totalPoints > 0 ? ($totalScore / $totalPoints) * 100 : 0;
        $this->is_passed = $this->percentage >= $this->quiz->passing_score;
        
        $this->save();
    }

    public function complete(): void
    {
        if (!$this->is_completed) {
            $this->completed_at = now();
            $this->time_taken_seconds = $this->started_at->diffInSeconds(now());
            $this->calculateScore();
        }
    }

    public function forceComplete(): void
    {
        $this->complete();
    }

    public function getAnswerForQuestion(int $questionId): mixed
    {
        return $this->answers[$questionId] ?? null;
    }

    public function setAnswerForQuestion(int $questionId, mixed $answer): void
    {
        $answers = $this->answers ?? [];
        $answers[$questionId] = $answer;
        $this->answers = $answers;
        $this->save();
    }

    public function removeAnswerForQuestion(int $questionId): void
    {
        $answers = $this->answers ?? [];
        unset($answers[$questionId]);
        $this->answers = $answers;
        $this->save();
    }
}
