<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)
            ->orderBy('sort_order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->hasMany(Lesson::class)
            ->where('is_published', true)
            ->orderBy('sort_order');
    }

    public function getDurationAttribute(): int
    {
        return $this->publishedLessons->sum('duration_minutes') ?? 0;
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration ?? 0;
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}ч {$remainingMinutes}мин";
        }

        return "{$minutes}мин";
    }

    public function getLessonsCountAttribute(): int
    {
        return $this->publishedLessons->count();
    }
}
