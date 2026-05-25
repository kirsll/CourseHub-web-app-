@extends('layouts.app')

@section('title', 'Главная страница')

@section('content')
<!-- Hero секция -->
<section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Обучайтесь онлайн с лучшими преподавателями
                </h1>
                <p class="text-xl mb-8 text-blue-100">
                    Более 1000 курсов по программированию, дизайну, маркетингу и бизнесу. Начните свое обучение сегодня!
                </p>
                <div class="flex space-x-4">
                    <a href="{{ route('courses.index') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                        Найти курсы
                    </a>
                    <a href="#" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                        Стать преподавателем
                    </a>
                </div>
            </div>
            <div class="hidden md:block">
                <!-- <img src="{{ asset('images/hero-image.svg') }}" alt="Online Learning" class="w-full h-auto"> -->
            </div>
        </div>
    </div>
</section>

<!-- Статистика -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">1000+</div>
                <div class="text-gray-600">Курсов</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">50 000+</div>
                <div class="text-gray-600">Студентов</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">200+</div>
                <div class="text-gray-600">Преподавателей</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-blue-600 mb-2">98%</div>
                <div class="text-gray-600">Довольных студентов</div>
            </div>
        </div>
    </div>
</section>

<!-- Популярные курсы -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Популярные курсы</h2>
            <p class="text-xl text-gray-600">Самые востребованные курсы нашей платформы</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse ($popularCourses as $course)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition">
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
                        
                        @if ($course->has_discount)
                            <span class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm">
                                -{{ $course->discount_percentage }}%
                            </span>
                        @endif
                    </div>
                    
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-1">{{ $course->category->name ?? 'Без категории' }}</div>
                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                            <a href="{{ route('courses.show', $course) }}" class="hover:text-blue-600">
                                {{ $course->title }}
                            </a>
                        </h3>
                        
                        <div class="flex items-center mb-2">
                            <div class="flex text-yellow-400">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $course->rating)
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
                        
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-users mr-1"></i> {{ $course->students_count }}
                            </div>
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-clock mr-1"></i> {{ $course->formatted_duration }}
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                @if ($course->has_discount)
                                    <span class="text-gray-400 line-through text-sm">{{ $course->formatted_price }}</span>
                                    <span class="text-blue-600 font-bold ml-2">{{ $course->formatted_current_price }}</span>
                                @else
                                    <span class="text-blue-600 font-bold">{{ $course->formatted_price }}</span>
                                @endif
                            </div>
                            <a href="{{ route('courses.show', $course) }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                Подробнее
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500">
                    Курсы не найдены
                </div>
            @endforelse
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('courses.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Смотреть все курсы
            </a>
        </div>
    </div>
</section>

<!-- Категории -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Категории курсов</h2>
            <p class="text-xl text-gray-600">Выберите интересующую вас область</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-auto lg:grid-cols-5 gap-4 justify-items-center">
            @forelse ($categories as $category)
                <a href="{{ route('courses.category', $category) }}" 
                   class="bg-gray-50 rounded-lg p-4 text-center hover:bg-blue-50 hover:border-blue-300 border-2 border-transparent transition w-full max-w-[12rem]">
                    @if ($category->icon)
                        <i class="{{ $category->icon }} text-3xl text-blue-600 mb-2"></i>
                    @else
                        <i class="fas fa-folder text-3xl text-blue-600 mb-2"></i>
                    @endif
                    <h3 class="font-semibold text-gray-900">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $category->published_courses_count }} курсов</p>
                </a>
            @empty
                <div class="col-span-full text-center text-gray-500">
                    Категории не найдены
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Преподаватели -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Наши преподаватели</h2>
            <p class="text-xl text-gray-600">Эксперты с практическим опытом</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($instructors as $instructor)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center mb-4">
                        @if ($instructor->avatar)
                            <img src="{{ asset('public/storage/' . $instructor->avatar) }}" 
                                 alt="{{ $instructor->full_name }}" 
                                 class="w-16 h-16 rounded-full mr-4">
                        @else
                            <div class="w-16 h-16 rounded-full bg-gray-300 flex items-center justify-center mr-4">
                                <i class="fas fa-user text-gray-600 text-xl"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $instructor->full_name }}</h3>
                            <p class="text-sm text-gray-600">{{ $instructor->courses_count }} курсов</p>
                        </div>
                    </div>
                    
                    @if ($instructor->bio)
                        <p class="text-gray-600 text-sm mb-4">{{ Str::limit($instructor->bio, 100) }}</p>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <div class="flex text-yellow-400">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-sm"></i>
                            @endfor
                        </div>
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm">
                            Смотреть курсы
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500">
                    Преподаватели не найдены
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- CTA секция -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Готовы начать обучение?</h2>
        <p class="text-xl mb-8 text-blue-100">
            Присоединяйтесь к тысячам студентов, которые уже меняют свою жизнь с нашими курсами
        </p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('register') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Начать бесплатно
            </a>
            <a href="{{ route('courses.index') }}" class="border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition">
                Изучить курсы
            </a>
        </div>
    </div>
</section>
@endsection
