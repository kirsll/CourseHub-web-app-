@extends('layouts.app')

@section('title', 'Доходы')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Доходы</h1>
        <p class="text-gray-600 mt-2">Управление финансовыми показателями</p>
    </div>

    <!-- Фильтры периода -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Период</h2>
            </div>
            <div class="flex space-x-4">
                <select class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option>Последние 30 дней</option>
                    <option>Последние 3 месяца</option>
                    <option>Последние 6 месяцев</option>
                    <option>Последний год</option>
                    <option>Все время</option>
                </select>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Применить
                </button>
            </div>
        </div>
    </div>

    <!-- Общая статистика -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-ruble-sign text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Общий доход</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format(150000, 0, '.', ' ') }} ₽
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">За период</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format(45000, 0, '.', ' ') }} ₽
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
                    <p class="text-sm text-gray-600">Средний в месяц</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format(15000, 0, '.', ' ') }} ₽
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-percentage text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Комиссия платформы</p>
                    <p class="text-2xl font-bold text-gray-900">15%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- График доходов -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">График доходов</h2>
        <div class="h-64 flex items-end justify-between">
            @for ($i = 0; $i < 12; $i++)
                <div class="w-8 bg-green-200 rounded-t" style="height: {{ rand(30, 100) }}%"></div>
            @endfor
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-600">
            <span>Янв</span>
            <span>Фев</span>
            <span>Мар</span>
            <span>Апр</span>
            <span>Май</span>
            <span>Июн</span>
            <span>Июл</span>
            <span>Авг</span>
            <span>Сен</span>
            <span>Окт</span>
            <span>Ноя</span>
            <span>Дек</span>
        </div>
    </div>

    <!-- Таблица транзакций -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900">История транзакций</h2>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-download mr-2"></i> Выгрузить отчет
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Дата
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Курс
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Студент
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Сумма
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Комиссия
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Доход
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for ($i = 0; $i < 10; $i++)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ now()->subDays(rand(1, 30))->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Laravel 10 для начинающих
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Студент {{ $i + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format(rand(2000, 10000), 0, '.', ' ') }} ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format(rand(300, 1500), 0, '.', ' ') }} ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format(rand(1700, 8500), 0, '.', ' ') }} ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Завершено
                                </span>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Пагинация -->
    <div class="mt-6 flex justify-between items-center">
        <div class="text-sm text-gray-700">
            Показано от 1 до 10 из 156 результатов
        </div>
        <div class="flex space-x-2">
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                Предыдущая
            </button>
            <button class="px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-50">
                Следующая
            </button>
        </div>
    </div>
</div>
@endsection
