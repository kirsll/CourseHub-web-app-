@extends('layouts.app')

@section('title', 'Настройки')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Настройки</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Название сайта</label>
                <input name="site_name" value="{{ old('site_name', config('app.name')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                @error('site_name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                <textarea name="site_description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('site_description') }}</textarea>
                @error('site_description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email для связи</label>
                <input type="email" name="contact_email" value="{{ old('contact_email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                @error('contact_email')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Комиссия (%)</label>
                <input type="number" step="0.01" name="commission_rate" value="{{ old('commission_rate', 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                @error('commission_rate')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg hover:bg-black">Сохранить</button>
            </div>
        </form>
    </div>
</div>
@endsection
