<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnrollmentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Необходима авторизация');
        }

        $courseId = $request->route('course');
        $lessonId = $request->route('lesson');
        
        /** @var User $user */
        $user = Auth::user();
        
        // Администраторы и преподаватели имеют доступ
        if ($user->isAdmin() || $user->isInstructor()) {
            return $next($request);
        }

        // Проверяем запись на курс
        $enrollment = Enrollment::where('user_id', $user->id)
            ->when($courseId, function ($query, $courseId) {
                return $query->where('course_id', $courseId);
            })
            ->when($lessonId, function ($query, $lessonId) {
                return $query->whereHas('course.lessons', function ($q) use ($lessonId) {
                    $q->where('lessons.id', $lessonId);
                });
            })
            ->first();

        if (!$enrollment) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо записаться на курс для доступа к материалам',
                ], 403);
            }

            if ($courseId) {
                $course = Course::find($courseId);
                if ($course) {
                    return redirect()->route('courses.show', $course)
                        ->with('error', 'Необходимо записаться на курс для доступа к материалам');
                }
            }

            return redirect()->route('courses.index')
                ->with('error', 'Необходимо записаться на курс для доступа к материалам');
        }

        // Добавляем enrollment в request для использования в контроллерах
        $request->attributes->set('enrollment', $enrollment);

        return $next($request);
    }
}
