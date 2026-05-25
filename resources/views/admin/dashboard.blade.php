@extends('layouts.app')

@section('title', 'Админ-панель')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Админ-панель</h1>
        <p class="text-gray-600 mt-2">Управление системой и статистика</p>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Пользователи</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\User::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-book text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Курсы</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Course::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-graduation-cap text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Записи</p>
                    <p class="text-2xl font-bold text-gray-900">{{ App\Models\Enrollment::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-ruble-sign text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Доходы</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format(0, 0, '.', ' ') }} ₽
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Пользователи</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.users') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-users mr-2"></i> Все пользователи
                </a>
                <a href="{{ route('admin.users.create') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-user-plus mr-2"></i> Добавить пользователя
                </a>
                <a href="{{ route('admin.roles') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-user-shield mr-2"></i> Роли
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Курсы</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.courses') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-book mr-2"></i> Курсы
                </a>
                <a href="{{ route('admin.categories') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-tags mr-2"></i> Категории
                </a>
                <a href="{{ route('admin.reviews') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-star mr-2"></i> Отзывы
                </a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Финансы</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.payments') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-ruble-sign mr-2"></i> Платежи
                </a>
                <a href="{{ route('admin.orders') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-file-invoice mr-2"></i> Заказы
                </a>
                <a href="{{ route('admin.reports.sales') }}" class="block text-blue-600 hover:text-blue-800">
                    <i class="fas fa-chart-line mr-2"></i> Отчеты
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow px-6 py-4 mb-8">
        <div class="flex flex-col items-center justify-center flex-wrap gap-3">
            <div class="text-lg font-semibold text-gray-900">Система</div>
            <div class="flex flex-wrap gap-8 text-sm">
                <a href="{{ route('admin.db') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-database mr-2"></i> Режим БД
                </a>
                <a href="{{ route('admin.analytics') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-chart-pie mr-2"></i> Аналитика
                </a>
                <a href="{{ route('admin.settings') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-cog mr-2"></i> Настройки
                </a>
            </div>
        </div>
    </div>

    <!-- Последние действия -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Последние действия</h2>
        </div>
        <div class="p-6">
            <div class="text-center py-8">
                <i class="fas fa-history text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">Система логирования действий находится в разработке</p>
            </div>
        </div>
    </div>
</div>
@endsection
