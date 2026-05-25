@extends('layouts.app')

@section('title', 'Аналитика')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Аналитика</h1>
        <p class="text-gray-600 mt-2">Детальная статистика по вашим курсам</p>
    </div>

    <!-- Общая статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Всего курсов</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $courses->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Всего студентов</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $analytics['total_students'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Средний рейтинг</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($analytics['average_rating'] ?? 0, 1) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-ruble-sign text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Общий доход</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($analytics['total_revenue'] ?? 0, 0, '.', ' ') }} ₽
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Графики и таблицы -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- График записей -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Записи за последние 30 дней</h2>
            <div class="h-64 flex items-end justify-between">
                @for ($i = 0; $i < 30; $i++)
                    <div class="w-2 bg-blue-200 rounded-t" style="height: {{ rand(20, 100) }}%"></div>
                @endfor
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-600">
                <span>1</span>
                <span>7</span>
                <span>14</span>
                <span>21</span>
                <span>30</span>
            </div>
        </div>

        <!-- График доходов -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Доходы за последние 30 дней</h2>
            <div class="h-64 flex items-end justify-between">
                @for ($i = 0; $i < 30; $i++)
                    <div class="w-2 bg-green-200 rounded-t" style="height: {{ rand(10, 80) }}%"></div>
                @endfor
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-600">
                <span>1</span>
                <span>7</span>
                <span>14</span>
                <span>21</span>
                <span>30</span>
            </div>
        </div>
    </div>

    <!-- Таблица курсов -->
    <div class="bg-white rounded-lg shadow mt-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Статистика по курсам</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Курс
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Студенты
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Рейтинг
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Доход
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($courses as $course)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if ($course->thumbnail)
                                        <img src="{{ asset('public/storage/' . $course->thumbnail) }}" 
                                             alt="{{ $course->title }}" 
                                             class="h-10 w-10 rounded-lg mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <i class="fas fa-book text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $course->category->name ?? 'Без категории' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $course->enrollments->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    {{ $course->rating }}
                                    <div class="flex text-yellow-400 ml-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($course->rating))
                                                <i class="fas fa-star text-xs"></i>
                                            @else
                                                <i class="far fa-star text-xs"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($course->price * $course->enrollments->count(), 0, '.', ' ') }} ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($course->is_published)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Опубликован
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Черновик
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
