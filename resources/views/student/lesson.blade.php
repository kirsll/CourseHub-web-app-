@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Навигация курса -->
    <nav class="bg-white rounded-lg shadow mb-8 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('student.course', $lesson->course->id) }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    К курсу
                </a>
                <div class="h-6 w-px bg-blue-600 rounded"></div>
                <span class="text-gray-900 font-medium">{{ $lesson->course->title }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">
                    Прогресс: {{ $enrollment->formatted_progress }}
                </span>
                <div class="w-32 bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, max(0, (float) $enrollment->progress_percentage)) }}%"></div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Контент урока -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $lesson->title }}</h2>
            @if ($lesson->description)
                <p class="text-gray-600">{{ $lesson->description }}</p>
            @endif
            <div class="flex items-center space-x-4 mt-4 text-sm text-gray-600">
                <span>
                    <i class="fas fa-clock mr-1"></i> {{ $lesson->formatted_duration }}
                </span>
                <span>
                    <i class="fas fa-book mr-1"></i> Модуль: {{ $lesson->module->title }}
                </span>
                @if ($lesson->is_free)
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">
                        <i class="fas fa-gift mr-1"></i> Бесплатный урок
                    </span>
                @endif
            </div>
        </div>

        <!-- Видео или контент урока -->
        @if ($lesson->type === 'video')
            <div class="mb-6">
                @if ($lesson->video_url)
                    <div class="relative w-full bg-black rounded-lg overflow-hidden shadow-lg" style="padding-top: 56.25%;">
                        <iframe 
                            src="{{ $lesson->video_url }}"
                            class="absolute top-0 left-0 w-full h-full"
                            allowfullscreen
                            title="{{ $lesson->title }}">
                        </iframe>
                    </div>
                @else
                    <div class="relative w-full bg-gray-200 rounded-lg overflow-hidden" style="padding-top: 56.25%;">
                        <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center">
                            <p class="text-gray-500">Видео не загружено</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Текстовый контент -->
        @if ($lesson->content)
            <div class="prose max-w-none mb-6">
                {!! $lesson->content !!}
            </div>
        @endif

        <!-- Материалы урока -->
        @if ($lesson->materials && $lesson->materials->count() > 0)
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Материалы урока</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($lesson->materials as $material)
                        <div class="flex items-center p-4 border rounded-lg hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file text-blue-500 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">{{ $material->title ?? 'Материал' }}</h4>
                                <p class="text-xs text-gray-500">Файл</p>
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-xs">
                                    Скачать
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Тест урока -->
        @if ($lesson->activeQuiz->isNotEmpty())
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Тест урока</h3>
                @php
                    $quiz = $lesson->activeQuiz->first();
                @endphp
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-blue-900">{{ $quiz->title }}</h4>
                            <p class="text-sm text-blue-700">
                                {{ $quiz->questions->count() ?? 0 }} вопросов • 
                                {{ $quiz->time_limit_minutes ?? 0 }} минут • 
                                {{ $quiz->passing_score ?? 0 }}% для прохождения
                            </p>
                        </div>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Начать тест
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Кнопки управления -->
        <div class="flex justify-between items-center mt-8">
            @if (!$progress->is_completed)
                <button 
                    onclick="markLessonComplete({{ $lesson->id }})"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
                    type="button">
                    <i class="fas fa-check mr-2"></i>
                    Завершить урок
                </button>
            @else
                <span class="text-green-600 font-medium">
                    <i class="fas fa-check-circle mr-2"></i>
                    Урок завершен
                </span>
            @endif

            @if ($nextLesson)
                <a href="{{ route('student.lessons.show', [$lesson->course->id, $nextLesson->id]) }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                    Следующий урок
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            @else
                <a href="{{ route('student.course', $lesson->course->id) }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                    К курсу
                    <i class="fas fa-home ml-2"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Боковая панель с уроками -->
    @if ($lesson->course->modules->count() > 0)
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Содержание курса</h3>
            <div class="space-y-2">
                @foreach ($lesson->course->modules as $module)
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4">
                            <h4 class="font-medium text-gray-900 mb-2">{{ $module->title }}</h4>
                            @foreach ($module->publishedLessons as $moduleLesson)
                                <div class="flex items-center justify-between py-2 px-3 rounded-lg transition-colors duration-200 {{ $moduleLesson->id === $lesson->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                    <div class="flex items-center">
                                        @if ($moduleLesson->id === $lesson->id)
                                            <i class="fas fa-play-circle text-blue-600"></i>
                                        @else
                                            <i class="far fa-play-circle text-gray-400"></i>
                                        @endif
                                        <a href="{{ route('student.lessons.show', [$lesson->course->id, $moduleLesson->id]) }}" 
                                           class="ml-3 text-gray-700 hover:text-blue-600 {{ $moduleLesson->id === $lesson->id ? 'font-semibold' : '' }}">
                                            {{ $moduleLesson->title }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $moduleLesson->formatted_duration }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function markLessonComplete(lessonId) {
    fetch(`/student/courses/{{ $lesson->course->id }}/lessons/${lessonId}/complete`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Сервер вернул не JSON ответ');
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            if (data.message) {
                alert(data.message);
            }
            if (data.next_lesson) {
                setTimeout(() => {
                    window.location.href = `/student/courses/{{ $lesson->course->id }}/lessons/${data.next_lesson.id}`;
                }, 100);
            } else {
                setTimeout(() => {
                    window.location.reload();
                }, 100);
            }
        } else {
            throw new Error(data.message || 'Failed to complete lesson');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Произошла ошибка при завершении урока');
    });
}

// Автосохранение прогресса
let progressInterval;
function startProgressTracking() {
    progressInterval = setInterval(() => {
        const watchTime = 10;
        fetch(`/student/courses/{{ $lesson->course->id }}/lessons/{{ $lesson->id }}/progress`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_time',
                watch_time_seconds: watchTime,
                completion_percentage: Math.min(100, ({{ $progress->completion_percentage ?? 0 }} + watchTime * 0.5))
            })
        });
    }, 10000);
}

startProgressTracking();

window.addEventListener('beforeunload', () => {
    if (progressInterval) {
        clearInterval(progressInterval);
    }
});
</script>
@endpush
