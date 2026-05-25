@extends('layouts.app')

@section('title', 'Мои заказы')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Мои заказы</h1>
        <p class="text-gray-600 mt-2">История ваших заказов и платежей</p>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Все статусы</option>
                    <option value="pending">Ожидает оплаты</option>
                    <option value="paid">Оплачен</option>
                    <option value="cancelled">Отменен</option>
                    <option value="refunded">Возвращен</option>
                </select>
            </div>
            <div class="flex-1">
                <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex-1">
                <input type="text" placeholder="Поиск по номеру заказа..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Список заказов -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Заказ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Дата
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Курс
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Сумма
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    #{{ $order->order_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $order->created_at->format('d.m.Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $order->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @foreach ($order->enrollments as $enrollment)
                                        <div>{{ $enrollment->course->title }}</div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ number_format($order->final_amount, 2, '.', ' ') }} ₽
                                </div>
                                @if ($order->discount_amount > 0)
                                    <div class="text-xs text-gray-500 line-through">
                                        {{ number_format($order->total_amount, 2, '.', ' ') }} ₽
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if ($order->status === 'paid')
                                        bg-green-100 text-green-800
                                    @elseif ($order->status === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif ($order->status === 'cancelled')
                                        bg-red-100 text-red-800
                                    @elseif ($order->status === 'refunded')
                                        bg-gray-100 text-gray-800
                                    @else
                                        bg-blue-100 text-blue-800
                                    @endif
                                ">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button type="button" onclick="showOrderDetails({{ $order->id }})" class="text-blue-600 hover:text-blue-900 mr-3" title="Детали заказа">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if ($order->status === 'pending')
                                    <a href="{{ route('student.checkout', $order->enrollments->first()->course_id) }}" class="text-green-600 hover:text-green-900 mr-3" title="Оплатить">
                                        <i class="fas fa-credit-card"></i>
                                    </a>
                                @endif
                                @if ($order->status === 'paid')
                                    <a href="#" onclick="alert('Функция скачивания чека в разработке'); return false;" class="text-gray-600 hover:text-gray-900" title="Скачать чек">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-receipt text-gray-300 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Заказы не найдены</h3>
                                <p class="text-gray-600">У вас пока нет заказов</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Пагинация -->
    @if ($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif

    <!-- Детали заказа (модальное окно) -->
    <div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Детали заказа</h3>
                    <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="orderDetails" class="space-y-4">
                    <!-- Содержимое будет загружено через JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function showOrderDetails(orderId) {
    fetch(`/student/orders/${orderId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('orderModal');
            const details = document.getElementById('orderDetails');
            
            details.innerHTML = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Номер заказа</label>
                        <p class="text-gray-900">#${data.order_number}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Дата создания</label>
                        <p class="text-gray-900">${data.created_at}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Статус</label>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-${data.status_color}-100 text-${data.status_color}-800">
                            ${data.status_label}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Курсы</label>
                        <div class="space-y-2">
                            ${data.courses.map(course => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-sm">${course.title}</span>
                                    <span class="text-sm font-medium">${course.price}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Итоговая сумма</label>
                        <p class="text-lg font-bold text-gray-900">${data.final_amount} ₽</p>
                    </div>
                    
                    ${data.payments.length > 0 ? `
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Платежи</label>
                            <div class="space-y-2">
                                ${data.payments.map(payment => `
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <span class="text-sm">${payment.method}</span>
                                        <span class="text-sm font-medium">${payment.amount} ₽</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
            
            modal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function closeOrderModal() {
    document.getElementById('orderModal').classList.add('hidden');
}

// Фильтрация заказов
document.addEventListener('DOMContentLoaded', function() {
    const filters = document.querySelectorAll('select, input[type="date"], input[type="text"]');
    
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            
            if (this.value && this.value !== 'all') {
                params.set(this.name, this.value);
            } else {
                params.delete(this.name);
            }
            
            window.location.href = `${url.pathname}?${params.toString()}`;
        });
    });
});
</script>
@endsection
