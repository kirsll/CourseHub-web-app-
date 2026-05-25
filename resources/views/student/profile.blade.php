@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Профиль</h1>
        <p class="text-gray-600 mt-2">Управляйте своей учетной записью</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Основная информация -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Основная информация</h2>
                
                <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Аватар -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Фото профиля</label>
                        <div class="flex items-center space-x-4">
                            @if ($student->avatar)
                                <img src="{{ asset('public/storage/' . $student->avatar) }}" 
                                     alt="{{ $student->full_name }}" 
                                     class="w-20 h-20 rounded-full object-cover">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-2xl"></i>
                                </div>
                            @endif
                            <div>
                                <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">
                                <label for="avatar" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 cursor-pointer">
                                    <i class="fas fa-camera mr-2"></i>
                                    Изменить фото
                                </label>
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG или GIF. Макс. 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Имя и фамилия -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Имя
                            </label>
                            <input type="text" id="first_name" name="first_name" value="{{ $student->first_name }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Фамилия
                            </label>
                            <input type="text" id="last_name" name="last_name" value="{{ $student->last_name }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ $student->email }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Телефон -->
                    <div class="mb-4">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Телефон
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ $student->phone }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- О себе -->
                    <div class="mb-6">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                            О себе
                        </label>
                        <textarea id="bio" name="bio" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Расскажите о себе...">{{ $student->bio }}</textarea>
                        @error('bio')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Социальные сети -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Социальные сети
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <input type="url" name="social_links[github]" value="{{ $student->social_links['github'] ?? '' }}"
                                       placeholder="GitHub" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <input type="url" name="social_links[linkedin]" value="{{ $student->social_links['linkedin'] ?? '' }}"
                                       placeholder="LinkedIn" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <input type="url" name="social_links[twitter]" value="{{ $student->social_links['twitter'] ?? '' }}"
                                       placeholder="Twitter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <input type="url" name="social_links[website]" value="{{ $student->social_links['website'] ?? '' }}"
                                       placeholder="Веб-сайт" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div class="flex space-x-4">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                            Сохранить изменения
                        </button>
                        <a href="{{ route('student.dashboard') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200">
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="lg:col-span-1">
            <!-- Статистика -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Статистика</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Курсов:</span>
                        <span class="font-medium">{{ $student->enrollments()->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Завершено:</span>
                        <span class="font-medium">{{ $student->enrollments()->whereNotNull('completed_at')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Сертификатов:</span>
                        <span class="font-medium">{{ $student->certificates()->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Часов обучения:</span>
                        <span class="font-medium">{{ round($student->lessonProgress()->sum('watch_time_seconds') / 3600, 1) }}</span>
                    </div>
                </div>
            </div>

            <!-- Роль -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Роль</h3>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                        @if ($student->isStudent())
                            <i class="fas fa-user-graduate text-blue-600"></i>
                        @elseif ($student->isInstructor())
                            <i class="fas fa-chalkboard-teacher text-blue-600"></i>
                        @elseif ($student->isAdmin())
                            <i class="fas fa-cog text-blue-600"></i>
                        @endif
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $student->role->display_name }}</div>
                        <div class="text-sm text-gray-600">{{ $student->role->name }}</div>
                    </div>
                </div>
            </div>

            <!-- Активность -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Активность</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Дата регистрации:</span>
                        <span class="font-medium">{{ $student->created_at->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Последний вход:</span>
                        <span class="font-medium">{{ $student->last_login_at ? $student->last_login_at->format('d.m.Y H:i') : 'Никогда' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Баланс:</span>
                        <span class="font-medium">{{ number_format($student->balance, 2, '.', ' ') }} ₽</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
