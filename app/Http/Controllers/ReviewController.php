<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Review;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();
        
        // Проверяем, что студент записан на курс
        $enrollment = $course->enrollments()
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Проверяем, что курс завершен хотя бы на 50%
        if ($enrollment->progress_percentage < 50) {
            return back()->with('error', 'Можно оставить отзыв после прохождения минимум 50% курса');
        }

        // Проверяем, что отзыв еще не оставлен
        if ($course->reviews()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Вы уже оставляли отзыв на этот курс');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrollment_id' => $enrollment->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_visible' => true, // Опубликовываем сразу (можно изменить на модерацию)
        ]);

        return back()->with('success', 'Спасибо за ваш отзыв!');
    }

    public function update(Request $request, Review $review)
    {
        $user = Auth::user();
        
        // Проверяем, что отзыв принадлежит пользователю
        if ($review->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Отзыв обновлен');
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();
        
        // Проверяем, что отзыв принадлежит пользователю
        if ($review->user_id !== $user->id) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Отзыв удален');
    }

    // Методы для преподавателей и администраторов
    public function approve(Review $review)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isInstructor()) {
            abort(403);
        }

        // Преподаватель может одобрять только отзывы своих курсов
        if ($user->isInstructor()) {
            $course = $review->course;
            if ($course->instructor_id !== Auth::id()) {
                abort(403);
            }
        }

        $review->approve();

        return back()->with('success', 'Отзыв одобрен');
    }

    public function hide(Review $review)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isInstructor()) {
            abort(403);
        }

        // Преподаватель может скрывать только отзывы своих курсов
        if ($user->isInstructor()) {
            $course = $review->course;
            if ($course->instructor_id !== Auth::id()) {
                abort(403);
            }
        }

        $review->hide();

        return back()->with('success', 'Отзыв скрыт');
    }
}
