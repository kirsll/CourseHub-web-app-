@extends('layouts.app')

@section('title', 'Студенты курса')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Студенты курса</h1>
                <p class="text-gray-600 mt-2">Управление студентами курса "{{ $course->title }}"</p>
            </div>
            <a href="{{ route('instructor.courses.edit', $course) }}" 
               class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                <i class="fas fa-arrow-left mr-2"></i> К курсу
            </a>
        </div>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Всего студентов</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $enrollments->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-graduation-cap text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Завершили</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $enrollments->whereNotNull('completed_at')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Средний прогресс</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($enrollments->avg('progress_percentage'), 1) }}%
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
                    <p class="text-sm text-gray-600">Общий доход</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($enrollments->sum('paid_amount'), 0, '.', ' ') }} ₽
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form action="{{ route('instructor.courses.students', $course) }}" method="GET" class="flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по имени или email" 
                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="">Все статусы</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Завершенные</option>
                </select>
                <select name="sort" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                    <option value="date_desc" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>По дате записи (новые)</option>
                    <option value="date_asc" {{ request('sort') === 'date_asc' ? 'selected' : '' }}>По дате записи (старые)</option>
                    <option value="progress_desc" {{ request('sort') === 'progress_desc' ? 'selected' : '' }}>По прогрессу (убыв)</option>
                    <option value="progress_asc" {{ request('sort') === 'progress_asc' ? 'selected' : '' }}>По прогрессу (возраст)</option>
                    <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>По имени</option>
                </select>
                <button type="submit" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i> Выгрузить
            </button>
        </form>
    </div>

    <!-- Таблица студентов -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Студент
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Дата записи
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Прогресс
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Оплачено
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($enrollments as $enrollment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if ($enrollment->user->avatar)
                                        <img src="{{ asset('storage/' . $enrollment->user->avatar) }}" 
                                             alt="{{ $enrollment->user->full_name }}" 
                                             class="h-10 w-10 rounded-full mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-600 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $enrollment->user->full_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $enrollment->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $enrollment->enrolled_at->format('d.m.Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 mr-3">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, max(0, (float) $enrollment->progress_percentage)) }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-900">
                                        {{ number_format($enrollment->progress_percentage, 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($enrollment->paid_amount, 0, '.', ' ') }} ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($enrollment->completed_at)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Завершен
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        В процессе
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('instructor.courses.student.progress', [$course, $enrollment->user]) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    <button class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Пагинация -->
    <div class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-700">
            Показано от 1 до {{ $enrollments->count() }} из {{ $enrollments->count() }} результатов
        </div>
    </div>
</div>
@endsection
