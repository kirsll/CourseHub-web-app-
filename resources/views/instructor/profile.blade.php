@extends('layouts.app')

@section('title', 'Профиль преподавателя')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Профиль преподавателя</h1>
        <p class="text-gray-600 mt-2">Управление личной информацией и настройками</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Основная информация -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Личная информация</h2>
                
                <form action="{{ route('instructor.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Имя
                            </label>
                            <input type="text" id="first_name" name="first_name" required
                                   value="{{ $instructor->first_name }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Фамилия
                            </label>
                            <input type="text" id="last_name" name="last_name" required
                                   value="{{ $instructor->last_name }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email" required
                               value="{{ $instructor->email }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Изменение email потребует подтверждения</p>
                    </div>

                    <div class="mb-6">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Телефон
                        </label>
                        <input type="tel" id="phone" name="phone"
                               value="{{ $instructor->phone }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-6">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                            О себе
                        </label>
                        <textarea id="bio" name="bio" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Расскажите о себе и своем опыте...">{{ $instructor->bio }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                            Аватар
                        </label>
                        <div class="flex items-center space-x-6">
                            <div class="shrink-0">
                                @if ($instructor->avatar)
                                    <img src="{{ asset('public/storage/' . $instructor->avatar) }}" 
                                         alt="{{ $instructor->full_name }}" 
                                         class="h-16 w-16 rounded-full object-cover">
                                @else
                                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600 text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" id="avatar" name="avatar" accept="image/*"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG или GIF. Максимальный размер 2MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Статистика -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Курсы:</span>
                        <span class="font-medium">{{ $instructor->courses->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Студенты:</span>
                        <span class="font-medium">{{ $instructor->courses->sum(function($c) { return $c->enrollments->count(); }) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Рейтинг:</span>
                        <span class="font-medium">4.8 ⭐</span>
                    </div>
                </div>
            </div>

            <!-- Быстрые ссылки -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Быстрые ссылки</h3>
                <div class="space-y-2">
                    <a href="{{ route('instructor.dashboard') }}" 
                       class="block text-blue-600 hover:text-blue-800">
                        <i class="fas fa-tachometer-alt mr-2"></i> Дашборд
                    </a>
                    <a href="{{ route('instructor.courses') }}" 
                       class="block text-blue-600 hover:text-blue-800">
                        <i class="fas fa-book mr-2"></i> Мои курсы
                    </a>
                    <a href="{{ route('instructor.analytics') }}" 
                       class="block text-blue-600 hover:text-blue-800">
                        <i class="fas fa-chart-line mr-2"></i> Аналитика
                    </a>
                    <a href="{{ route('instructor.earnings') }}" 
                       class="block text-blue-600 hover:text-blue-800">
                        <i class="fas fa-ruble-sign mr-2"></i> Доходы
                    </a>
                </div>
            </div>

            <!-- Уведомления -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Уведомления</h3>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" checked class="mr-2">
                        <span class="text-sm text-gray-700">Новые записи на курсы</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" checked class="mr-2">
                        <span class="text-sm text-gray-700">Новые отзывы</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="mr-2">
                        <span class="text-sm text-gray-700">Еженедельный отчет</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
