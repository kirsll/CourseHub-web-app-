@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Заголовок курса -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg p-8 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $course->title }}</h1>
                <p class="text-blue-100">{{ $course->description }}</p>
                <div class="flex items-center space-x-4 mt-4">
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                        {{ $course->level_label }}
                    </span>
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                        {{ $course->formatted_duration }}
                    </span>
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm">
                        {{ $course->modules_count ?? 0 }} модулей
                    </span>
                </div>
            </div>
            <div class="text-right">
                @if ($course->thumbnail)
                    <img src="{{ asset('public/storage/' . $course->thumbnail) }}" 
                         alt="{{ $course->title }}" 
                         class="w-32 h-24 rounded-lg object-cover">
                @else
                    <div class="w-32 h-24 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-4xl"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <!-- Описание -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">О курсе</h2>
                <div class="prose max-w-none text-gray-600">
                    {!! $course->content !!}
                </div>
            </div>

            <!-- Что вы изучите -->
            @if ($course->what_you_will_learn)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Что вы изучите</h2>
                    <ul class="space-y-2">
                        @foreach (is_array($course->what_you_will_learn) ? $course->what_you_will_learn : json_decode($course->what_you_will_learn, true) as $item)
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Требования -->
            @if ($course->requirements)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Требования</h2>
                    <ul class="space-y-2">
                        @foreach (is_array($course->requirements) ? $course->requirements : json_decode($course->requirements, true) as $requirement)
                            <li class="flex items-start">
                                <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-3"></i>
                                <span>{{ $requirement }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Целевая аудитория -->
            @if ($course->target_audience)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Целевая аудитория</h2>
                    <ul class="space-y-2">
                        @foreach (is_array($course->target_audience) ? $course->target_audience : json_decode($course->target_audience, true) as $audience)
                            <li class="flex items-start">
                                <i class="fas fa-user-friends text-blue-500 mt-1 mr-3"></i>
                                <span>{{ $audience }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Модули и уроки -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Содержание курса</h2>
                @if ($course->modules && $course->modules->count() > 0)
                    @foreach ($course->modules as $module)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $module->title }}</h3>
                            @if ($module->description)
                                <p class="text-gray-600 text-sm mb-4">{{ $module->description }}</p>
                            @endif
                            <div class="space-y-2">
                                @if ($module->lessons && $module->lessons->count() > 0)
                                    @foreach ($module->lessons as $lesson)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-play-circle text-gray-400 mr-3"></i>
                                                <span class="text-gray-700">{{ $lesson->title }}</span>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $lesson->formatted_duration ?? 'N/A' }}
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm">Уроки пока не добавлены</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500">Содержание курса пока не добавлено</p>
                @endif
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="lg:col-span-1">
            <div class="sticky-sidebar space-y-6">
            <!-- Инструктор -->
            @if ($course->instructor)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Преподаватель</h3>
                    <div class="flex items-center mb-4">
                        @if ($course->instructor->avatar)
                            <img src="{{ asset('public/storage/' . $course->instructor->avatar) }}" 
                                 alt="{{ $course->instructor->full_name }}" 
                                 class="w-12 h-12 rounded-full mr-3">
                        @else
                            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $course->instructor->full_name }}</h4>
                            <p class="text-sm text-gray-600">{{ $course->instructor->bio ?? 'Информация о преподавателе' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>{{ $course->rating ?? 0 }} ⭐</span>
                        <span>{{ $course->reviews_count ?? 0 }} отзывов</span>
                    </div>
                </div>
            @endif

            <!-- Статистика -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Студенты:</span>
                        <span class="font-medium">{{ $course->students_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Уроки:</span>
                        <span class="font-medium">{{ $course->lessons_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Рейтинг:</span>
                        <span class="font-medium">{{ $course->rating ?? 0 }} ⭐</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Длительность:</span>
                        <span class="font-medium">{{ $course->formatted_duration }}</span>
                    </div>
                </div>
            </div>

            <!-- Цена и запись -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Цена</h3>
                @if ($course->discount_price && $course->discount_price < $course->price)
                    <div class="mb-2">
                        <span class="text-gray-400 line-through text-lg">{{ $course->formatted_price }}</span>
                        <div class="text-2xl font-bold text-blue-600">{{ $course->formatted_current_price }}</div>
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                            Скидка {{ round((($course->price - $course->discount_price) / $course->price) * 100) }}%
                        </span>
                    </div>
                @else
                    <div class="text-2xl font-bold text-blue-600">{{ $course->formatted_current_price }}</div>
                @endif
                
                @if (auth()->check())
                    <form action="{{ route('student.enroll', $course) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 text-center font-medium">
                            Записаться на курс
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" 
                       class="block w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 text-center font-medium">
                        Войти для записи
                    </a>
                @endif
            </div>

            <!-- Категория -->
            @if ($course->category)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Категория</h3>
                    <div class="flex items-center">
                        @if ($course->category->icon)
                            <i class="{{ $course->category->icon }} text-blue-600 mr-3"></i>
                        @endif
                        <div>
                            <div class="font-medium text-gray-900">{{ $course->category->name }}</div>
                            <div class="text-sm text-gray-600">{{ $course->category->courses_count ?? 0 }} курсов</div>
                        </div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>

    <!-- Отзывы -->
    @if ($course->visibleReviews && $course->visibleReviews->count() > 0)
        <div class="bg-white rounded-lg shadow p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Отзывы</h2>
            <div class="space-y-4">
                @foreach ($course->visibleReviews as $review)
                    <div class="border-b pb-4 last:border-0">
                        <div class="flex items-start mb-2">
                            @if ($review->user->avatar)
                                <img src="{{ asset('public/storage/' . $review->user->avatar) }}" 
                                     alt="{{ $review->user->full_name }}" 
                                     class="w-10 h-10 rounded-full mr-3">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center mb-1">
                                    <div class="font-medium text-gray-900">{{ $review->user->full_name }}</div>
                                    <div class="flex text-yellow-400 ml-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $review->rating)
                                                <i class="fas fa-star text-sm"></i>
                                            @else
                                                <i class="far fa-star text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                @if ($review->comment)
                                    <p class="text-gray-600 text-sm mt-2">{{ $review->comment }}</p>
                                @endif
                                <div class="text-xs text-gray-500 mt-2">
                                    {{ $review->created_at->format('d.m.Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
