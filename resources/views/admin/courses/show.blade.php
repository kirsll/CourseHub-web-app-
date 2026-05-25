@extends('layouts.app')

@section('title', 'Курс')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
            <div class="text-sm text-gray-500">{{ $course->slug }}</div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.courses') }}" class="text-blue-600 hover:text-blue-800">Назад</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Описание</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! $course->content !!}
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Модули и уроки</h2>
                @foreach ($course->modules as $module)
                    <div class="mb-6">
                        <div class="text-sm font-semibold text-gray-900">{{ $module->title }}</div>
                        @if ($module->description)
                            <div class="text-sm text-gray-600 mt-1">{{ $module->description }}</div>
                        @endif
                        <div class="mt-3 space-y-2">
                            @foreach ($module->lessons as $lesson)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="text-sm text-gray-900">{{ $lesson->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $lesson->type }} · {{ $lesson->formatted_duration }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Студенты</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Студент</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Прогресс</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Статус</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($course->enrollments as $enrollment)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $enrollment->user->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $enrollment->formatted_progress }}</td>
                                    <td class="px-4 py-3">
                                        @if ($enrollment->is_completed)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Завершен</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">В процессе</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Информация</h2>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Преподаватель</span>
                        <span class="text-gray-900">{{ $course->instructor?->full_name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Категория</span>
                        <span class="text-gray-900">{{ $course->category?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Опубликован</span>
                        <span class="text-gray-900">{{ $course->is_published ? 'Да' : 'Нет' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Рекомендуемый</span>
                        <span class="text-gray-900">{{ $course->is_featured ? 'Да' : 'Нет' }}</span>
                    </div>
                </div>

                <div class="mt-6">
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

                <div class="mt-4">
                    <form action="{{ route('admin.courses.delete', $course) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-center bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700" onclick="return confirm('Удалить курс?')">Удалить курс</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
