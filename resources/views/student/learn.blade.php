@extends('layouts.app')

@section('title', 'Обучение')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Навигация курса -->
    <nav class="bg-white rounded-lg shadow mb-8 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('student.course', $course->id) }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    К курсу
                </a>
                <div class="h-6 w-px bg-blue-600 rounded"></div>
                <span class="text-gray-900 font-medium">{{ $course->title }}</span>
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
    @if ($currentLesson)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ $currentLesson->title }}</h2>
                @if ($currentLesson->description)
                    <p class="text-gray-600 mt-2">{{ $currentLesson->description }}</p>
                @endif
            </div>

        <!-- Видео или контент урока -->
        @if ($currentLesson->type === 'video')
            <div class="mb-6">
                @if ($currentLesson->video_url)
                    <div class="relative w-full bg-black rounded-lg overflow-hidden shadow-lg" style="padding-top: 56.25%;">
                        <video 
                            controls
                            class="absolute top-0 left-0 w-full h-full"
                            poster="{{ asset('images/video-poster.jpg') }}"
                            title="{{ $currentLesson->title }}">
                            <source src="{{ $currentLesson->video_url }}" type="video/mp4">
                            Ваш браузер не поддерживает видео.
                        </video>
                    </div>
                @else
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <i class="fas fa-video text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">Видео для этого урока еще не загружено</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Текстовый контент -->
        @if ($currentLesson->content)
            <div class="prose max-w-none mb-6">
                {!! $currentLesson->content !!}
            </div>
        @endif

        <!-- Материалы урока -->
        @if ($currentLesson->materials && $currentLesson->materials->count() > 0)
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Материалы урока</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($currentLesson->materials as $material)
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

        <!-- Кнопки управления -->
        <div class="flex justify-between items-center">
            @if (!$progress->is_completed)
                <button 
                    onclick="markLessonComplete({{ $currentLesson->id }})"
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
                <a href="{{ route('student.lessons.show', [$course->id, $nextLesson->id]) }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                    Следующий урок
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            @else
                <a href="{{ route('student.course', $course->id) }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
                    <i class="fas fa-home ml-2"></i>
                </a>
            @endif
        </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-center py-8">
                <i class="fas fa-book-open text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Нет уроков в курсе</h3>
                <p class="text-gray-600 mb-4">В этом курсе пока нет добавленных уроков</p>
                <a href="{{ route('student.course', $course->id) }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Вернуться к курсу
                </a>
            </div>
        </div>
    @endif

    <!-- Боковая панель с уроками -->
    @if ($course->modules->count() > 0)
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Содержание курса</h3>
            <div class="space-y-2">
                @foreach ($course->modules as $module)
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-4">
                            <h4 class="font-medium text-gray-900">{{ $module->title }}</h4>
                            @foreach ($module->publishedLessons as $lesson)
                                <div class="flex items-center justify-between py-2 px-3 rounded-lg transition-colors duration-200 {{ $lesson->id === $currentLesson->id ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                    <div class="flex items-center">
                                        @if ($lesson->id === $currentLesson->id)
                                            <i class="fas fa-play-circle text-blue-600"></i>
                                        @else
                                            <i class="far fa-play-circle text-gray-400"></i>
                                        @endif
                                        <a href="{{ route('student.lessons.show', [$course->id, $lesson->id]) }}" 
                                           class="ml-3 text-gray-700 hover:text-blue-600 {{ $lesson->id === $currentLesson->id ? 'font-semibold' : '' }}">
                                            {{ $lesson->title }}
                                        </a>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $lesson->formatted_duration }}
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
@if ($currentLesson)
<script>
function markLessonComplete(lessonId) {
    fetch(`/student/courses/{{ $course->id }}/lessons/${lessonId}/complete`, {
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
                showNotification(data.message, 'success');
            }
            if (data.next_lesson) {
                setTimeout(() => {
                    window.location.href = `/student/courses/{{ $course->id }}/lessons/${data.next_lesson.id}`;
                }, 1000);
            } else {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        } else {
            throw new Error(data.message || 'Failed to complete lesson');
        }
    })
    .catch(error => {
        showNotification('Произошла ошибка при завершении урока', 'error');
    });
}

// Автосохранение прогресса
let progressInterval;
function startProgressTracking() {
    progressInterval = setInterval(() => {
        const watchTime = Math.floor(Math.random() * 10);
        fetch(`/student/courses/{{ $course->id }}/lessons/{{ $currentLesson->id }}/progress`, {
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
@endif
@endpush
