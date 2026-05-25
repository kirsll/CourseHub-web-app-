@extends('layouts.app')

@section('title', 'Схема БД')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Схема БД</h1>
            <div class="text-sm text-gray-500">Driver: {{ $driver }}</div>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('admin.db') }}" class="text-blue-600 hover:text-blue-800">Данные</a>
            <a href="{{ route('admin.db.schema') }}" class="text-gray-900 font-semibold">Схема</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        @if (count($relations) === 0)
            <div class="text-sm text-gray-600">Связи (foreign keys) не найдены</div>
        @else
            <div class="space-y-6">
                @foreach ($byTable as $table => $items)
                    <div class="border rounded-lg">
                        <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                            <div class="font-semibold text-gray-900">{{ $table }}</div>
                            <a href="{{ route('admin.db', ['table' => $table]) }}" class="text-blue-600 hover:text-blue-800 text-sm">Открыть данные</a>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Колонка</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ссылается на</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Constraint</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($items as $rel)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $rel['column'] }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">
                                                <a class="text-blue-600 hover:text-blue-800" href="{{ route('admin.db', ['table' => $rel['ref_table']]) }}">
                                                    {{ $rel['ref_table'] }}.{{ $rel['ref_column'] }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $rel['constraint'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Пояснительная памятка</h2>

        <div class="space-y-8">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Описание таблиц</h3>
                <div class="text-sm text-gray-600 mb-4">
                    Ниже приведено описание таблиц, ключевых ограничений и основных полей. Описание формируется автоматически из схемы БД.
                </div>

                <div class="space-y-4">
                    @foreach ($tables as $table)
                        @php($info = $tableInfo[$table] ?? null)
                        <div class="border rounded-lg">
                            <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                                <div class="font-semibold text-gray-900">{{ $table }}</div>
                                <a href="{{ route('admin.db', ['table' => $table]) }}" class="text-blue-600 hover:text-blue-800 text-sm">Открыть данные</a>
                            </div>
                            <div class="p-4">
                                <div class="text-sm text-gray-700">{{ $info['description'] ?? 'Служебная/прикладная таблица проекта.' }}</div>

                                <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="text-gray-500">Primary key</div>
                                        <div class="text-gray-900 font-medium">
                                            @if (!empty($info['pk']))
                                                {{ implode(', ', $info['pk']) }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="text-gray-500">Unique</div>
                                        <div class="text-gray-900 font-medium">
                                            @if (!empty($info['unique']))
                                                {{ implode(', ', $info['unique']) }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                    <div class="p-3 bg-gray-50 rounded-lg">
                                        <div class="text-gray-500">Редактирование</div>
                                        <div class="text-gray-900 font-medium">
                                            {{ !empty($info['has_id']) ? 'CRUD доступен (id найден)' : 'Только просмотр' }}
                                        </div>
                                    </div>
                                </div>

                                @if (!empty($info['columns']))
                                    <details class="mt-4">
                                        <summary class="cursor-pointer text-sm text-blue-600 hover:text-blue-800">Показать колонки</summary>
                                        <div class="mt-3 overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-white">
                                                    <tr>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Колонка</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NULL</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Default</th>
                                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach ($info['columns'] as $col)
                                                        <tr>
                                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $col['name'] ?? '' }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $col['type'] ?? '' }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ !empty($col['nullable']) ? 'YES' : 'NO' }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ ($col['default'] ?? null) === null ? '—' : (string) $col['default'] }}</td>
                                                            <td class="px-4 py-2 text-sm text-gray-700">{{ $col['key'] ?? '—' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </details>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Триггеры</h3>
                @if (empty($triggers))
                    <div class="text-sm text-gray-600">Триггеры не найдены</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Имя</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Таблица</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timing</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($triggers as $t)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $t['name'] }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            <a class="text-blue-600 hover:text-blue-800" href="{{ route('admin.db', ['table' => $t['table']]) }}">{{ $t['table'] }}</a>
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $t['timing'] ?? '—' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $t['event'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Функции / процедуры</h3>
                @if (empty($routines))
                    <div class="text-sm text-gray-600">Функции/процедуры не найдены</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Имя</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($routines as $r)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-700">{{ $r['type'] }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $r['name'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
