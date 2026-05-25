<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'enrollment_id',
        'certificate_number',
        'template',
        'certificate_data',
        'file_path',
        'issued_at',
        'is_active',
    ];

    protected $casts = [
        'certificate_data' => 'array',
        'issued_at' => 'datetime',
        'is_active' => 'boolean',
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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function getFormattedIssuedAtAttribute(): string
    {
        return $this->issued_at->format('d.m.Y');
    }

    public function getFormattedIssuedAtFullAttribute(): string
    {
        return $this->issued_at->format('d F Y г.');
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('certificates.download', $this->id);
    }

    public function getPublicUrlAttribute(): string
    {
        return route('certificates.verify', $this->certificate_number);
    }

    public function getVerificationUrlAttribute(): string
    {
        return route('certificates.verify', $this->certificate_number);
    }

    public function getStudentNameAttribute(): string
    {
        return $this->certificate_data['student_name'] ?? $this->user->full_name;
    }

    public function getCourseTitleAttribute(): string
    {
        return $this->certificate_data['course_title'] ?? $this->course->title;
    }

    public function getInstructorNameAttribute(): string
    {
        return $this->certificate_data['instructor_name'] ?? $this->course->instructor->full_name;
    }

    public function getCompletionDateAttribute(): string
    {
        return $this->certificate_data['completion_date'] ?? $this->enrollment->completed_at->format('d.m.Y');
    }

    public function getTotalHoursAttribute(): string
    {
        $hours = floor($this->course->duration_minutes / 60);
        $minutes = $this->course->duration_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}ч {$minutes}мин";
        }
        
        return "{$minutes}мин";
    }

    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    public static function generateCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(uniqid());
        } while (self::where('certificate_number', $number)->exists());

        return $number;
    }
}
