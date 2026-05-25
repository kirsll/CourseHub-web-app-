@extends('layouts.app')

@section('title', 'Создание курса')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Создание курса</h1>
        <p class="text-gray-600 mt-2">Заполните информацию о курсе</p>
    </div>

    <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Основная информация -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Основная информация</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Название курса *
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Название курса">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Категория *
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите категорию</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Краткое описание *
                </label>
                <textarea id="description" name="description" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Краткое описание курса"></textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Полное описание *
                </label>
                <textarea id="content" name="content" rows="6" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Полное описание курса"></textarea>
                @error('content')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Медиа -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Медиа</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                        Обложка курса
                    </label>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG или GIF. Макс. 2MB</p>
                    @error('thumbnail')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="preview_video" class="block text-sm font-medium text-gray-700 mb-2">
                        Видео-превью
                    </label>
                    <input type="url" id="preview_video" name="preview_video"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="URL видео (YouTube, Vimeo)">
                    @error('preview_video')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Цена -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Цена</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Цена *
                    </label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Цена со скидкой
                    </label>
                    <input type="number" id="discount_price" name="discount_price" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                    @error('discount_price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Детали курса -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Детали курса</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">
                        Уровень *
                    </label>
                    <select id="level" name="level" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите уровень</option>
                        <option value="beginner">Начальный</option>
                        <option value="intermediate">Средний</option>
                        <option value="advanced">Продвинутый</option>
                    </select>
                    @error('level')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                        Язык *
                    </label>
                    <select id="language" name="language" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Выберите язык</option>
                        <option value="ru">Русский</option>
                        <option value="en">English</option>
                        <option value="es">Español</option>
                    </select>
                    @error('language')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Длительность (минуты) *
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" min="1" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                    @error('duration_minutes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Дополнительная информация</h2>
            
            <div class="mb-6">
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">
                    Требования
                </label>
                <div id="requirements-container" class="space-y-2">
                    <div class="requirement-item flex items-center space-x-2">
                        <input type="text" name="requirements[]" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Введите требование">
                        <button type="button" onclick="addRequirement()" 
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Добавьте требования к студентам</p>
                @error('requirements')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="what_you_will_learn" class="block text-sm font-medium text-gray-700 mb-2">
                    Что вы изучите
                </label>
                <div id="what-you-will-learn-container" class="space-y-2">
                    <div class="learn-item flex items-center space-x-2">
                        <input type="text" name="what_you_will_learn[]" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Введите что изучат студенты">
                        <button type="button" onclick="addLearnItem()" 
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Добавьте что изучат студенты</p>
                @error('what_you_will_learn')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-2">
                    Целевая аудитория
                </label>
                <div id="target-audience-container" class="space-y-2">
                    <div class="audience-item flex items-center space-x-2">
                        <input type="text" name="target_audience[]" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Введите целевую аудиторию">
                        <button type="button" onclick="addAudienceItem()" 
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-1">Добавьте целевую аудиторию</p>
                @error('target_audience')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Кнопки -->
        <div class="flex justify-between">
            <a href="{{ route('instructor.courses') }}" 
               class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Отмена
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i>
                Создать курс
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function addRequirement() {
    const container = document.getElementById('requirements-container');
    const newItem = document.createElement('div');
    newItem.className = 'requirement-item flex items-center space-x-2';
    newItem.innerHTML = `
        <input type="text" name="requirements[]" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
               placeholder="Введите требование">
        <button type="button" onclick="removeRequirement(this)" 
                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(newItem);
}

function removeRequirement(button) {
    button.parentElement.remove();
}

function addLearnItem() {
    const container = document.getElementById('what-you-will-learn-container');
    const newItem = document.createElement('div');
    newItem.className = 'learn-item flex items-center space-x-2';
    newItem.innerHTML = `
        <input type="text" name="what_you_will_learn[]" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
               placeholder="Введите что изучат студенты">
        <button type="button" onclick="removeLearnItem(this)" 
                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(newItem);
}

function removeLearnItem(button) {
    button.parentElement.remove();
}

function addAudienceItem() {
    const container = document.getElementById('target-audience-container');
    const newItem = document.createElement('div');
    newItem.className = 'audience-item flex items-center space-x-2';
    newItem.innerHTML = `
        <input type="text" name="target_audience[]" 
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
               placeholder="Введите целевую аудиторию">
        <button type="button" onclick="removeAudienceItem(this)" 
                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(newItem);
}

function removeAudienceItem(button) {
    button.parentElement.remove();
}
</script>
@endsection

</html>
