@extends('layouts.app')

@section('title', 'Мои курсы')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Мои курсы</h1>
            <p class="text-gray-600 mt-2">Управляйте своими курсами</p>
        </div>
        <a href="{{ route('instructor.courses.create') }}" 
           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>
            Создать курс
        </a>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Всего курсов</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $courses->total() }}</p>
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
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $courses->where('is_published', true)->count() }}
                    </p>
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
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $courses->sum('enrollments_count') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Средний рейтинг</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format($courses->avg('rating'), 1) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Все статусы</option>
                    <option value="published">Опубликованные</option>
                    <option value="draft">Черновики</option>
                    <option value="archived">Архивные</option>
                </select>
            </div>
            <div class="flex-1">
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Все категории</option>
                    <option value="programming">Программирование</option>
                    <option value="design">Дизайн</option>
                    <option value="marketing">Маркетинг</option>
                </select>
            </div>
            <div class="flex-1">
                <input type="text" placeholder="Поиск курсов..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Список курсов -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($courses as $course)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <!-- Изображение курса -->
                <div class="relative">
                    @if ($course->thumbnail)
                        <img src="{{ asset('public/storage/' . $course->thumbnail) }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-48 object-cover rounded-t-lg">
                    @else
                        <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                            <i class="fas fa-book text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    
                    <!-- Статус -->
                    <div class="absolute top-2 left-2 flex space-x-2">
                        @if ($course->is_published)
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-check mr-1"></i> Опубликован
                            </span>
                        @else
                            <span class="bg-gray-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-edit mr-1"></i> Черновик
                            </span>
                        @endif
                        @if ($course->is_featured)
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-star mr-1"></i> Хит
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Информация о курсе -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">
                        <a href="{{ route('instructor.courses.edit', $course) }}" 
                           class="hover:text-blue-600">
                            {{ $course->title }}
                        </a>
                    </h3>
                    
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ \Illuminate\Support\Str::limit($course->description, 100) }}
                    </p>
                    
                    <!-- Статистика -->
                    <div class="flex items-center justify-between mb-3 text-sm text-gray-600">
                        <span>
                            <i class="fas fa-users mr-1"></i> {{ $course->enrollments_count }}
                        </span>
                        <span>
                            <i class="fas fa-star mr-1"></i> {{ $course->rating }}
                        </span>
                        <span>
                            <i class="fas fa-ruble-sign mr-1"></i> {{ number_format($course->price, 0, '.', ' ') }}
                        </span>
                    </div>
                    
                    <!-- Прогресс -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600">Модули</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $course->modules_count ?? 0 }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                        </div>
                    </div>
                    
                    <!-- Кнопки действий -->
                    <div class="flex space-x-2">
                        <a href="{{ route('instructor.courses.edit', $course) }}" 
                           class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 text-center">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('instructor.modules', $course) }}" 
                           class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded text-sm hover:bg-gray-200 text-center">
                            <i class="fas fa-th-list"></i>
                        </a>
                        @if ($course->is_published)
                            <a href="{{ route('instructor.courses.unpublish', $course) }}" 
                               class="flex-1 bg-yellow-100 text-yellow-700 px-3 py-2 rounded text-sm hover:bg-yellow-200 text-center"
                               onclick="return confirm('Вы уверены, что хотите снять курс с публикации?')">
                                <i class="fas fa-eye-slash"></i>
                            </a>
                        @else
                            <a href="{{ route('instructor.courses.publish', $course) }}" 
                               class="flex-1 bg-green-100 text-green-700 px-3 py-2 rounded text-sm hover:bg-green-200 text-center"
                               onclick="return confirm('Вы уверены, что хотите опубликовать курс?')">
                                <i class="fas fa-eye"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-book-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">У вас пока нет курсов</h3>
                <p class="text-gray-600 mb-4">Создайте свой первый курс</p>
                <a href="{{ route('instructor.courses.create') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Создать курс
                </a>
            </div>
        @endforelse
    </div>

    <!-- Пагинация -->
    @if ($courses->hasPages())
        <div class="mt-8">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
