@extends('layouts.app')

@section('title', 'Аналитика')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Аналитика</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Рост пользователей (30 дней)</h2>
            <div class="space-y-2 text-sm">
                @foreach ($stats['users_growth'] as $row)
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ \Illuminate\Support\Carbon::parse($row->date)->format('d.m') }}</span>
                        <span class="text-gray-900 font-medium">{{ $row->count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Новые курсы (30 дней)</h2>
            <div class="space-y-2 text-sm">
                @foreach ($stats['courses_growth'] as $row)
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ \Illuminate\Support\Carbon::parse($row->date)->format('d.m') }}</span>
                        <span class="text-gray-900 font-medium">{{ $row->count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Доход по месяцам</h2>
            <div class="space-y-2 text-sm">
                @foreach ($stats['revenue_monthly'] as $row)
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ \Illuminate\Support\Carbon::parse($row->month)->format('m.Y') }}</span>
                        <span class="text-gray-900 font-medium">{{ number_format((float) $row->revenue, 2, '.', ' ') }} ₽</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
