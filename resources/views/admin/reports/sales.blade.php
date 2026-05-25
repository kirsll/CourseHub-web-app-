@extends('layouts.app')

@section('title', 'Отчет по продажам')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Отчет по продажам</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.reports.sales') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Дата начала</label>
                <input type="date" name="start_date" value="{{ \Illuminate\Support\Carbon::parse($startDate)->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Дата конца</label>
                <input type="date" name="end_date" value="{{ \Illuminate\Support\Carbon::parse($endDate)->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div class="md:col-span-2 flex justify-end">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg hover:bg-black">Показать</button>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-500">Общий доход</div>
                <div class="text-xl font-bold text-gray-900">{{ number_format((float) $totalRevenue, 2, '.', ' ') }} ₽</div>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-500">Комиссия</div>
                <div class="text-xl font-bold text-gray-900">{{ number_format((float) $totalCommission, 2, '.', ' ') }} ₽</div>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-500">Доход преподавателей</div>
                <div class="text-xl font-bold text-gray-900">{{ number_format((float) $totalInstructorEarnings, 2, '.', ' ') }} ₽</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Курс</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Покупатель</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Сумма</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Комиссия</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Доход препода</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($sales as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $payment->paid_at?->format('d.m.Y H:i') ?? $payment->created_at?->format('d.m.Y H:i') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="text-gray-900 font-medium">{{ $payment->course?->title ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->course?->instructor?->full_name ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="text-gray-900">{{ $payment->order?->user?->full_name ?? '—' }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->order?->user?->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format((float) ($payment->amount ?? 0), 2, '.', ' ') }} ₽
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format((float) ($payment->commission ?? 0), 2, '.', ' ') }} ₽
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format((float) ($payment->instructor_earnings ?? 0), 2, '.', ' ') }} ₽
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $sales->links() }}
    </div>
</div>
@endsection
