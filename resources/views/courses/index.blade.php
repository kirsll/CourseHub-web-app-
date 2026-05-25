@extends('layouts.app')

@section('title', 'Каталог курсов')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Заголовок -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Каталог курсов</h1>
        <p class="text-gray-600 mt-2">Найдите идеальный курс для вашего обучения</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Фильтры -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Фильтры</h2>
                
                <form action="{{ route('courses.index') }}" method="GET" class="space-y-6">
                    <!-- Поиск -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Поиск</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Название курса..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Категория -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Категория</label>
                        <select name="category" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Все категории</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->slug }}" 
                                        {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Уровень -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Уровень</label>
                        <select name="level" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Все уровни</option>
                            <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>
                                Начальный
                            </option>
                            <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>
                                Средний
                            </option>
                            <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>
                                Продвинутый
                            </option>
                        </select>
                    </div>

                    <!-- Цена -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Цена</label>
                        <select name="price" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Все цены</option>
                            <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>
                                Бесплатные
                            </option>
                            <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>
                                Платные
                            </option>
                        </select>
                    </div>

                    <!-- Рейтинг -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Минимальный рейтинг</label>
                        <select name="rating" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Любой рейтинг</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>
                                4+ ⭐⭐⭐⭐
                            </option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>
                                3+ ⭐⭐⭐
                            </option>
                        </select>
                    </div>

                    <!-- Сортировка -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Сортировка</label>
                        <select name="sort" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>
                                По популярности
                            </option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>
                                Новые
                            </option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                Старые
                            </option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>
                                Дешевле
                            </option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>
                                Дороже
                            </option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>
                                По рейтингу
                            </option>
                        </select>
                    </div>

                    <div class="flex space-x-2">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Применить
                        </button>
                        <a href="{{ route('courses.index') }}" 
                           class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 text-center">
                            Сброс
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Курсы -->
        <div class="lg:col-span-3">
            <!-- Результаты поиска -->
            @if (request('search'))
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-blue-800">
                        Найдено курсов: {{ $courses->total() }} по запросу "{{ request('search') }}"
                    </p>
                </div>
            @endif

            <!-- Список курсов -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($courses as $course)
                    <div class="course-card bg-white rounded-xl shadow-md transition flex flex-col h-full overflow-hidden border border-gray-100">
                        <!-- Изображение курса -->
                        <div class="relative img-wrapper h-48 flex-shrink-0">
                            @if ($course->thumbnail)
                                <img src="{{ asset('public/storage/' . $course->thumbnail) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                                    <i class="fas fa-book text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <!-- Бейджи -->
                            <div class="absolute top-2 left-2 flex space-x-2">
                                @if ($course->is_featured)
                                    <span class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                        <i class="fas fa-star mr-1"></i>Хит
                                    </span>
                                @endif
                                @if ($course->has_discount)
                                    <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">
                                        -{{ $course->discount_percentage }}%
                                    </span>
                                @endif
                            </div>
                            
                            <!-- Уровень -->
                            <div class="absolute top-2 right-2">
                                <span class="bg-gray-900 bg-opacity-75 text-white px-2 py-1 rounded text-xs">
                                    {{ $course->level_label ?? 'Не указан' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Информация о курса -->
                        <div class="p-5 flex flex-col flex-grow">
                            <!-- Категория -->
                            <div class="text-sm text-gray-500 mb-1">
                                {{ $course->category->name ?? 'Без категории' }}
                            </div>
                            
                            <!-- Название -->
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('courses.show', $course) }}" class="hover:text-blue-600">
                                    {{ $course->title }}
                                </a>
                            </h3>
                            
                            <!-- Описание -->
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                {{ \Illuminate\Support\Str::limit($course->description, 100) }}
                            </p>
                            
                            <!-- Рейтинг и студенты -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= floor($course->rating))
                                                <i class="fas fa-star text-sm"></i>
                                            @else
                                                <i class="far fa-star text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-600 ml-2">
                                        {{ $course->rating }} ({{ $course->reviews_count }})
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-users mr-1"></i>{{ $course->students_count }}
                                </div>
                            </div>
                            
                            <!-- Преподаватель -->
                            <div class="flex items-center mb-3">
                                @if ($course->instructor->avatar)
                                    <img src="{{ asset('public/storage/' . $course->instructor->avatar) }}" 
                                         alt="{{ $course->instructor->full_name }}" 
                                         class="w-6 h-6 rounded-full mr-2">
                                @else
                                    <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                        <i class="fas fa-user text-gray-600 text-xs"></i>
                                    </div>
                                @endif
                                <span class="text-sm text-gray-600">{{ $course->instructor->full_name }}</span>
                            </div>
                            
                            <!-- Длительность и уроки -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-clock mr-1"></i>{{ $course->formatted_duration }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-book-open mr-1"></i>{{ $course->lessons_count }} уроков
                                </div>
                            </div>
                            
                            <!-- Цена и кнопка -->
                            <div class="mt-auto pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    @if ($course->has_discount)
                                        <div class="flex flex-col">
                                            <span class="text-gray-400 line-through text-xs">
                                                {{ $course->formatted_price }}
                                            </span>
                                            <span class="text-blue-600 font-bold text-lg leading-none mt-1">
                                                {{ $course->formatted_current_price }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-blue-600 font-bold text-lg">
                                            {{ $course->formatted_current_price }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('courses.show', $course) }}" 
                                       class="btn bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 font-medium whitespace-nowrap">
                                        Подробнее
                                    </a>
                                    @if (auth()->check())
                                        <form action="{{ route('student.enroll', $course) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium whitespace-nowrap">
                                                Записаться
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('login') }}" 
                                           class="btn bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium whitespace-nowrap">
                                            Войти
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Курсы не найдены</h3>
                        <p class="text-gray-600 mb-4">Попробуйте изменить параметры фильтрации</p>
                        <a href="{{ route('courses.index') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Сбросить фильтры
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
    </div>
</div>
@endsection
