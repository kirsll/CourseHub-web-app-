@extends('layouts.app')

@section('title', 'Управление модулями курса')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Заголовок -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Модули курса</h1>
            <p class="text-gray-600 mt-2">{{ $course->title }}</p>
        </div>
        <a href="{{ route('instructor.courses') }}" 
           class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200">
            <i class="fas fa-arrow-left mr-2"></i>
            К курсам
        </a>
    </div>

    <!-- Информация о курсе -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $course->title }}</h2>
                <p class="text-gray-600">{{ $course->description }}</p>
                <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                    <span><i class="fas fa-book mr-1"></i> {{ $course->modules->count() }} модулей</span>
                    <span><i class="fas fa-video mr-1"></i> {{ $course->lessons->count() }} уроков</span>
                    <span><i class="fas fa-users mr-1"></i> {{ $course->enrollments_count ?? 0 }} студентов</span>
                </div>
            </div>
            <div>
                @if ($course->is_published)
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-check mr-1"></i> Опубликован
                    </span>
                @else
                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-edit mr-1"></i> Черновик
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Добавление модуля -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Добавить новый модуль</h2>
        <form action="{{ route('instructor.modules.store', $course) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Название модуля *
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Например: Введение в PHP">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Описание
                    </label>
                    <input type="text" id="description" name="description"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Краткое описание модуля">
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>
                    Добавить модуль
                </button>
            </div>
        </form>
    </div>

    <!-- Список модулей -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Список модулей</h2>
        </div>
        
        @if ($course->modules->count() > 0)
            <div class="divide-y">
                @foreach ($course->modules->sortBy('sort_order') as $module)
                    <div class="p-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                                        @if ($module->description)
                                            <p class="text-gray-600 text-sm">{{ $module->description }}</p>
                                        @endif
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                            <span><i class="fas fa-video mr-1"></i> {{ $module->lessons->count() }} уроков</span>
                                            <span><i class="fas fa-clock mr-1"></i> {{ $module->formatted_duration }}</span>
                                            <span><i class="fas fa-list-ol mr-1"></i> Порядок: {{ $module->sort_order }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('instructor.lessons', $module) }}" 
                                   class="bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200 text-sm">
                                    <i class="fas fa-video mr-1"></i>
                                    Уроки
                                </a>
                                <button onclick="editModule({{ $module->id }})" 
                                        class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 text-sm">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteModule({{ $module->id }})" 
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
                <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Нет модулей</h3>
                <p class="text-gray-600 mb-4">Добавьте первый модуль для структурирования курса</p>
            </div>
        @endif
    </div>
</div>

@section('scripts')
<script>
function editModule(moduleId) {
    // Здесь можно добавить логику редактирования модуля
    alert('Функция редактирования модуля будет добавлена');
}

function deleteModule(moduleId) {
    if (confirm('Вы уверены, что хотите удалить этот модуль? Все уроки также будут удалены.')) {
        // Создаем форму для удаления
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/instructor/modules/${moduleId}`;
        
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
