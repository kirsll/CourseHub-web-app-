@extends('layouts.app')

@section('title', 'Оплата отменена')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center">
        <!-- Иконка отмены -->
        <div class="mb-8">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                <i class="fas fa-times text-red-600 text-3xl"></i>
            </div>
        </div>

        <!-- Заголовок -->
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Оплата отменена</h1>
        <p class="text-gray-600 mb-8">Платеж не был завершен. Вы можете повторить попытку.</p>

        <!-- Кнопки действий -->
        <div class="flex justify-center space-x-4">
            <button onclick="history.back()" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
                Повторить оплату
            </button>
            <a href="{{ route('courses.index') }}" 
               class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 font-medium">
                Каталог курсов
            </a>
        </div>
    </div>

    <!-- Дополнительная информация -->
    <div class="mt-12 bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Что произошло?</h2>
        <div class="space-y-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-medium text-gray-900 mb-1">Платеж был отменен</h3>
                    <p class="text-sm text-gray-600">Вы отменили платеж или произошла ошибка при обработке. Деньги не были списаны с вашего счета.</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-shield-alt text-green-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-medium text-gray-900 mb-1">Безопасность гарантирована</h3>
                    <p class="text-sm text-gray-600">Ваши финансовые данные защищены. Никаких списаний не произошло.</p>
                </div>
            </div>
            <div class="flex items-start">
                <i class="fas fa-headset text-blue-600 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-medium text-gray-900 mb-1">Нужна помощь?</h3>
                    <p class="text-sm text-gray-600">Если у вас возникли проблемы с оплатой, свяжитесь с нашей службой поддержки.</p>
                </div>
            </div>
        </div>

        <!-- Кнопка поддержки -->
        <div class="mt-6 text-center">
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">
                Связаться с поддержкой
            </a>
        </div>
    </div>
</div>
@endsection
