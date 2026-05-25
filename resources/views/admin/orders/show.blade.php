@extends('layouts.app')

@section('title', 'Заказ')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Заказ #{{ $order->id }}</h1>
            <div class="text-sm text-gray-500">{{ $order->created_at?->format('d.m.Y H:i') }}</div>
        </div>
        <a href="{{ route('admin.orders') }}" class="text-blue-600 hover:text-blue-800">Назад</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Платежи</h2>
                <div class="space-y-3">
                    @foreach ($order->payments as $payment)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-gray-900">#{{ $payment->id }} · {{ $payment->payment_gateway }}</div>
                                <div class="text-sm text-gray-700">{{ number_format((float) $payment->amount, 2, '.', ' ') }} ₽</div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">Статус: {{ $payment->status }} · Транзакция: {{ $payment->transaction_id ?? '—' }}</div>
                            <div class="text-xs text-gray-500">Курс: {{ $payment->course?->title ?? '—' }}</div>
                            <div class="mt-2">
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 text-sm">Открыть платеж</a>
                            </div>
                        </div>
                    @endforeach
                    @if ($order->payments->count() === 0)
                        <div class="text-sm text-gray-600">Платежей нет</div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Записи на курсы</h2>
                <div class="space-y-3">
                    @foreach ($order->enrollments as $enrollment)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm font-medium text-gray-900">{{ $enrollment->course?->title ?? '—' }}</div>
                            <div class="text-xs text-gray-500 mt-1">Прогресс: {{ $enrollment->formatted_progress }}</div>
                        </div>
                    @endforeach
                    @if ($order->enrollments->count() === 0)
                        <div class="text-sm text-gray-600">Записей нет</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Покупатель</h2>
                <div class="text-sm text-gray-900">{{ $order->user?->full_name ?? '—' }}</div>
                <div class="text-sm text-gray-600">{{ $order->user?->email ?? '' }}</div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Статус</h2>
                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @foreach (['pending','paid','cancelled','refunded'] as $status)
                            <option value="{{ $status }}" @selected($order->status === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-black">Сохранить</button>
                </form>

                <div class="mt-4 text-sm text-gray-700">
                    Сумма: <span class="font-semibold">{{ number_format((float) ($order->total_amount ?? 0), 2, '.', ' ') }} ₽</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
