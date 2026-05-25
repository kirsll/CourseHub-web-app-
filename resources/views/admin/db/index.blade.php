@extends('layouts.app')

@section('title', 'Режим БД')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Режим БД</h1>
            <div class="text-sm text-gray-500">Driver: {{ $driver }}</div>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <a href="{{ route('admin.db') }}" class="text-gray-900 font-semibold">Данные</a>
            <a href="{{ route('admin.db.schema') }}" class="text-blue-600 hover:text-blue-800">Схема</a>
            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">Назад</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm font-semibold text-gray-900 mb-3">Таблицы</div>
                <div class="space-y-1 max-h-[70vh] overflow-auto">
                    @foreach ($tables as $table)
                        <a
                            href="{{ route('admin.db', ['table' => $table]) }}"
                            class="block px-3 py-2 rounded-lg text-sm {{ $selectedTable === $table ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-50' }}"
                        >
                            {{ $table }}
                        </a>
                    @endforeach
                    @if (count($tables) === 0)
                        <div class="text-sm text-gray-600">Таблицы не найдены</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <div class="text-lg font-semibold text-gray-900">{{ $selectedTable ?? '—' }}</div>
                        @if ($selectedTable)
                            <div class="text-sm text-gray-500">Колонок: {{ count($columns) }}</div>
                        @endif
                    </div>
                    @if ($selectedTable && $isEditable)
                        <a href="{{ route('admin.db.create', $selectedTable) }}" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-black text-sm">
                            Добавить
                        </a>
                    @endif
                </div>

                <div class="p-6">
                    @if (!$selectedTable)
                        <div class="text-sm text-gray-600">Выбери таблицу слева</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        @foreach ($columns as $col)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $col }}</th>
                                        @endforeach
                                        @if ($isEditable)
                                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($rows as $row)
                                        <tr>
                                            @foreach ($columns as $col)
                                                <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                                    @php($value = $row->{$col} ?? null)
                                                    @if (is_null($value))
                                                        <span class="text-gray-400">NULL</span>
                                                    @elseif (is_bool($value))
                                                        {{ $value ? 'true' : 'false' }}
                                                    @elseif (is_scalar($value))
                                                        {{ \Illuminate\Support\Str::limit((string) $value, 120) }}
                                                    @else
                                                        {{ \Illuminate\Support\Str::limit(json_encode($value, JSON_UNESCAPED_UNICODE), 120) }}
                                                    @endif
                                                </td>
                                            @endforeach

                                            @if ($isEditable)
                                                <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                                    @if (isset($row->id))
                                                        <a href="{{ route('admin.db.edit', ['table' => $selectedTable, 'id' => $row->id]) }}" class="text-blue-600 hover:text-blue-800">Редактировать</a>
                                                        <form action="{{ route('admin.db.destroy', ['table' => $selectedTable, 'id' => $row->id]) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-800 ml-4" onclick="return confirm('Удалить запись?')">Удалить</button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400">—</span>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $rows->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
