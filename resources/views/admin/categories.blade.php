@extends('layouts.app')

@section('title', 'Категории')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Категории</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Добавить категорию</h2>
                <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Название</label>
                        <input name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                        @error('name')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input name="slug" value="{{ old('slug') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                        @error('slug')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Родительская категория</label>
                        <select name="parent_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">— Нет —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) old('parent_id') === (string) $cat->id)>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Иконка (class)</label>
                        <input name="icon" value="{{ old('icon') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="fas fa-code">
                        @error('icon')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Цвет</label>
                        <input name="color" value="{{ old('color') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="#3B82F6">
                        @error('color')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Порядок сортировки</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" min="0">
                        @error('sort_order')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                        <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('description') }}</textarea>
                        @error('description')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700">Создать</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категория</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Родитель</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($category->icon)
                                            <i class="{{ $category->icon }} text-blue-600"></i>
                                        @else
                                            <i class="fas fa-folder text-gray-400"></i>
                                        @endif
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                            <div class="text-xs text-gray-500">sort: {{ $category->sort_order }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $category->slug }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $category->parent?->name ?? '—' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <details class="inline-block text-left">
                                        <summary class="cursor-pointer text-blue-600 hover:text-blue-800">Редактировать</summary>
                                        <div class="mt-2 p-4 bg-gray-50 border rounded-lg w-96">
                                            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-3">
                                                @csrf
                                                @method('PUT')

                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Название</label>
                                                    <input name="name" value="{{ old('name', $category->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Slug</label>
                                                    <input name="slug" value="{{ old('slug', $category->slug) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Родитель</label>
                                                    <select name="parent_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                                        <option value="">— Нет —</option>
                                                        @foreach ($categories as $cat)
                                                            <option value="{{ $cat->id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $cat->id)>
                                                                {{ $cat->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Иконка</label>
                                                    <input name="icon" value="{{ old('icon', $category->icon) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Цвет</label>
                                                    <input name="color" value="{{ old('color', $category->color) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Сортировка</label>
                                                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" min="0">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Описание</label>
                                                    <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $category->description) }}</textarea>
                                                </div>

                                                <div class="flex justify-end">
                                                    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-black text-sm">Сохранить</button>
                                                </div>
                                            </form>
                                        </div>
                                    </details>

                                    <form action="{{ route('admin.categories.delete', $category) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 ml-4" onclick="return confirm('Удалить категорию?')">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
