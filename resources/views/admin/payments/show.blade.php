@extends('layouts.app')

@section('title', 'Платеж')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Платеж #{{ $payment->id }}</h1>
            <div class="text-sm text-gray-500">{{ $payment->created_at?->format('d.m.Y H:i') }}</div>
        </div>
        <a href="{{ route('admin.payments') }}" class="text-blue-600 hover:text-blue-800">Назад</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Детали</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-gray-500">Сумма</div>
                        <div class="text-gray-900 font-semibold">{{ number_format((float) ($payment->amount ?? 0), 2, '.', ' ') }} ₽</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-gray-500">Статус</div>
                        <div class="text-gray-900 font-semibold">{{ $payment->status }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-gray-500">Комиссия</div>
                        <div class="text-gray-900 font-semibold">{{ number_format((float) ($payment->commission ?? 0), 2, '.', ' ') }} ₽</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="text-gray-500">Доход преподавателя</div>
                        <div class="text-gray-900 font-semibold">{{ number_format((float) ($payment->instructor_earnings ?? 0), 2, '.', ' ') }} ₽</div>
                    </div>
                </div>

                <div class="mt-4 text-sm text-gray-700">
                    Gateway: {{ $payment->payment_gateway ?? '—' }}<br>
                    Transaction: {{ $payment->transaction_id ?? '—' }}<br>
                    Paid at: {{ $payment->paid_at?->format('d.m.Y H:i') ?? '—' }}
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Gateway response</h2>
                <pre class="text-xs bg-gray-50 p-4 rounded-lg overflow-x-auto">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Заказ</h2>
                <div class="text-sm text-gray-900">#{{ $payment->order?->id ?? '—' }}</div>
                <div class="text-sm text-gray-600">{{ $payment->order?->user?->full_name ?? '—' }}</div>
                <div class="text-sm text-gray-600">{{ $payment->order?->user?->email ?? '' }}</div>
                @if ($payment->order)
                    <div class="mt-3">
                        <a class="text-blue-600 hover:text-blue-800 text-sm" href="{{ route('admin.orders.show', $payment->order) }}">Открыть заказ</a>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Курс</h2>
                <div class="text-sm text-gray-900">{{ $payment->course?->title ?? '—' }}</div>
                <div class="text-sm text-gray-600">{{ $payment->course?->instructor?->full_name ?? '' }}</div>
                @if ($payment->course)
                    <div class="mt-3">
                        <a class="text-blue-600 hover:text-blue-800 text-sm" href="{{ route('admin.courses.show', $payment->course) }}">Открыть курс</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
