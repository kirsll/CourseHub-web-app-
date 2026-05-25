<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'content',
        'thumbnail',
        'preview_video',
        'price',
        'discount_price',
        'level',
        'language',
        'duration_minutes',
        'lessons_count',
        'students_count',
        'rating',
        'reviews_count',
        'requirements',
        'what_you_will_learn',
        'target_audience',
        'is_published',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'requirements' => 'array',
        'what_you_will_learn' => 'array',
        'target_audience' => 'array',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'course_category')
            ->withTimestamps();
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)
            ->orderBy('sort_order');
    }

    public function publishedModules(): HasMany
    {
        return $this->hasMany(Module::class)
            ->where('is_published', true)
            ->orderBy('sort_order');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Module::class)
            ->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function visibleReviews(): HasMany
    {
        return $this->hasMany(Review::class)
            ->where('is_visible', true);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->discount_price ?? $this->price ?? 0);
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->has_discount) {
            return null;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) ($this->price ?? 0), 2, '.', ' ') . ' ₽';
    }

    public function getFormattedCurrentPriceAttribute(): string
    {
        return number_format((float) ($this->current_price ?? 0), 2, '.', ' ') . ' ₽';
    }

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'beginner' => 'Начальный',
            'intermediate' => 'Средний',
            'advanced' => 'Продвинутый',
            default => (string) $this->level,
        };
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
}
