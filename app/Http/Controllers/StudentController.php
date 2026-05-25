<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\Certificate;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        /** @var User $student */
        $student = Auth::user();
        
        $enrollments = $student->enrollments()
            ->with('course.instructor')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $inProgressCourses = $student->enrollments()
            ->whereNull('completed_at')
            ->with('course')
            ->orderBy('created_at', 'desc')
            ->get();

        $completedCourses = $student->enrollments()
            ->whereNotNull('completed_at')
            ->with('course')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        $certificates = $student->certificates()
            ->with('course')
            ->orderBy('issued_at', 'desc')
            ->take(3)
            ->get();

        $recentProgress = $student->lessonProgress()
            ->with('lesson.module.course')
            ->orderBy('last_accessed_at', 'desc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'enrollments',
            'inProgressCourses',
            'completedCourses',
            'certificates',
            'recentProgress'
        ));
    }

    public function courses()
    {
        /** @var User $student */
        $student = Auth::user();
        
        $enrollments = $student->enrollments()
            ->with(['course.instructor', 'course.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('student.courses', compact('enrollments'));
    }

    public function course($courseId)
    {
        /** @var User $student */
        $student = Auth::user();
        
        $course = Course::findOrFail($courseId);
        
        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->with(['lessonProgress.lesson.module'])
            ->firstOrFail();

        $course->load(['modules.publishedLessons']);

        return view('student.course', compact('course', 'enrollment'));
    }

    public function learn($courseId)
    {
        $student = Auth::user();
        
        $course = Course::findOrFail($courseId);
        
        $enrollment = request()->attributes->get('enrollment');
        
        $course->load(['modules.publishedLessons']);

        // Находим текущий урок (первый незавершенный или последний просмотренный)
        $currentLesson = null;
        
        // Ищем первый незавершенный урок
        foreach ($course->modules as $module) {
            foreach ($module->publishedLessons as $lesson) {
                $progress = $enrollment->lessonProgress()
                    ->where('lesson_id', $lesson->id)
                    ->first();
                
                if (!$progress || !$progress->is_completed) {
                    $currentLesson = $lesson;
                    break 2;
                }
            }
        }

        // Если все уроки завершены, берем последний
        if (!$currentLesson) {
            $lastProgress = $enrollment->lessonProgress()
                ->with('lesson')
                ->orderBy('last_accessed_at', 'desc')
                ->first();
            
            $currentLesson = $lastProgress?->lesson;
            
            // Если нет прогресса, берем первый урок
            if (!$currentLesson) {
                $firstModule = $course->modules->first();
                $currentLesson = $firstModule ? $firstModule->publishedLessons->first() : null;
            }
        }

        // Получаем прогресс для текущего урока
        if ($currentLesson) {
            $progress = $enrollment->lessonProgress()
                ->where('lesson_id', $currentLesson->id)
                ->first() ?? new LessonProgress([
                    'enrollment_id' => $enrollment->id,
                    'lesson_id' => $currentLesson->id,
                    'is_completed' => false,
                    'completion_percentage' => 0,
                    'watch_time_seconds' => 0,
                ]);
        } else {
            // Если нет уроков, создаем пустой прогресс
            $progress = new LessonProgress([
                'enrollment_id' => $enrollment->id,
                'lesson_id' => 0,
                'is_completed' => false,
                'completion_percentage' => 0,
                'watch_time_seconds' => 0,
            ]);
        }

        // Находим следующий урок
        $nextLesson = null;
        $foundCurrent = false;
        
        if ($currentLesson) {
            foreach ($course->modules as $module) {
                foreach ($module->publishedLessons as $lesson) {
                    if ($foundCurrent) {
                        $nextLesson = $lesson;
                        break 2;
                    }
                    if ($lesson->id == $currentLesson->id) {
                        $foundCurrent = true;
                    }
                }
            }
        }

        return view('student.learn', compact('course', 'enrollment', 'currentLesson', 'progress', 'nextLesson'));
    }

    public function unenroll(Request $request, $courseId)
    {
        $student = Auth::user();
        
        $enrollment = $student->enrollments()
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Учитывая каскадное удаление (onDelete('cascade') в миграциях), 
        // удаление enrollment автоматически удалит все LessonProgress.
        $enrollment->delete();

        return redirect()->route('student.courses')
            ->with('success', 'Курс был успешно удален из вашей библиотеки.');
    }

    public function profile()
    {
        /** @var User $student */
        $student = Auth::user();
        
        return view('student.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'social_links' => 'nullable|array',
        ]);

        /** @var User $student */
        $student = Auth::user();
        $student->update($request->all());

        return back()->with('success', 'Профиль обновлен');
    }

    public function orders()
    {
        /** @var User $student */
        $student = Auth::user();
        
        $orders = $student->orders()
            ->with(['payments.course', 'enrollments.course'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('student.orders', compact('orders'));
    }
}
