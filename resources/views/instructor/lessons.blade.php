@extends('layouts.app')

@section('title', 'Управление уроками модуля')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Заголовок -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Уроки модуля</h1>
            <p class="text-gray-600 mt-2">{{ $module->title }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('instructor.modules', $course) }}" 
               class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>
                К модулям
            </a>
        </div>
    </div>

    <!-- Информация о модуле -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $module->title }}</h2>
                <p class="text-gray-600">{{ $module->description }}</p>
                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                    <span><i class="fas fa-video mr-1"></i> {{ $module->lessons->count() }} уроков</span>
                    <span><i class="fas fa-clock mr-1"></i> {{ $module->formatted_duration }}</span>
                    <span><i class="fas fa-list-ol mr-1"></i> Порядок: {{ $module->sort_order }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Добавление урока -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Добавить новый урок</h2>
        <form action="{{ route('instructor.lessons.store', $module) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Название урока *
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Например: Установка PHP">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Тип урока *
                    </label>
                    <select id="type" name="type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="video">Видео</option>
                        <option value="text">Текст</option>
                        <option value="quiz">Тест</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Длительность (минуты) *
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" required min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="30">
                    @error('duration_minutes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Порядковый номер
                    </label>
                    <input type="number" id="sort_order" name="sort_order" min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="{{ $module->lessons->count() + 1 }}">
                    @error('sort_order')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Описание урока
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Краткое описание урока"></textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-4">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Содержание урока
                </label>
                <textarea id="content" name="content" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Полное содержание урока"></textarea>
                @error('content')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Добавить урок
                </button>
            </div>
        </form>
    </div>

    <!-- Список уроков -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Список уроков</h2>
        </div>
        
        @if ($module->lessons->count() > 0)
            <div class="divide-y">
                @foreach ($module->lessons->sortBy('sort_order') as $lesson)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                                        @if ($lesson->type === 'video')
                                            <i class="fas fa-video"></i>
                                        @elseif ($lesson->type === 'text')
                                            <i class="fas fa-file-alt"></i>
                                        @else
                                            <i class="fas fa-question-circle"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $lesson->title }}</h3>
                                        @if ($lesson->description)
                                            <p class="text-gray-600 text-sm">{{ $lesson->description }}</p>
                                        @endif
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                            <span><i class="fas fa-clock mr-1"></i> {{ $lesson->duration_minutes }} мин</span>
                                            <span><i class="fas fa-list-ol mr-1"></i> Порядок: {{ $lesson->sort_order }}</span>
                                            @if ($lesson->is_published)
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-check mr-1"></i> Опубликован
                                                </span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">
                                                    <i class="fas fa-edit mr-1"></i> Черновик
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <button onclick="editLesson({{ $lesson->id }})" 
                                        class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 text-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteLesson({{ $lesson->id }})" 
                                        class="bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-video-slash text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Нет уроков</h3>
                <p class="text-gray-600 mb-4">Добавьте первый урок в этот модуль</p>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
function editLesson(lessonId) {
    // Здесь можно добавить логику редактирования урока
    alert('Функция редактирования урока будет добавлена');
}

function deleteLesson(lessonId) {
    if (confirm('Вы уверены, что хотите удалить этот урок?')) {
        // Создаем форму для удаления
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/instructor/lessons/${lessonId}`;
        
        // Добавляем CSRF токен
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Добавляем метод DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
