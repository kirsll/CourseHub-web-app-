@extends('layouts.app')

@section('title', 'Редактировать запись')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Редактировать запись</h1>
            <div class="text-sm text-gray-500">Таблица: {{ $table }} · ID: {{ $id }}</div>
        </div>
        <a href="{{ route('admin.db', ['table' => $table]) }}" class="text-blue-600 hover:text-blue-800">Назад</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.db.update', ['table' => $table, 'id' => $id]) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            @foreach ($columns as $col)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $col }}</label>
                    
                    @if(isset($options[$col]))
                        <select name="{{ $col }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">-- Выберите значение --</option>
                            @foreach($options[$col] as $val => $label)
                                <option value="{{ $val }}" {{ old($col, $row->{$col} ?? '') == (string)$val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        @php
                            $inputType = 'text';
                            if (str_contains($col, 'date') || str_contains($col, '_at')) $inputType = 'datetime-local';
                            elseif (str_contains($col, 'price') || str_contains($col, 'amount') || str_contains($col, 'id') || str_contains($col, 'order')) $inputType = 'number';
                            
                            $val = old($col, $row->{$col} ?? '');
                            // Format datetime-local string if needed
                            if ($inputType === 'datetime-local' && $val && is_string($val)) {
                                $val = date('Y-m-d\TH:i', strtotime($val));
                            }
                        @endphp
                        <input type="{{ $inputType }}" name="{{ $col }}" value="{{ $val }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" {{ $inputType === 'number' && str_contains($col, 'price') ? 'step="0.01"' : '' }}>
                    @endif

                    @if(isset($descriptions[$col]))
                        <div class="text-gray-500 text-xs mt-1">{{ $descriptions[$col] }}</div>
                    @endif

                    @error($col)<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg hover:bg-black">Сохранить</button>
            </div>
        </form>

        <form action="{{ route('admin.db.destroy', ['table' => $table, 'id' => $id]) }}" method="POST" class="mt-6">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full bg-red-600 text-white px-5 py-2 rounded-lg hover:bg-red-700" onclick="return confirm('Удалить запись?')">
                Удалить
            </button>
        </form>
    </div>
</div>
@endsection
