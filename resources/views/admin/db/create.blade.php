@extends('layouts.app')

@section('title', 'Добавить запись')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Добавить запись</h1>
            <div class="text-sm text-gray-500">Таблица: {{ $table }}</div>
        </div>
        <a href="{{ route('admin.db', ['table' => $table]) }}" class="text-blue-600 hover:text-blue-800">Назад</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.db.store', ['table' => $table]) }}" method="POST" class="space-y-4">
            @csrf

            @foreach ($columns as $col)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $col }}</label>
                    
                    @if(isset($options[$col]))
                        <select name="{{ $col }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">-- Выберите значение --</option>
                            @foreach($options[$col] as $val => $label)
                                <option value="{{ $val }}" {{ old($col) == (string)$val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        @php
                            $inputType = 'text';
                            if (str_contains($col, 'date') || str_contains($col, '_at')) $inputType = 'datetime-local';
                            elseif (str_contains($col, 'price') || str_contains($col, 'amount') || str_contains($col, 'id') || str_contains($col, 'order')) $inputType = 'number';
                        @endphp
                        <input type="{{ $inputType }}" name="{{ $col }}" value="{{ old($col) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" {{ $inputType === 'number' && str_contains($col, 'price') ? 'step="0.01"' : '' }}>
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
    </div>
</div>
@endsection
