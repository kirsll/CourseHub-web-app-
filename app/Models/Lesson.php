<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'content',
        'video_url',
        'duration_minutes',
        'type',
        'is_free',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LessonMaterial::class)
            ->orderBy('sort_order');
    }

    public function quiz(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function activeQuiz(): HasMany
    {
        return $this->hasMany(Quiz::class)
            ->where('is_active', true);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function getCourseAttribute(): ?Course
    {
        return $this->module?->course;
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_minutes ?? 0;
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}ч {$remainingMinutes}мин";
        }

        return "{$minutes}мин";
    }

    public function getIsVideoAttribute(): bool
    {
        return $this->type === 'video';
    }

    public function getIsTextAttribute(): bool
    {
        return $this->type === 'text';
    }

    public function getIsQuizAttribute(): bool
    {
        return $this->type === 'quiz';
    }

    public function getIsAssignmentAttribute(): bool
    {
        return $this->type === 'assignment';
    }

    public function getVideoIdAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // YouTube video ID extraction
        if (str_contains($this->video_url, 'youtube.com/watch?v=')) {
            parse_str(parse_url($this->video_url, PHP_URL_QUERY), $query);
            return $query['v'] ?? null;
        }

        if (str_contains($this->video_url, 'youtu.be/')) {
            return explode('/', $this->video_url)[3] ?? null;
        }

        // Vimeo video ID extraction
        if (str_contains($this->video_url, 'vimeo.com/')) {
            $path = parse_url($this->video_url, PHP_URL_PATH);
            return explode('/', $path)[1] ?? null;
        }

        return null;
    }
}
