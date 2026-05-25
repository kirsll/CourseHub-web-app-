<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'type',
        'options',
        'correct_answers',
        'points',
        'explanation',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'points' => 'integer',
        'is_active' => 'boolean',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getIsSingleChoiceAttribute(): bool
    {
        return $this->type === 'single_choice';
    }

    public function getIsMultipleChoiceAttribute(): bool
    {
        return $this->type === 'multiple_choice';
    }

    public function getIsTrueFalseAttribute(): bool
    {
        return $this->type === 'true_false';
    }

    public function getIsTextAttribute(): bool
    {
        return $this->type === 'text';
    }

    public function getFormattedOptionsAttribute(): array
    {
        if (!$this->options) {
            return [];
        }

        return collect($this->options)->map(function ($option, $key) {
            return [
                'key' => $key,
                'value' => $option,
                'label' => chr(65 + $key), // A, B, C, D...
            ];
        })->toArray();
    }

    public function getCorrectAnswersKeysAttribute(): array
    {
        if (!$this->correct_answers) {
            return [];
        }

        return is_array($this->correct_answers) 
            ? $this->correct_answers 
            : [$this->correct_answers];
    }

    public function getFormattedCorrectAnswersAttribute(): array
    {
        $correctKeys = $this->correct_answers_keys;
        $options = $this->formatted_options;

        return collect($correctKeys)->map(function ($key) use ($options) {
            $option = collect($options)->firstWhere('key', $key);
            return $option ? $option['label'] : null;
        })->filter()->toArray();
    }

    public function validateAnswer($answer): bool
    {
        $correctAnswers = $this->correct_answers_keys;

        return match($this->type) {
            'single_choice' => $answer === $correctAnswers[0] ?? false,
            'multiple_choice' => is_array($answer) && 
                empty(array_diff($answer, $correctAnswers)) && 
                empty(array_diff($correctAnswers, $answer)),
            'true_false' => $answer === $correctAnswers[0] ?? false,
            'text' => is_string($answer) && 
                in_array(strtolower(trim($answer)), array_map('strtolower', $correctAnswers)),
            default => false
        };
    }

    public function calculateScore($answer): int
    {
        if (!$this->validateAnswer($answer)) {
            return 0;
        }

        return $this->points;
    }

    public function getIsAutoGradableAttribute(): bool
    {
        return in_array($this->type, ['single_choice', 'multiple_choice', 'true_false']);
    }

    public function getRequiresManualGradingAttribute(): bool
    {
        return $this->type === 'text';
    }

    public function getQuestionTypeLabelAttribute(): string
    {
        return match($this->type) {
            'single_choice' => 'Одиночный выбор',
            'multiple_choice' => 'Множественный выбор',
            'true_false' => 'Правда/Ложь',
            'text' => 'Текстовый ответ',
            default => 'Неизвестный тип'
        };
    }
}
