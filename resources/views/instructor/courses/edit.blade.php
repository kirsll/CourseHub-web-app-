@extends('layouts.app')

@section('title', 'Редактирование курса')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Редактирование курса</h1>
        <p class="text-gray-600 mt-2">Измените информацию о курсе</p>
    </div>

    <form action="{{ route('instructor.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- Основная информация -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Основная информация</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Название курса *
                    </label>
                    <input type="text" id="title" name="title" value="{{ $course->title }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                            <option value="{{ $category->id }}" {{ $course->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
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
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $course->description }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Полное описание *
                </label>
                <textarea id="content" name="content" rows="6" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ $course->content }}</textarea>
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
                    @if ($course->thumbnail)
                        <div class="mt-2">
                            <img src="{{ asset('public/storage/' . $course->thumbnail) }}" 
                                 alt="Текущая обложка" 
                                 class="w-32 h-24 object-cover rounded">
                        </div>
                    @endif
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG или GIF. Макс. 2MB</p>
                    @error('thumbnail')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="preview_video" class="block text-sm font-medium text-gray-700 mb-2">
                        Видео-превью
                    </label>
                    <input type="url" id="preview_video" name="preview_video" value="{{ $course->preview_video }}"
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
                           value="{{ $course->price }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Цена со скидкой
                    </label>
                    <input type="number" id="discount_price" name="discount_price" step="0.01" min="0"
                           value="{{ $course->discount_price }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                        <option value="beginner" {{ $course->level === 'beginner' ? 'selected' : '' }}>Начальный</option>
                        <option value="intermediate" {{ $course->level === 'intermediate' ? 'selected' : '' }}>Средний</option>
                        <option value="advanced" {{ $course->level === 'advanced' ? 'selected' : '' }}>Продвинутый</option>
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
                        <option value="ru" {{ $course->language === 'ru' ? 'selected' : '' }}>Русский</option>
                        <option value="en" {{ $course->language === 'en' ? 'selected' : '' }}>English</option>
                        <option value="es" {{ $course->language === 'es' ? 'selected' : '' }}>Español</option>
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
                           value="{{ $course->duration_minutes }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Требования
                </label>
                <div id="requirements-container" class="space-y-2">
                    @if($course->requirements && count($course->requirements) > 0)
                        @foreach($course->requirements as $index => $req)
                            <div class="requirement-item flex items-center space-x-2">
                                <input type="text" name="requirements[]" value="{{ $req }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Введите требование">
                                @if($index === 0)
                                    <button type="button" onclick="addRequirement()" 
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                @else
                                    <button type="button" onclick="removeRequirement(this)" 
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="requirement-item flex items-center space-x-2">
                            <input type="text" name="requirements[]" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Введите требование">
                            <button type="button" onclick="addRequirement()" 
                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Добавьте требования к студентам</p>
                @error('requirements')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Что вы изучите
                </label>
                <div id="what-you-will-learn-container" class="space-y-2">
                    @if($course->what_you_will_learn && count($course->what_you_will_learn) > 0)
                        @foreach($course->what_you_will_learn as $index => $learn)
                            <div class="learn-item flex items-center space-x-2">
                                <input type="text" name="what_you_will_learn[]" value="{{ $learn }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Введите что изучат студенты">
                                @if($index === 0)
                                    <button type="button" onclick="addLearnItem()" 
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                @else
                                    <button type="button" onclick="removeLearnItem(this)" 
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="learn-item flex items-center space-x-2">
                            <input type="text" name="what_you_will_learn[]" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Введите что изучат студенты">
                            <button type="button" onclick="addLearnItem()" 
                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Добавьте что изучат студенты</p>
                @error('what_you_will_learn')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Целевая аудитория
                </label>
                <div id="target-audience-container" class="space-y-2">
                    @if($course->target_audience && count($course->target_audience) > 0)
                        @foreach($course->target_audience as $index => $audience)
                            <div class="audience-item flex items-center space-x-2">
                                <input type="text" name="target_audience[]" value="{{ $audience }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="Введите целевую аудиторию">
                                @if($index === 0)
                                    <button type="button" onclick="addAudienceItem()" 
                                            class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                @else
                                    <button type="button" onclick="removeAudienceItem(this)" 
                                            class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="audience-item flex items-center space-x-2">
                            <input type="text" name="target_audience[]" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Введите целевую аудиторию">
                            <button type="button" onclick="addAudienceItem()" 
                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    @endif
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
            <div class="flex space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>
                    Сохранить изменения
                </button>
                @if (!$course->is_published)
                    <a href="{{ route('instructor.courses.publish', $course) }}" 
                       class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                        <i class="fas fa-eye mr-2"></i>
                        Опубликовать
                    </a>
                @else
                    <a href="{{ route('instructor.courses.unpublish', $course) }}" 
                       class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-eye-slash mr-2"></i>
                        Снять с публикации
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
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
