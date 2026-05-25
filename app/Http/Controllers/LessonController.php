<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LessonController extends Controller
{
    public function show($courseId, $lessonId)
    {
        $enrollment = request()->attributes->get('enrollment');
        
        // Загружаем урок с курсом и модулем
        $lesson = Lesson::with(['materials', 'activeQuiz.questions', 'module.course.modules.publishedLessons'])
            ->findOrFail($lessonId);

        // Получаем или создаем прогресс урока
        $progress = $enrollment->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->firstOrCreate([
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
                'enrollment_id' => $enrollment->id,
            ], [
                'watch_time_seconds' => 0,
                'completion_percentage' => 0,
            ]);

        // Отмечаем начало просмотра урока
        if (!$progress->is_started) {
            $progress->markAsStarted();
        }

        // Получаем следующий урок
        $nextLesson = $this->getNextLesson($lesson, $enrollment);

        return view('student.lesson', compact('lesson', 'progress', 'nextLesson', 'enrollment'));
    }

    public function updateProgress(Request $request, $courseId, $lessonId)
    {
        $enrollment = request()->attributes->get('enrollment');
        
        $lesson = Lesson::findOrFail($lessonId);
        
        $progress = $enrollment->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->firstOrFail();

        $request->validate([
            'watch_time_seconds' => 'required|integer|min:0',
            'completion_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $progress->updateWatchTime($request->watch_time_seconds);
        $progress->updateProgress($request->completion_percentage);

        return response()->json([
            'success' => true,
            'progress' => [
                'watch_time_seconds' => $progress->watch_time_seconds,
                'completion_percentage' => $progress->completion_percentage,
                'is_completed' => $progress->is_completed,
                'formatted_watch_time' => $progress->formatted_watch_time,
                'formatted_completion_percentage' => $progress->formatted_completion_percentage,
            ]
        ]);
    }

    public function ajaxUpdateProgress(Request $request, $courseId, $lessonId)
    {
        $enrollment = request()->attributes->get('enrollment');

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Запись на курс не найдена'
            ], 403);
        }
        
        $lesson = Lesson::findOrFail($lessonId);
        
        $progress = $enrollment->lessonProgress()
            ->where('lesson_id', $lesson->id)
            ->firstOrCreate([
                'lesson_id' => $lesson->id,
                'user_id' => Auth::id(),
                'enrollment_id' => $enrollment->id,
            ], [
                'watch_time_seconds' => 0,
                'completion_percentage' => 0,
            ]);

        $action = $request->input('action', 'update_time');
        
        if ($action === 'update_time') {
            $request->validate([
                'watch_time_seconds' => 'nullable|integer|min:0',
                'completion_percentage' => 'nullable|numeric|min:0|max:100',
            ]);
            
            if ($request->watch_time_seconds) {
                $progress->updateWatchTime($request->watch_time_seconds);
            }
            if ($request->completion_percentage) {
                $progress->updateProgress($request->completion_percentage);
            }
        }

        return response()->json([
            'success' => true,
            'progress' => [
                'watch_time_seconds' => $progress->watch_time_seconds,
                'completion_percentage' => $progress->completion_percentage,
                'is_completed' => $progress->is_completed,
                'formatted_watch_time' => $progress->formatted_watch_time,
                'formatted_completion_percentage' => $progress->formatted_completion_percentage,
            ]
        ]);
    }

    public function markComplete(Request $request, $courseId, $lessonId)
    {
        try {
            $enrollment = request()->attributes->get('enrollment');
            
            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Запись на курс не найдена'
                ], 403);
            }
            
            $lesson = Lesson::findOrFail($lessonId);
            
            $progress = $enrollment->lessonProgress()
                ->where('lesson_id', $lesson->id)
                ->firstOrCreate([
                    'lesson_id' => $lesson->id,
                    'user_id' => Auth::id(),
                    'enrollment_id' => $enrollment->id,
                ], [
                    'watch_time_seconds' => 0,
                    'completion_percentage' => 0,
                ]);

            $progress->markAsCompleted();
            
            // Обновляем общий прогресс курса
            $enrollment->updateProgress();

            $nextLesson = $this->getNextLesson($lesson, $enrollment);
            
            return response()->json([
                'success' => true,
                'message' => 'Урок успешно завершен!',
                'next_lesson' => $nextLesson ? [
                    'id' => $nextLesson->id,
                    'title' => $nextLesson->title
                ] : null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error completing lesson: ' . $e->getMessage(), [
                'lesson_id' => $lessonId,
                'course_id' => $courseId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при завершении урока'
            ], 500);
        }
    }

    protected function getNextLesson(Lesson $currentLesson, Enrollment $enrollment)
    {
        try {
            $course = $currentLesson->module->course;
            $course->load('modules.publishedLessons');

            $allLessons = $course->modules
                ->flatMap(function ($module) {
                    return $module->publishedLessons;
                })
                ->sortBy('sort_order')
                ->values();

            $currentIndex = $allLessons->search(function ($lesson) use ($currentLesson) {
                return $lesson->id === $currentLesson->id;
            });

            return $allLessons->get($currentIndex + 1);
        } catch (\Exception $e) {
            return null;
        }
    }
}
