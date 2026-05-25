@extends('layouts.app')

@section('title', 'Отзывы курса')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Отзывы курса</h1>
                <p class="text-gray-600 mt-2">Управление отзывами курса "{{ $course->title }}"</p>
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
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Средний рейтинг</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->rating }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-comment text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Всего отзывов</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $reviews->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Опубликованные</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $reviews->where('is_visible', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-eye-slash text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Скрытые</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $reviews->where('is_visible', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex space-x-4">
                <select class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option>Все отзывы</option>
                    <option>Опубликованные</option>
                    <option>Скрытые</option>
                </select>
                <select class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option>Все рейтинги</option>
                    <option>5 звезд</option>
                    <option>4 звезды</option>
                    <option>3 звезды</option>
                    <option>2 звезды</option>
                    <option>1 звезда</option>
                </select>
                <select class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option>Сортировка</option>
                    <option>По дате</option>
                    <option>По рейтингу</option>
                </select>
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i> Выгрузить отчет
            </button>
        </div>
    </div>

    <!-- Список отзывов -->
    <div class="space-y-6">
        @foreach ($reviews as $review)
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        @if ($review->user->avatar)
                            <img src="{{ asset('public/storage/' . $review->user->avatar) }}" 
                                 alt="{{ $review->user->full_name }}" 
                                 class="h-12 w-12 rounded-full mr-4">
                        @else
                            <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        @endif
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <div class="font-medium text-gray-900">{{ $review->user->full_name }}</div>
                                <div class="flex text-yellow-400 ml-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            @if ($review->comment)
                                <p class="text-gray-600 mt-2">{{ $review->comment }}</p>
                            @endif
                            <div class="text-xs text-gray-500 mt-2">
                                {{ $review->created_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        @if ($review->is_visible)
                            <button class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        @else
                            <button class="text-green-600 hover:text-green-800">
                                <i class="fas fa-eye"></i>
                            </button>
                        @endif
                        <button class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Пагинация -->
    <div class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-700">
            Показано от 1 до {{ $reviews->count() }} из {{ $reviews->count() }} результатов
        </div>
    </div>
</div>
@endsection
