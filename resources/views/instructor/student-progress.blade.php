@extends('layouts.app')

@section('title', 'Прогресс студента')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Прогресс студента</h1>
                <p class="text-gray-600 mt-2">Детальная информация о прогрессе студента {{ $student->full_name }}</p>
            </div>
            <a href="{{ route('instructor.courses.students', $course) }}" 
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i> К студентам
            </a>
        </div>
    </div>

    <!-- Информация о студенте -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center">
            @if ($student->avatar)
                <img src="{{ asset('public/storage/' . $student->avatar) }}" 
                     alt="{{ $student->full_name }}" 
                     class="h-16 w-16 rounded-full mr-4">
            @else
                <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                    <i class="fas fa-user text-gray-600 text-xl"></i>
                </div>
            @endif
            <div class="flex-1">
                <h2 class="text-xl font-semibold text-gray-900">{{ $student->full_name }}</h2>
                <p class="text-gray-600">{{ $student->email }}</p>
                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                    <span>
                        <i class="fas fa-calendar mr-1"></i>
                        Записан: {{ $enrollment->enrolled_at->format('d.m.Y') }}
                    </span>
                    @if ($enrollment->completed_at)
                        <span>
                            <i class="fas fa-graduation-cap mr-1"></i>
                            Завершен: {{ $enrollment->completed_at->format('d.m.Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Общая статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Общий прогресс</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($enrollment->progress_percentage, 1) }}%
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-play-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Пройдено уроков</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $enrollment->lessonProgress()->where('is_completed', true)->count() }}
                        /
                        {{ $enrollment->lessonProgress()->count() }}
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
                    <p class="text-sm text-gray-600">Время обучения</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $enrollment->lessonProgress->sum('watch_time_seconds') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-ruble-sign text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Оплачено</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($enrollment->paid_amount, 0, '.', ' ') }} ₽
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Прогресс по модулям -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Прогресс по модулям</h2>
        </div>
        <div class="p-6">
            @foreach ($course->modules as $module)
                <div class="mb-6 last:mb-0">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $module->title }}</h3>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, max(0, ($module->lessons->count() > 0 ? ($module->lessons->where(function($lesson) use ($enrollment) { return $enrollment->lessonProgress()->where('lesson_id', $lesson->id)->where('is_completed', true)->exists(); })->count() / $module->lessons->count() * 100 : 0)) }}%"></div>
                            </div>
                            <span class="text-sm text-gray-900">
                                {{ $module->lessons->count() > 0 ? number_format(($module->lessons->where(function($lesson) use ($enrollment) { return $enrollment->lessonProgress()->where('lesson_id', $lesson->id)->where('is_completed', true)->exists(); })->count() / $module->lessons->count() * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        @foreach ($module->lessons as $lesson)
                            <div class="flex items-center justify-between p-3 border rounded-lg {{ $enrollment->lessonProgress()->where('lesson_id', $lesson->id)->where('is_completed', true)->exists() ? 'bg-green-50' : 'bg-gray-50' }}">
                                <div class="flex items-center">
                                    @if ($enrollment->lessonProgress()->where('lesson_id', $lesson->id)->where('is_completed', true)->exists())
                                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    @else
                                        <i class="far fa-circle text-gray-400 mr-2"></i>
                                    @endif
                                    <span class="text-gray-700">{{ $lesson->title }}</span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span>{{ $lesson->formatted_duration }}</span>
                                    <span>{{ $enrollment->lessonProgress()->where('lesson_id', $lesson->id)->first()->formatted_completion_percentage ?? '0%' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
