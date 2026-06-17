@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showDeleteModal: false }">
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
                        {{ $course->lessons_count }} уроков
                    </span>
                </div>
            </div>
            <div class="text-right">
                @if ($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" 
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

    <!-- Прогресс курса -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Прогресс курса</h2>
            <span class="text-sm font-medium text-blue-600">{{ $enrollment->formatted_progress }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3 mb-3">
            <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ min(100, max(0, (float) $enrollment->progress_percentage)) }}%"></div>
        </div>
        <div class="flex items-center justify-between text-sm text-gray-600">
            <span>{{ $enrollment->completed_lessons_count }} из {{ $enrollment->total_lessons_count }} уроков завершено</span>
            @if ($enrollment->is_completed)
                <span class="text-green-600 font-medium"><i class="fas fa-check-circle mr-1"></i>Курс завершен!</span>
            @else
                <a href="{{ route('student.learn', $course->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fas fa-play mr-1"></i>Продолжить обучение
                </a>
            @endif
        </div>
    </div>

    <!-- Информация о курсе -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
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
                        @foreach ($course->what_you_will_learn as $item)
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
                        @foreach ($course->requirements as $requirement)
                            <li class="flex items-start">
                                <i class="fas fa-exclamation-circle text-yellow-500 mt-1 mr-3"></i>
                                <span>{{ $requirement }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Модули и уроки -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Содержание курса</h2>
                @foreach ($course->modules as $module)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $module->title }}</h3>
                        @if ($module->description)
                            <p class="text-gray-600 text-sm mb-4">{{ $module->description }}</p>
                        @endif
                        <div class="space-y-2">
                            @foreach ($module->publishedLessons as $lesson)
                                @php
                                    $lessonProgress = $enrollment->lessonProgress->where('lesson_id', $lesson->id)->first();
                                    $isLessonCompleted = $lessonProgress && $lessonProgress->is_completed;
                                @endphp
                                <a href="{{ route('student.lessons.show', [$course->id, $lesson->id]) }}" 
                                   class="flex items-center justify-between p-3 rounded-lg transition-colors duration-200 {{ $isLessonCompleted ? 'bg-green-50 hover:bg-green-100' : 'bg-gray-50 hover:bg-blue-50' }}">
                                    <div class="flex items-center">
                                        @if ($isLessonCompleted)
                                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                        @else
                                            <i class="fas fa-play-circle text-gray-400 mr-3"></i>
                                        @endif
                                        <span class="text-gray-700">{{ $lesson->title }}</span>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $lesson->formatted_duration }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="lg:col-span-1">
            <!-- Инструктор -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Преподаватель</h3>
                <div class="flex items-center mb-4">
                    @if ($course->instructor->avatar)
                        <img src="{{ asset('storage/' . $course->instructor->avatar) }}" 
                             alt="{{ $course->instructor->full_name }}" 
                             class="w-12 h-12 rounded-full mr-3">
                    @else
                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                            <i class="fas fa-user text-gray-600"></i>
                        </div>
                    @endif
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $course->instructor->full_name }}</h4>
                        <p class="text-sm text-gray-600">{{ $course->instructor->bio }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>{{ $course->rating }} ⭐</span>
                    <span>{{ $course->reviews_count }} отзывов</span>
                </div>
                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                    Посмотреть профиль
                </a>
            </div>

            <!-- Статистика -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Студенты:</span>
                        <span class="font-medium">{{ $course->students_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Уроки:</span>
                        <span class="font-medium">{{ $course->lessons_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Рейтинг:</span>
                        <span class="font-medium">{{ $course->rating }} ⭐</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Длительность:</span>
                        <span class="font-medium">{{ $course->formatted_duration }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Обучение</h3>
                
                <a href="{{ route('student.learn', $course->id) }}" 
                   class="block w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 text-center font-medium mb-3 transition-colors">
                    <i class="fas fa-play mr-2"></i>
                    @if ($enrollment->progress_percentage > 0)
                        Продолжить обучение
                    @else
                        Начать обучение
                    @endif
                </a>
                
                @if ($enrollment->is_completed)
                    <div class="text-center text-green-600 font-medium mb-3">
                        <i class="fas fa-trophy mr-1"></i>Курс завершен!
                    </div>
                @endif
                
                <button type="button" 
                        @click="showDeleteModal = true"
                        class="block w-full bg-red-50 text-red-600 px-4 py-2 rounded-lg hover:bg-red-100 text-center font-medium transition-colors">
                    <i class="fas fa-trash-alt mr-2"></i>Удалить курс из библиотеки
                </button>
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
                            <div class="text-sm text-gray-600">{{ $course->category->courses_count }} курсов</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Отзывы -->
    @if ($course->visibleReviews->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Отзывы</h2>
            <div class="space-y-4">
                @foreach ($course->visibleReviews as $review)
                    <div class="border-b pb-4 last:border-0">
                        <div class="flex items-start mb-2">
                            @if ($review->user->avatar)
                                <img src="{{ asset('storage/' . $review->user->avatar) }}" 
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
                                    <div class="flex text-yellow-400">
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
                                    {{ $review->formatted_created_at }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Модальное окно подтверждения удаления -->
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Удаление курса
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Вы уверены, что хотите удалить курс <span class="font-bold text-gray-800">{{ $course->title }}</span> из вашей библиотеки? 
                                    <br><br>
                                    <strong class="text-red-600">Внимание:</strong> При удалении курса прогресс обучения будет безвозвратно утерян, а курс будет удален без возврата денежных средств.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST" action="{{ route('student.unenroll', $course->id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Да, удалить курс
                        </button>
                    </form>
                    <button type="button" @click="showDeleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
