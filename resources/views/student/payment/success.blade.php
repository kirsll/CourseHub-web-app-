@extends('layouts.app')

@section('title', 'Оплата успешно')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center">
        <!-- Иконка успеха -->
        <div class="mb-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-check text-green-600 text-3xl"></i>
            </div>
        </div>

        <!-- Заголовок -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Оплата успешно завершена!</h1>
        <p class="text-gray-600 mb-8">Доступ к курсу открыт. Приятного обучения!</p>

        <!-- Кнопки действий -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('student.dashboard') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                Перейти к курсам
            </a>
            <a href="{{ route('courses.index') }}" 
               class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 font-medium">
                Каталог курсов
            </a>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="mt-12 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Что дальше?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-play text-blue-600"></i>
                </div>
                <h3 class="font-medium text-gray-900 mb-2">Начните обучение</h3>
                <p class="text-sm text-gray-600">Перейдите в раздел "Мои курсы" и начните изучать материалы</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-certificate text-blue-600"></i>
                </div>
                <h3 class="font-medium text-gray-900 mb-2">Получите сертификат</h3>
                <p class="text-sm text-gray-600">После завершения курса вы получите сертификат</p>
            </div>
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-headset text-blue-600"></i>
                </div>
                <h3 class="font-medium text-gray-900 mb-2">Поддержка</h3>
                <p class="text-sm text-gray-600">Наши преподаватели всегда готовы помочь</p>
            </div>
        </div>
    </div>
</div>
@endsection
