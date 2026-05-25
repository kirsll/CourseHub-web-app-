@extends('layouts.app')

@section('title', 'Курсы')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Курсы</h1>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Курс</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Преподаватель</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Категория</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($courses as $course)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                            <div class="text-xs text-gray-500">{{ $course->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $course->instructor?->full_name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $course->category?->name ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($course->is_published)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Опубликован</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Черновик</span>
                            @endif

                            @if ($course->is_featured)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">Рекомендуемый</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <a href="{{ route('admin.courses.show', $course) }}" class="text-blue-600 hover:text-blue-800">Открыть</a>

                            <details class="inline-block text-left ml-4">
                                <summary class="cursor-pointer text-gray-700 hover:text-gray-900">Статус</summary>
                                <div class="mt-2 p-4 bg-gray-50 border rounded-lg w-72">
                                    <form action="{{ route('admin.courses.status', $course) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PUT')

                                        <label class="flex items-center justify-between text-sm">
                                            <span>Опубликован</span>
                                            <input type="hidden" name="is_published" value="0">
                                            <input type="checkbox" name="is_published" value="1" @checked($course->is_published)>
                                        </label>

                                        <label class="flex items-center justify-between text-sm">
                                            <span>Рекомендуемый</span>
                                            <input type="hidden" name="is_featured" value="0">
                                            <input type="checkbox" name="is_featured" value="1" @checked($course->is_featured)>
                                        </label>

                                        <div class="flex justify-end">
                                            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-lg hover:bg-black text-sm">Сохранить</button>
                                        </div>
                                    </form>
                                </div>
                            </details>

                            <form action="{{ route('admin.courses.delete', $course) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 ml-4" onclick="return confirm('Удалить курс?')">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $courses->links() }}
    </div>
</div>
@endsection
