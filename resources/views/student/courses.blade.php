@extends('layouts.app')

@section('title', 'Мои курсы')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="{ showDeleteModal: false, courseIdToDelete: null, courseTitleToDelete: '' }">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Мои курсы</h1>
        <p class="text-gray-600 mt-2">Управляйте своими курсами и отслеживайте прогресс</p>
    </div>

    <!-- Фильтры -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center space-x-4">
            <div class="flex-1">
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Все курсы</option>
                    <option value="in_progress">В процессе</option>
                    <option value="completed">Завершенные</option>
                    <option value="not_started">Не начаты</option>
                </select>
            </div>
            <div class="flex-1">
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Все категории</option>
                    <option value="programming">Программирование</option>
                    <option value="design">Дизайн</option>
                    <option value="marketing">Маркетинг</option>
                </select>
            </div>
            <div class="flex-1">
                <input type="text" placeholder="Поиск курсов..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Список курсов -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($enrollments as $enrollment)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                <!-- Изображение курса -->
                <div class="relative">
                    @if ($enrollment->course->thumbnail)
                        <img src="{{ asset('storage/' . $enrollment->course->thumbnail) }}" 
                             alt="{{ $enrollment->course->title }}" 
                             class="w-full h-48 object-cover rounded-t-lg">
                    @else
                        <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center">
                            <i class="fas fa-book text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                    
                    <!-- Статус -->
                    <div class="absolute top-2 right-2">
                        @if ($enrollment->is_completed)
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-check mr-1"></i> Завершен
                            </span>
                        @elseif ($enrollment->progress_percentage > 0)
                            <span class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-play mr-1"></i> В процессе
                            </span>
                        @else
                            <span class="bg-gray-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-clock mr-1"></i> Не начат
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Информация о курсе -->
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-2">
                        <a href="{{ route('student.course', $enrollment->course) }}" 
                           class="hover:text-blue-600">
                            {{ $enrollment->course->title }}
                        </a>
                    </h3>
                    
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ \Illuminate\Support\Str::limit($enrollment->course->description, 100) }}
                    </p>
                    
                    <!-- Преподаватель -->
                    <div class="flex items-center mb-3">
                        @if ($enrollment->course->instructor->avatar)
                            <img src="{{ asset('storage/' . $enrollment->course->instructor->avatar) }}" 
                                 alt="{{ $enrollment->course->instructor->full_name }}" 
                                 class="w-6 h-6 rounded-full mr-2">
                        @else
                            <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                <i class="fas fa-user text-gray-600 text-xs"></i>
                            </div>
                        @endif
                        <span class="text-sm text-gray-600">{{ $enrollment->course->instructor->full_name }}</span>
                    </div>
                    
                    <!-- Прогресс -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-600">Прогресс</span>
                            <span class="text-sm font-medium text-gray-900">
                                {{ $enrollment->formatted_progress }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" 
                                 style="width: {{ min(100, max(0, (float) $enrollment->progress_percentage)) }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Статистика -->
                    <div class="flex items-center justify-between mb-3 text-sm text-gray-600">
                        <span>
                            <i class="fas fa-clock mr-1"></i> {{ $enrollment->course->formatted_duration }}
                        </span>
                        <span>
                            <i class="fas fa-book-open mr-1"></i> {{ $enrollment->course->lessons_count }} уроков
                        </span>
                    </div>
                    
                    <!-- Кнопки действий -->
                    <div class="flex space-x-2">
                        <a href="{{ route('student.learn', $enrollment->course) }}" 
                           class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 text-center">
                            @if ($enrollment->is_completed)
                                Повторить
                            @elseif ($enrollment->progress_percentage > 0)
                                Продолжить
                            @else
                                Начать
                            @endif
                        </a>
                        <a href="{{ route('student.course', $enrollment->course) }}" 
                           class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded text-sm hover:bg-gray-200 text-center">
                            Подробнее
                        </a>
                        <button type="button" 
                                @click="courseIdToDelete = {{ $enrollment->course->id }}; courseTitleToDelete = '{{ addslashes($enrollment->course->title) }}'; showDeleteModal = true"
                                class="bg-red-50 text-red-600 px-3 py-2 rounded text-sm hover:bg-red-100 text-center"
                                title="Удалить курс">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-book-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">У вас пока нет курсов</h3>
                <p class="text-gray-600 mb-4">Запишитесь на курсы, чтобы начать обучение</p>
                <a href="{{ route('courses.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Найти курсы
                </a>
            </div>
        @endforelse
    </div>

    <!-- Пагинация -->
    @if ($enrollments->hasPages())
        <div class="mt-8">
            {{ $enrollments->links() }}
        </div>
    @endif

    <!-- Модальное окно подтверждения удаления -->
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="showDeleteModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Удаление курса
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Вы уверены, что хотите удалить курс <span class="font-bold text-gray-800" x-text="courseTitleToDelete"></span> из вашей библиотеки? 
                                    <br><br>
                                    <strong class="text-red-600">Внимание:</strong> При удалении курса прогресс обучения будет безвозвратно утерян, а курс будет удален без возврата денежных средств. Для повторного прохождения вам придется приобрести его заново.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form method="POST" :action="`/student/courses/${courseIdToDelete}/unenroll`">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Да, удалить курс
                        </button>
                    </form>
                    <button type="button" @click="showDeleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
