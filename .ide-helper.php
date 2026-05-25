<?php

/**
 * PhpStorm IDE Helper
 *
 * Этот файл помогает IDE понимать типы и связи в Laravel проекте
 */

namespace {
    exit("This file should not be included, only analyzed by your IDE");
}

/**
 * @property \App\Models\User $user
 * @property \App\Models\Course $course
 * @property \App\Models\Enrollment $enrollment
 * @property \App\Models\Lesson $lesson
 */
class Auth {
    /**
     * Get the authenticated user.
     *
     * @return \App\Models\User|null
     */
    public static function user() {}
}

/**
 * @property \App\Models\User $user
 * @property \App\Models\Course $course
 * @property \App\Models\Enrollment $enrollment
 */
class Request {
    /**
     * Get an input item from the request.
     *
     * @param string $key
     * @return mixed
     */
    public function input($key) {}
}

namespace App\Models {
    /**
     * @property int $id
     * @property string $first_name
     * @property string $last_name
     * @property string $email
     * @property string $full_name
     * @property \App\Models\Role|null $role
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $courses
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Certificate[] $certificates
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\LessonProgress[] $lessonProgress
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\QuizAttempt[] $quizAttempts
     */
    class User {
        /**
         * Get the courses for the user.
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function courses() {}

        /**
         * Get the enrollments for the user.
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function enrollments() {}

        /**
         * Check if user is admin.
         *
         * @return bool
         */
        public function isAdmin() {}

        /**
         * Check if user is instructor.
         *
         * @return bool
         */
        public function isInstructor() {}

        /**
         * Check if user is student.
         *
         * @return bool
         */
        public function isStudent() {}

        /**
         * Update the model in the database.
         *
         * @param array $attributes
         * @return bool
         */
        public function update(array $attributes = []) {}
    }

    /**
     * @property int $id
     * @property string $title
     * @property string $slug
     * @property string $description
     * @property float $price
     * @property float|null $discount_price
     * @property float $current_price
     * @property string $formatted_price
     * @property string $formatted_current_price
     * @property bool $has_discount
     * @property int|null $discount_percentage
     * @property string $level
     * @property string $level_label
     * @property int $duration_minutes
     * @property string $formatted_duration
     * @property int $students_count
     * @property float $rating
     * @property int $reviews_count
     * @property bool $is_published
     * @property bool $is_featured
     * @property \App\Models\User $instructor
     * @property \App\Models\Category|null $category
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Module[] $modules
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Lesson[] $lessons
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
     */
    class Course {
        /**
         * Get the instructor that owns the course.
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function instructor() {}

        /**
         * Get the category that owns the course.
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function category() {}

        /**
         * Get the enrollments for the course.
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function enrollments() {}

        /**
         * Get the reviews for the course.
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function reviews() {}

        /**
         * Scope a query to only include published courses.
         *
         * @param \Illuminate\Database\Eloquent\Builder $query
         * @return \Illuminate\Database\Eloquent\Builder
         */
        public function scopePublished($query) {}
    }

    /**
     * @property int $id
     * @property int $user_id
     * @property int $course_id
     * @property float $progress_percentage
     * @property string $formatted_progress
     * @property float $paid_amount
     * @property string $formatted_paid_amount
     * @property \Carbon\Carbon|null $completed_at
     * @property bool $is_completed
     * @property \App\Models\User $user
     * @property \App\Models\Course $course
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\LessonProgress[] $lessonProgress
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Certificate[] $certificates
     */
    class Enrollment {
        /**
         * Get the user that owns the enrollment.
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function user() {}

        /**
         * Get the course that owns the enrollment.
         *
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function course() {}

        /**
         * Get the lesson progress for the enrollment.
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function lessonProgress() {}

        /**
         * Update the progress of the enrollment.
         *
         * @return void
         */
        public function updateProgress() {}
    }
}

namespace Illuminate\Support\Facades {
    /**
     * @see \Illuminate\Support\Facades\Storage
     */
    class Storage {
        /**
         * Get a filesystem instance.
         *
         * @param string|null $name
         * @return \Illuminate\Contracts\Filesystem\Filesystem
         */
        public static function disk($name = null) {}
    }
}
 
namespace Illuminate\Contracts\Filesystem {
    /**
     * Filesystem interface.
     */
    interface Filesystem {
        /**
         * Download the file at the given path.
         *
         * @param string $path
         * @param string|null $name
         * @param array $headers
         * @return \Symfony\Component\HttpFoundation\StreamedResponse
         */
        public function download($path, $name = null, array $headers = []);
 
        /**
         * Determine if a file exists.
         *
         * @param string $path
         * @return bool
         */
        public function exists($path);
 
        /**
         * Write the contents of a file.
         *
         * @param string $path
         * @param string|resource $contents
         * @param array $options
         * @return bool
         */
        public function put($path, $contents, $options = []);
    }
}