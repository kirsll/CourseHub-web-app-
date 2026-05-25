@extends('layouts.app')

@section('title', 'Дашборд студента')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Мой дашборд</h1>
        <p class="text-gray-600 mt-2">Добро пожаловать, {{ auth()->user()->first_name }}!</p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Активные курсы</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $inProgressCourses->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Завершенные</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $completedCourses->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-certificate text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Сертификаты</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $certificates->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Часы обучения</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ floor($recentProgress->sum('watch_time_seconds') / 3600) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Текущие курсы -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Продолжить обучение</h2>
                </div>
                <div class="p-6">
                    @forelse ($inProgressCourses as $enrollment)
                        <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100 last:border-0">
                            <div class="flex items-center">
                                @if ($enrollment->course->thumbnail)
                                    <img src="{{ asset('public/storage/' . $enrollment->course->thumbnail) }}" 
                                         alt="{{ $enrollment->course->title }}" 
                                         class="w-16 h-16 rounded-lg mr-4">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                        <i class="fas fa-book text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        <a href="{{ route('student.course', $enrollment->course) }}" 
                                           class="hover:text-blue-600">
                                            {{ $enrollment->course->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $enrollment->course->instructor->full_name }}
                                    </p>
                                    <div class="mt-2">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm text-gray-600">Прогресс</span>
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ $enrollment->formatted_progress }}
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ min(100, max(0, (float) $enrollment->progress_percentage)) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('student.learn', $enrollment->course) }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Продолжить
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-book-open text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">У вас пока нет активных курсов</p>
                            <a href="{{ route('courses.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                Найти курсы
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Завершенные курсы -->
            @if ($completedCourses->count() > 0)
                <div class="bg-white rounded-lg shadow mt-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Завершенные курсы</h2>
                    </div>
                    <div class="p-6">
                        @foreach ($completedCourses as $enrollment)
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    @if ($enrollment->course->thumbnail)
                                        <img src="{{ asset('public/storage/' . $enrollment->course->thumbnail) }}" 
                                             alt="{{ $enrollment->course->title }}" 
                                             class="w-12 h-12 rounded-lg mr-3">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fas fa-book text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="font-medium text-gray-900">
                                            {{ $enrollment->course->title }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            Завершен {{ $enrollment->completed_at->format('d.m.Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    @if ($enrollment->completed_at)
                                    <a href="{{ route('student.certificates') }}" 
                                       class="text-blue-600 hover:text-blue-800"
                                       title="Сертификат доступен">
                                        <i class="fas fa-certificate"></i>
                                    </a>
                                @else
                                    <span class="text-gray-400" title="Завершите курс для получения сертификата">
                                        <i class="fas fa-certificate"></i>
                                    </span>
                                @endif
                                    <a href="{{ route('student.course', $enrollment->course) }}" 
                                       class="text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Сертификаты -->
            @if ($certificates->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Последние сертификаты</h2>
                    </div>
                    <div class="p-6">
                        @foreach ($certificates as $certificate)
                            <div class="flex items-center mb-4">
                                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                                    <i class="fas fa-certificate text-yellow-600 text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 text-sm">
                                        {{ $certificate->course->title }}
                                    </h4>
                                    <p class="text-xs text-gray-600">
                                        {{ $certificate->formatted_issued_at }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        <a href="{{ route('student.certificates') }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm">
                            Все сертификаты →
                        </a>
                    </div>
                </div>
            @endif

            <!-- Последняя активность -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Последняя активность</h2>
                </div>
                <div class="p-6">
                    @forelse ($recentProgress as $progress)
                        <div class="flex items-center mb-4">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <i class="fas fa-play-circle text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm">
                                    {{ $progress->lesson->title }}
                                </h4>
                                <p class="text-xs text-gray-600">
                                    {{ $progress->lesson->course->title }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $progress->last_accessed_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Активность не найдена</p>
                    @endforelse
                </div>
            </div>

            <!-- Рекомендации -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Рекомендуемые курсы</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-200 rounded-lg mr-3 flex items-center justify-center">
                                <i class="fas fa-code text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm">Python для начинающих</h4>
                                <p class="text-xs text-gray-600">42 урока • 8 часов</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gray-200 rounded-lg mr-3 flex items-center justify-center">
                                <i class="fas fa-palette text-gray-400"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm">UI/UX Design</h4>
                                <p class="text-xs text-gray-600">35 уроков • 12 часов</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('courses.index') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm mt-4 inline-block">
                        Все рекомендации →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
