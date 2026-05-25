<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    public function publishedCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id')
            ->where('is_published', true);
    }

    public function getFullPathAttribute(): string
    {
        $path = collect([$this->slug]);
        $parent = $this->parent;
        
        while ($parent) {
            $path->prepend($parent->slug);
            $parent = $parent->parent;
        }
        
        return $path->implode('/');
    }
}
