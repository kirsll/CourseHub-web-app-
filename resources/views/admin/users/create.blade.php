@extends('layouts.app')

@section('title', 'Добавить пользователя')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Добавить пользователя</h1>
        <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800">Назад</a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                    <input name="first_name" value="{{ old('first_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    @error('first_name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Фамилия</label>
                    <input name="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    @error('last_name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                @error('email')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                @error('password')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Роль</label>
                <select name="role_id" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected((int) old('role_id') === $role->id)>
                            {{ $role->display_name ?? $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                    <input name="phone" value="{{ old('phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('phone')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Активен</label>
                    <select name="is_active" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="1" @selected(old('is_active', '1') === '1')>Да</option>
                        <option value="0" @selected(old('is_active') === '0')>Нет</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">О себе</label>
                <textarea name="bio" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('bio') }}</textarea>
                @error('bio')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">Создать</button>
            </div>
        </form>
    </div>
</div>
@endsection
