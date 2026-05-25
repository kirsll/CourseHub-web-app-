@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Оформление заказа</h1>
        <p class="text-gray-600 mt-2">Оформите доступ к курсу "{{ $course->title }}"</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Основная форма -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <form action="{{ route('student.payment.process', $course->id) }}" method="POST">
                    @csrf
                    <!-- Информация о курсе -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-semibold text-gray-900 mb-2">Информация о курсе</h3>
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $course->title }}</h4>
                                <p class="text-sm text-gray-600">{{ $course->description }}</p>
                            </div>
                            <div class="text-right">
                                @if ($course->discount_price && $course->discount_price < $course->price)
                                    <div>
                                        <span class="text-gray-400 line-through text-sm">{{ $course->formatted_price }}</span>
                                        <div class="text-xl font-bold text-blue-600">{{ $course->formatted_current_price }}</div>
                                    </div>
                                @else
                                    <div class="text-xl font-bold text-blue-600">{{ $course->formatted_current_price }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Способ оплаты -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Способ оплаты</h3>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="card" checked class="mr-3">
                                <div>
                                    <div class="font-medium">Банковская карта</div>
                                    <div class="text-sm text-gray-600">Visa, Mastercard, МИР</div>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="yookassa" class="mr-3">
                                <div>
                                    <div class="font-medium">ЮKassa</div>
                                    <div class="text-sm text-gray-600">Быстрая оплата онлайн</div>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="robokassa" class="mr-3">
                                <div>
                                    <div class="font-medium">Robokassa</div>
                                    <div class="text-sm text-gray-600">Надежные платежи</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Промокод (заглушка) -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Промокод</h3>
                        <div class="flex">
                            <input type="text" name="coupon_code" placeholder="Введите промокод" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-r-md hover:bg-gray-300">
                                Применить
                            </button>
                        </div>
                    </div>

                    <!-- Кнопка оплаты -->
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 font-medium">
                        Оплатить {{ $course->formatted_current_price }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Боковая панель с итогами -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                <h3 class="font-semibold text-gray-900 mb-4">Итого к оплате</h3>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Курс:</span>
                        <span class="font-medium">{{ $course->formatted_price }}</span>
                    </div>
                    
                    @if ($course->discount_price && $course->discount_price < $course->price)
                        <div class="flex justify-between text-green-600">
                            <span>Скидка:</span>
                            <span>-{{ number_format($course->price - $course->discount_price, 2, '.', ' ') }} ₽</span>
                        </div>
                    @endif
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-lg font-semibold">Итого:</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $course->formatted_current_price }}</span>
                    </div>
                </div>

                <!-- Информация о безопасности -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-shield-alt text-blue-600 mt-1 mr-3"></i>
                        <div class="text-sm text-gray-700">
                            <div class="font-medium text-gray-900 mb-1">Безопасная оплата</div>
                            <p>Ваши данные защищены и не передаются третьим лицам. Доступ к курсу открывается сразу после успешной оплаты.</p>
                        </div>
                    </div>
                </div>

                <!-- Поддержка -->
                <div class="mt-4 text-center">
                    <p class="text-sm text-gray-600 mb-2">Нужна помощь?</p>
                    <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Связаться с поддержкой
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
