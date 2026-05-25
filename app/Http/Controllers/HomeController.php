<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Course::published()
            ->featured()
            ->with(['instructor', 'category'])
            ->take(8)
            ->get();

        $popularCourses = Course::published()
            ->with(['instructor', 'category'])
            ->orderBy('students_count', 'desc')
            ->take(6)
            ->get();

        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('publishedCourses')
            ->get();

        $instructors = User::whereHas('courses', function ($query) {
                $query->published();
            })
            ->withCount('courses')
            ->take(6)
            ->get();

        return view('home', compact(
            'featuredCourses',
            'popularCourses', 
            'categories',
            'instructors'
        ));
    }

    public function notifications(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    public function markNotificationRead(Request $request, $notificationId)
    {
        /** @var User $user */
        $user = Auth::user();
        $notification = $user->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
}
