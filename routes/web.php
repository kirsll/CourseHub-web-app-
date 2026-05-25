<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Аутентификация
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Публичные курсы
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
Route::get('/categories/{category:slug}', [CourseController::class, 'category'])->name('courses.category');
Route::get('/search', [CourseController::class, 'search'])->name('courses.search');

/*
|--------------------------------------------------------------------------
| Student Routes (/student/*)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'student'])->prefix('student')->name('student.')->group(function () {
    
    // Дашборд студента
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    
    // Мои курсы
    Route::get('/courses', [StudentController::class, 'courses'])->name('courses');
    Route::get('/courses/{course}', [StudentController::class, 'course'])->where('course', '[0-9]+')->name('course');
    
    // Обучение
    Route::middleware(['enrollment'])->group(function () {
        Route::get('/courses/{course}/learn', [StudentController::class, 'learn'])->where('course', '[0-9]+')->name('learn');
        Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->where(['course' => '[0-9]+', 'lesson' => '[0-9]+'])->name('lessons.show');
        Route::post('/courses/{course}/lessons/{lesson}/progress', [LessonController::class, 'ajaxUpdateProgress'])->where(['course' => '[0-9]+', 'lesson' => '[0-9]+'])->name('lessons.progress');
        Route::post('/courses/{course}/lessons/{lesson}/complete', [LessonController::class, 'markComplete'])->where(['course' => '[0-9]+', 'lesson' => '[0-9]+'])->name('lessons.complete');
    });
    
    // Запись на курсы и отписка
    Route::post('/courses/{course}/enroll', [EnrollmentController::class, 'enroll'])->where('course', '[0-9]+')->name('enroll');
    Route::delete('/courses/{course}/unenroll', [StudentController::class, 'unenroll'])->where('course', '[0-9]+')->name('unenroll');
    Route::get('/courses/{course}/checkout', [EnrollmentController::class, 'checkout'])->where('course', '[0-9]+')->name('checkout');
    
    // Оплата
    Route::post('/payments/process/{course}', [EnrollmentController::class, 'processPayment'])->where('course', '[0-9]+')->name('payment.process');
    Route::get('/payments/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payments/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    
    // Отзывы
    Route::post('/courses/{course}/reviews', [ReviewController::class, 'store'])->where('course', '[0-9]+')->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Сертификаты
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates');
    Route::get('/certificates/{certificate}', [CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');
    
    // Профиль
    Route::get('/profile', [StudentController::class, 'profile'])->name('profile');
    Route::put('/profile', [StudentController::class, 'updateProfile'])->name('profile.update');
    Route::get('/orders', [StudentController::class, 'orders'])->name('orders');
});

/*
|--------------------------------------------------------------------------
| Instructor Routes (/instructor/*)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    
    // Дашборд преподавателя
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
    
    // Управление курсами
    Route::get('/courses', [InstructorController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [InstructorController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [InstructorController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}/edit', [InstructorController::class, 'editCourse'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [InstructorController::class, 'deleteCourse'])->name('courses.delete');
    
    // Публикация курса
    Route::get('/courses/{course}/publish', [InstructorController::class, 'publishCourse'])->name('courses.publish');
    Route::get('/courses/{course}/unpublish', [InstructorController::class, 'unpublishCourse'])->name('courses.unpublish');
    
    // Управление модулями
    Route::get('/courses/{course}/modules', [InstructorController::class, 'modules'])->name('modules');
    Route::post('/courses/{course}/modules', [InstructorController::class, 'storeModule'])->name('modules.store');
    Route::put('/modules/{module}', [InstructorController::class, 'updateModule'])->name('modules.update');
    Route::delete('/modules/{module}', [InstructorController::class, 'deleteModule'])->name('modules.delete');
    
    // Управление уроками
    Route::get('/modules/{module}/lessons', [InstructorController::class, 'lessons'])->name('lessons');
    Route::post('/modules/{module}/lessons', [InstructorController::class, 'storeLesson'])->name('lessons.store');
    Route::get('/lessons/{lesson}/edit', [InstructorController::class, 'editLesson'])->name('lessons.edit');
    Route::put('/lessons/{lesson}', [InstructorController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [InstructorController::class, 'deleteLesson'])->name('lessons.delete');
    
    // Управление тестами
    Route::get('/lessons/{lesson}/quiz', [InstructorController::class, 'quiz'])->name('quiz');
    Route::post('/lessons/{lesson}/quiz', [InstructorController::class, 'storeQuiz'])->name('quiz.store');
    Route::put('/quizzes/{quiz}', [InstructorController::class, 'updateQuiz'])->name('quiz.update');
    
    // Студенты курса
    Route::get('/courses/{course}/students', [InstructorController::class, 'students'])->name('courses.students');
    Route::get('/courses/{course}/students/{student}/progress', [InstructorController::class, 'studentProgress'])->name('courses.student.progress');
    
    // Отзывы курса
    Route::get('/courses/{course}/reviews', [InstructorController::class, 'reviews'])->name('courses.reviews');
    
    // Аналитика и статистика
    Route::get('/analytics', [InstructorController::class, 'analytics'])->name('analytics');
    Route::get('/earnings', [InstructorController::class, 'earnings'])->name('earnings');
    
    // Профиль преподавателя
    Route::get('/profile', [InstructorController::class, 'profile'])->name('profile');
    Route::put('/profile', [InstructorController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (/admin/*)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Дашборд администратора
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Управление пользователями
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Управление ролями
    Route::get('/roles', [AdminController::class, 'roles'])->name('roles');
    Route::post('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
    
    // Управление категориями
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
    
    // Управление курсами
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/{course}', [AdminController::class, 'showCourse'])->name('courses.show');
    Route::put('/courses/{course}/status', [AdminController::class, 'updateCourseStatus'])->name('courses.status');
    Route::delete('/courses/{course}', [AdminController::class, 'deleteCourse'])->name('courses.delete');
    
    // Управление заказами
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::put('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    
    // Управление платежами
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::get('/payments/{payment}', [AdminController::class, 'showPayment'])->name('payments.show');

    // Режим БД
    Route::get('/db', [AdminController::class, 'dbMode'])->name('db');
    Route::get('/db/schema', [AdminController::class, 'dbSchema'])->name('db.schema');
    Route::get('/db/{table}/create', [AdminController::class, 'dbCreate'])->where('table', '[A-Za-z0-9_]+')->name('db.create');
    Route::post('/db/{table}', [AdminController::class, 'dbStore'])->where('table', '[A-Za-z0-9_]+')->name('db.store');
    Route::get('/db/{table}/{id}/edit', [AdminController::class, 'dbEdit'])->where(['table' => '[A-Za-z0-9_]+', 'id' => '[0-9]+'])->name('db.edit');
    Route::put('/db/{table}/{id}', [AdminController::class, 'dbUpdate'])->where(['table' => '[A-Za-z0-9_]+', 'id' => '[0-9]+'])->name('db.update');
    Route::delete('/db/{table}/{id}', [AdminController::class, 'dbDestroy'])->where(['table' => '[A-Za-z0-9_]+', 'id' => '[0-9]+'])->name('db.destroy');
    
    // Модерация отзывов
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::put('/reviews/{review}/approve', [AdminController::class, 'approveReview'])->name('reviews.approve');
    Route::put('/reviews/{review}/hide', [AdminController::class, 'hideReview'])->name('reviews.hide');
    
    // Аналитика и отчеты
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/reports/sales', [AdminController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/courses', [AdminController::class, 'coursesReport'])->name('reports.courses');
    Route::get('/reports/users', [AdminController::class, 'usersReport'])->name('reports.users');
    
    // Системные настройки
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| API Routes для AJAX запросов
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('api')->name('api.')->group(function () {
    
    // Поиск курсов
    Route::get('/search/courses', [CourseController::class, 'apiSearch'])->name('search.courses');
    
    // Прогресс урока (AJAX)
    Route::post('/lessons/{lesson}/progress/update', [LessonController::class, 'ajaxUpdateProgress'])->name('lessons.progress.update');
    
    // Уведомления
    Route::get('/notifications', [HomeController::class, 'notifications'])->name('notifications');
    Route::put('/notifications/{notification}/read', [HomeController::class, 'markNotificationRead'])->name('notifications.read');
    
    // Загрузка файлов
    Route::post('/upload/image', [CourseController::class, 'uploadImage'])->name('upload.image');
    Route::post('/upload/video', [CourseController::class, 'uploadVideo'])->name('upload.video');
    Route::post('/upload/document', [CourseController::class, 'uploadDocument'])->name('upload.document');
});

/*
|--------------------------------------------------------------------------
| Verification Routes (для сертификатов)
|--------------------------------------------------------------------------
*/

Route::get('/certificates/verify/{certificate_number}', [CertificateController::class, 'verify'])->name('certificates.verify');

/*
|--------------------------------------------------------------------------
| Fallback Routes
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
