<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::published()->with(['instructor', 'category']);

        // Фильтры
        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->level) {
            $query->where('level', $request->level);
        }

        if ($request->price) {
            if ($request->price === 'free') {
                $query->where('price', 0);
            } elseif ($request->price === 'paid') {
                $query->where('price', '>', 0);
            }
        }

        if ($request->rating) {
            $query->where('rating', '>=', $request->rating);
        }

        // Поиск
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'ILIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'ILIKE', '%' . $request->search . '%');
            });
        }

        // Сортировка
        $sort = $request->sort ?? 'popularity';
        switch ($sort) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $query->orderBy('current_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('current_price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            default:
                $query->orderBy('students_count', 'desc');
        }

        $courses = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        if (!$course->is_published) {
            abort(404);
        }

        // Загружаем связанные данные
        $course->load(['instructor', 'category', 'modules.lessons', 'reviews.user']);

        // Получаем связанные курсы
        $relatedCourses = Course::published()
            ->where('id', '!=', $course->id)
            ->where('category_id', $course->category_id)
            ->with(['instructor'])
            ->take(3)
            ->get();

        // Получаем отзыв текущего пользователя
        $userReview = null;
        if (auth()->check()) {
            $userReview = $course->reviews()->where('user_id', auth()->id())->first();
        }

        return view('courses.show', compact('course', 'relatedCourses', 'userReview'));
    }

    public function category(Category $category)
    {
        $courses = $category->publishedCourses()
            ->with(['instructor'])
            ->paginate(12);

        return view('courses.category', compact('category', 'courses'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('courses.index');
        }

        $courses = Course::published()
            ->where(function ($q) use ($query) {
                $q->where('title', 'ILIKE', '%' . $query . '%')
                  ->orWhere('description', 'ILIKE', '%' . $query . '%');
            })
            ->with(['instructor', 'category'])
            ->paginate(12);

        return view('courses.search', compact('courses', 'query'));
    }

    public function apiSearch(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $courses = Course::published()
            ->where('title', 'ILIKE', '%' . $query . '%')
            ->with(['instructor', 'category'])
            ->take(10)
            ->get()
            ->map(function ($course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'thumbnail' => $course->thumbnail,
                    'instructor' => $course->instructor->full_name,
                    'price' => $course->formatted_price,
                    'rating' => $course->rating,
                    'url' => route('courses.show', $course)
                ];
            });

        return response()->json($courses);
    }

    // Методы загрузки файлов (для преподавателей)
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->store('courses/images', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:mp4,avi,mov,wmv|max:102400' // 100MB
        ]);

        $file = $request->file('file');
        $path = $file->store('courses/videos', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path)
        ]);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,ppt,ppt,xls,xlsx,txt|max:10240' // 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('courses/documents', 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
            'size' => $file->getSize(),
            'mime' => $file->getMimeType()
        ]);
    }
}
