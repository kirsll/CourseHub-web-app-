@extends('layouts.app')

@section('title', 'Дашборд преподавателя')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Дашборд преподавателя</h1>
        <p class="text-gray-600 mt-2">Управляйте своими курсами и отслеживайте успехи студентов</p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Всего курсов</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-eye text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Опубликовано</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['published_courses'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Студенты</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-ruble-sign text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Доходы</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($stats['total_earnings'], 0, '.', ' ') }} ₽
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Мои курсы -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Мои курсы</h2>
                    <a href="{{ route('instructor.courses') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        Все курсы →
                    </a>
                </div>
                <div class="p-6">
                    @forelse ($recentCourses as $course)
                        <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100 last:border-0">
                            <div class="flex items-center">
                                @if ($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                         alt="{{ $course->title }}" 
                                         class="w-16 h-16 rounded-lg mr-4">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                        <i class="fas fa-book text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-gray-900">
                                        <a href="{{ route('instructor.courses.edit', $course) }}" 
                                           class="hover:text-blue-600">
                                            {{ $course->title }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="text-sm text-gray-600">
                                            <i class="fas fa-users mr-1"></i> {{ $course->enrollments->count() ?? 0 }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            <i class="fas fa-star mr-1"></i> {{ $course->rating }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            <i class="fas fa-ruble-sign mr-1"></i> {{ number_format($course->price, 0, '.', ' ') }}
                                        </span>
                                    </div>
                                    <div class="mt-2">
                                        @if ($course->is_published)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i> Опубликован
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i> Черновик
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('instructor.courses.edit', $course) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('instructor.courses.students', $course) }}" 
                                   class="text-purple-600 hover:text-purple-800">
                                    <i class="fas fa-users"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-book-open text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500 mb-4">У вас пока нет курсов</p>
                            <a href="{{ route('instructor.courses.create') }}" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                Создать курс
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Доходы за месяц -->
            <div class="bg-white rounded-lg shadow mt-8">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Доходы за месяц</h2>
                    <a href="{{ route('instructor.earnings') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">
                        Детально →
                    </a>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ number_format($stats['monthly_earnings'], 0, '.', ' ') }} ₽
                            </p>
                            <p class="text-sm text-gray-600">За последние 30 дней</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Всего доходов</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ number_format($stats['total_earnings'], 0, '.', ' ') }} ₽
                            </p>
                        </div>
                    </div>
                    
                    <!-- График доходов (упрощенный) -->
                    <div class="mt-6">
                        <div class="flex items-end justify-between h-32">
                            <div class="w-8 bg-blue-200 rounded-t" style="height: 60%"></div>
                            <div class="w-8 bg-blue-200 rounded-t" style="height: 80%"></div>
                            <div class="w-8 bg-blue-200 rounded-t" style="height: 45%"></div>
                            <div class="w-8 bg-blue-200 rounded-t" style="height: 90%"></div>
                            <div class="w-8 bg-blue-600 rounded-t" style="height: 70%"></div>
                            <div class="w-8 bg-blue-200 rounded-t" style="height: 55%"></div>
                            <div class="w-8 bg-blue-200 rounded-t" style="height: 85%"></div>
                        </div>
                        <div class="flex justify-between mt-2 text-xs text-gray-600">
                            <span>Пн</span>
                            <span>Вт</span>
                            <span>Ср</span>
                            <span>Чт</span>
                            <span>Пт</span>
                            <span>Сб</span>
                            <span>Вс</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Быстрые действия -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Быстрые действия</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('instructor.courses.create') }}" 
                           class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> Создать курс
                        </a>
                        <a href="{{ route('instructor.analytics') }}" 
                           class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-chart-line mr-2"></i> Аналитика
                        </a>
                        <a href="{{ route('instructor.earnings') }}" 
                           class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-ruble-sign mr-2"></i> Доходы
                        </a>
                    </div>
                </div>
            </div>

            <!-- Последние записи -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Последние записи</h2>
                </div>
                <div class="p-6">
                    @forelse ($recentEnrollments as $enrollment)
                        <div class="flex items-center mb-4">
                            @if ($enrollment->user->avatar)
                                <img src="{{ asset('storage/' . $enrollment->user->avatar) }}" 
                                     alt="{{ $enrollment->user->full_name }}" 
                                     class="w-10 h-10 rounded-full mr-3">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm">
                                    {{ $enrollment->user->full_name }}
                                </h4>
                                <p class="text-xs text-gray-600">
                                    {{ $enrollment->course->title }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $enrollment->created_at ? $enrollment->created_at->diffForHumans() : 'Неизвестно' }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">Записей не найдено</p>
                    @endforelse
                    
                    @if ($recentEnrollments->count() > 0)
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                            Все записи →
                        </a>
                    @endif
                </div>
            </div>

            <!-- Советы -->
            <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-3">
                    <i class="fas fa-lightbulb mr-2"></i> Советы
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                        <p class="text-sm text-blue-800">
                            Добавьте превью видео для увеличения конверсии
                        </p>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                        <p class="text-sm text-blue-800">
                            Регулярно обновляйте контент курсов
                        </p>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mt-1 mr-2"></i>
                        <p class="text-sm text-blue-800">
                            Отвечайте на вопросы студентов в комментариях
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
