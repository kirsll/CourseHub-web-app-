<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * @property User $instructor
 */

class InstructorController extends Controller
{
    public function dashboard()
    {
        /** @var User $instructor */
        $instructor = Auth::user();
        
        $courses = $instructor->courses()->with(['enrollments', 'reviews'])->get();
        
        $stats = [
            'total_courses' => $courses->count(),
            'published_courses' => $courses->where('is_published', true)->count(),
            'total_students' => $courses->sum(function($course) {
                return $course->enrollments->count();
            }),
            'total_earnings' => 0, // Заглушка для демонстрации
            'monthly_earnings' => 0, // Заглушка для демонстрации
        ];

        $recentCourses = $courses->take(5);
        $recentEnrollments = collect(); // Заглушка для демонстрации

        return view('instructor.dashboard', compact('stats', 'recentCourses', 'recentEnrollments'));
    }

    // Управление курсами
    public function courses()
    {
        /** @var User $user */
        $user = Auth::user();
        $courses = $user->courses()
            ->withCount(['enrollments', 'reviews'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('instructor.courses', compact('courses'));
    }

    public function createCourse()
    {
        $categories = Category::where('is_active', true)->get();
        
        return view('instructor.courses.create', compact('categories'));
    }

    public function storeCourse(Request $request)
    {
        $mergeData = [];
        foreach (['requirements', 'what_you_will_learn', 'target_audience'] as $field) {
            if ($request->has($field)) {
                $input = $request->input($field);
                if (is_string($input)) {
                    $items = array_filter(array_map('trim', explode("\n", $input)));
                } elseif (is_array($input)) {
                    $items = array_filter(array_map('trim', $input));
                } else {
                    $items = [];
                }
                $mergeData[$field] = empty($items) ? null : array_values($items);
            }
        }
        if (!empty($mergeData)) {
            $request->merge($mergeData);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'required|string|max:10',
            'requirements' => 'nullable|array',
            'what_you_will_learn' => 'nullable|array',
            'target_audience' => 'nullable|array',
        ]);

        try {
            $course = Course::create([
                'instructor_id' => Auth::id(),
                'title' => $request->title,
                'slug' => Str::slug($request->title) . '-' . time(),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'level' => $request->level,
                'language' => $request->language,
                'requirements' => $request->requirements,
                'what_you_will_learn' => $request->what_you_will_learn,
                'target_audience' => $request->target_audience,
            ]);

            return redirect()->route('instructor.courses')
                ->with('success', 'Курс создан. Теперь вы можете его редактировать.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при создании курса: ' . $e->getMessage());
        }
    }

    public function editCourse(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $course->load(['modules.lessons', 'category']);
        $categories = Category::where('is_active', true)->get();

        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $mergeData = [];
        foreach (['requirements', 'what_you_will_learn', 'target_audience'] as $field) {
            if ($request->has($field)) {
                $input = $request->input($field);
                if (is_string($input)) {
                    $items = array_filter(array_map('trim', explode("\n", $input)));
                } elseif (is_array($input)) {
                    $items = array_filter(array_map('trim', $input));
                } else {
                    $items = [];
                }
                $mergeData[$field] = empty($items) ? null : array_values($items);
            }
        }
        if (!empty($mergeData)) {
            $request->merge($mergeData);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'level' => 'required|in:beginner,intermediate,advanced',
            'language' => 'required|string|max:10',
            'requirements' => 'nullable|array',
            'what_you_will_learn' => 'nullable|array',
            'target_audience' => 'nullable|array',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preview_video' => 'nullable|string|max:255',
        ]);

        $course->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'level' => $request->level,
            'language' => $request->language,
            'requirements' => $request->requirements,
            'what_you_will_learn' => $request->what_you_will_learn,
            'target_audience' => $request->target_audience,
            'thumbnail' => $request->thumbnail,
            'preview_video' => $request->preview_video,
        ]);

        return redirect()->route('instructor.courses.edit', $course)
            ->with('success', 'Курс обновлен');
    }

    public function deleteCourse(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        if ($course->enrollments()->count() > 0) {
            return back()->with('error', 'Нельзя удалить курс со студентами');
        }

        $course->delete();

        return redirect()->route('instructor.courses')
            ->with('success', 'Курс удален');
    }

    public function publishCourse(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        // Проверяем, есть ли у курса модули и уроки
        if ($course->modules()->count() === 0) {
            return back()->with('error', 'Добавьте хотя бы один модуль перед публикацией');
        }

        if ($course->lessons()->count() === 0) {
            return back()->with('error', 'Добавьте хотя бы один урок перед публикацией');
        }

        $course->update([
            'is_published' => true,
            'published_at' => now()
        ]);

        return redirect()->route('instructor.courses')
            ->with('success', 'Курс опубликован и теперь доступен студентам');
    }

    public function unpublishCourse(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $course->update([
            'is_published' => false
        ]);

        return redirect()->route('instructor.courses')
            ->with('success', 'Курс снят с публикации');
    }

    // Управление модулями
    public function modules(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $course->load('modules.lessons');

        return view('instructor.modules', compact('course'));
    }

    public function storeModule(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
        ]);

        $sortOrder = $course->modules()->max('sort_order') + 1;

        $module = Module::create([
            'course_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'sort_order' => $sortOrder,
        ]);

        return redirect()->route('instructor.modules', $course)
            ->with('success', 'Модуль успешно добавлен');
    }

    public function updateModule(Request $request, Module $module)
    {
        $course = $module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $module->update($request->only(['title', 'description', 'sort_order', 'is_published']));

        return response()->json([
            'success' => true,
            'module' => $module,
        ]);
    }

    public function deleteModule(Module $module)
    {
        $course = $module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $module->delete();

        return redirect()->route('instructor.modules', $course)
            ->with('success', 'Модуль удален');
    }

    // Управление уроками
    public function lessons(Module $module)
    {
        $course = $module->course;
        
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $module->load('lessons');

        return view('instructor.lessons', compact('course', 'module'));
    }

    public function storeLesson(Request $request, Module $module)
    {
        $course = $module->course;
        
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'type' => 'required|in:video,text,quiz,assignment',
            'is_free' => 'boolean',
        ]);

        $sortOrder = $module->lessons()->max('sort_order') + 1;

        $lesson = Lesson::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'video_url' => $request->video_url,
            'duration_minutes' => $request->duration_minutes,
            'type' => $request->type,
            'is_free' => $request->is_free ?? false,
            'sort_order' => $sortOrder,
        ]);

        return redirect()->route('instructor.lessons', $module)
            ->with('success', 'Урок успешно добавлен');
    }

    public function editLesson(Lesson $lesson)
    {
        $course = $lesson->module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $lesson->load(['module.course', 'materials', 'activeQuiz.questions']);

        return view('instructor.lessons.edit', compact('lesson', 'course'));
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $course = $lesson->module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string|max:255',
            'duration_minutes' => 'required|integer|min:0',
            'type' => 'required|in:video,text,quiz,assignment',
            'is_free' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $lesson->update($request->only([
            'title',
            'description',
            'content',
            'video_url',
            'duration_minutes',
            'type',
            'is_free',
            'is_published',
            'sort_order',
        ]));

        return redirect()->route('instructor.lessons.edit', $lesson)
            ->with('success', 'Урок обновлен');
    }

    public function deleteLesson(Lesson $lesson)
    {
        $course = $lesson->module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $lesson->delete();

        return redirect()->route('instructor.lessons', $lesson->module)
            ->with('success', 'Урок удален');
    }

    public function quiz(Lesson $lesson)
    {
        $course = $lesson->module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $lesson->load(['module.course', 'activeQuiz.questions']);
        $quiz = $lesson->activeQuiz()->first();

        return view('instructor.quizzes.edit', compact('lesson', 'course', 'quiz'));
    }

    public function storeQuiz(Request $request, Lesson $lesson)
    {
        $course = $lesson->module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
        ]);

        $quiz = Quiz::create([
            'lesson_id' => $lesson->id,
            'title' => $request->title,
            'description' => $request->description,
            'time_limit_minutes' => $request->time_limit_minutes,
            'passing_score' => $request->passing_score,
            'max_attempts' => $request->max_attempts,
            'shuffle_questions' => (bool) ($request->shuffle_questions ?? false),
            'show_correct_answers' => (bool) ($request->show_correct_answers ?? true),
            'is_active' => true,
        ]);

        return redirect()->route('instructor.quiz', $lesson)
            ->with('success', 'Тест создан');
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $lesson = $quiz->lesson;
        $course = $lesson->module->course;

        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:10',
            'shuffle_questions' => 'nullable|boolean',
            'show_correct_answers' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $quiz->update($request->only([
            'title',
            'description',
            'time_limit_minutes',
            'passing_score',
            'max_attempts',
            'shuffle_questions',
            'show_correct_answers',
            'is_active',
        ]));

        return redirect()->route('instructor.quiz', $lesson)
            ->with('success', 'Тест обновлен');
    }

    public function reviews(Course $course)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($course->instructor_id !== Auth::id() && !$user->isAdmin()) {
            abort(403);
        }

        $reviews = $course->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('instructor.reviews', compact('course', 'reviews'));
    }

    // Студенты курса
    public function students(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $query = $course->enrollments()->with('user');

        // Поиск по имени или email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('completed_at');
            } elseif ($request->status === 'active') {
                $query->whereNull('completed_at');
            }
        }

        // Сортировка
        $sort = $request->input('sort', 'date_desc');
        switch ($sort) {
            case 'progress_desc':
                $query->orderBy('progress_percentage', 'desc');
                break;
            case 'progress_asc':
                $query->orderBy('progress_percentage', 'asc');
                break;
            case 'name_asc':
                $query->join('users', 'enrollments.user_id', '=', 'users.id')
                      ->orderBy('users.first_name', 'asc')
                      ->orderBy('users.last_name', 'asc')
                      ->select('enrollments.*');
                break;
            case 'date_asc':
                $query->orderBy('enrolled_at', 'asc');
                break;
            case 'date_desc':
            default:
                $query->orderBy('enrolled_at', 'desc');
                break;
        }

        $enrollments = $query->paginate(20)->withQueryString();

        return view('instructor.students', compact('course', 'enrollments'));
    }

    public function studentProgress(Course $course, User $student)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $enrollment = $course->enrollments()
            ->where('user_id', $student->id)
            ->with(['lessonProgress.lesson.module'])
            ->firstOrFail();

        return view('instructor.student-progress', compact('course', 'student', 'enrollment'));
    }

    // Аналитика
    public function analytics()
    {
        /** @var User $instructor */
        $instructor = Auth::user();
        
        $courses = $instructor->courses()->withCount(['enrollments', 'reviews'])->get();
        
        $analytics = [
            'total_revenue' => 0, // Заглушка
            'total_students' => $courses->sum('enrollments_count'),
            'average_rating' => $courses->where('rating', '>', 0)->avg('rating') ?? 0,
            'completion_rate' => 0, // Заглушка
        ];

        return view('instructor.analytics', compact('analytics', 'courses'));
    }

    public function earnings()
    {
        /** @var User $instructor */
        $instructor = Auth::user();
        
        $earnings = $instructor->courses()
            ->join('payments', 'courses.id', '=', 'payments.course_id')
            ->where('payments.status', 'completed')
            ->selectRaw('
                DATE_TRUNC(\'month\', payments.paid_at) as month,
                SUM(payments.instructor_earnings) as earnings,
                COUNT(*) as sales_count
            ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        return view('instructor.earnings', compact('earnings'));
    }

    // Профиль
    public function profile()
    {
        $instructor = Auth::user();
        
        return view('instructor.profile', compact('instructor'));
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

        /** @var User $instructor */
        $instructor = Auth::user();
        $instructor->update($request->all());

        return back()->with('success', 'Профиль обновлен');
    }
}
