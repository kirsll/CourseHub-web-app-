<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Логотип -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center">
                    <i class="fas fa-graduation-cap text-blue-600 text-2xl mr-2"></i>
                    <span class="text-xl font-bold text-gray-800">CourseHub</span>
                </a>
            </div>

            <!-- Основное меню -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                    Главная
                </a>
                <a href="{{ route('courses.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                    Курсы
                </a>
                
                @if (auth()->check())
                    @if (auth()->user()->isStudent())
                        <a href="{{ route('student.dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Мои курсы
                        </a>
                    @endif
                    
                    @if (auth()->user()->isInstructor())
                        <a href="{{ route('instructor.dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Преподавателю
                        </a>
                    @endif
                    
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                            Админка
                        </a>
                    @endif
                @endif
            </div>

            <!-- Поиск -->
            <div class="hidden md:flex items-center">
                <form action="{{ route('courses.search') }}" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Поиск курсов..." 
                           class="w-64 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <button type="submit" class="absolute right-2 top-2 text-gray-500 hover:text-blue-600">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <!-- Пользовательское меню -->
            <div class="flex items-center space-x-4">
                @if (auth()->check())
                    <!-- Уведомления -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative text-gray-700 hover:text-blue-600">
                            <i class="fas fa-bell text-xl"></i>
                            <!-- Счетчик скрыт пока нет реальной системы уведомлений -->
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg py-2 z-50">
                            <div class="px-4 py-2 border-b">
                                <h3 class="text-sm font-semibold text-gray-900">Уведомления</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <div class="px-4 py-4 text-center">
                                    <p class="text-sm text-gray-500">У вас пока нет новых уведомлений</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Профиль -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600">
                            @if (auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                     alt="Avatar" class="h-8 w-8 rounded-full">
                            @else
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                            @endif
                            <span class="hidden md:block text-sm font-medium">{{ auth()->user()->full_name }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                            @if (auth()->user()->isStudent())
                                <a href="{{ route('student.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Дашборд
                                </a>
                                <a href="{{ route('student.courses') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-book mr-2"></i> Мои курсы
                                </a>
                                <a href="{{ route('student.certificates') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-certificate mr-2"></i> Сертификаты
                                </a>
                            @endif

                            @if (auth()->user()->isInstructor())
                                <a href="{{ route('instructor.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-tachometer-alt mr-2"></i> Дашборд
                                </a>
                                <a href="{{ route('instructor.courses') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-book mr-2"></i> Мои курсы
                                </a>
                                <a href="{{ route('instructor.analytics') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-chart-line mr-2"></i> Аналитика
                                </a>
                            @endif

                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Админка
                                </a>
                            @endif

                            <hr class="my-2">
                            <a href="{{ route('student.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Профиль
                            </a>
                            <a href="{{ route('student.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-shopping-cart mr-2"></i> Заказы
                            </a>
                            <hr class="my-2">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Выйти
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        Войти
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        Регистрация
                    </a>
                @endif
            </div>

            <!-- Мобильное меню -->
            <div class="md:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Мобильное меню -->
    <div x-show="mobileMenuOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         class="md:hidden bg-white border-t">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                Главная
            </a>
            <a href="{{ route('courses.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                Курсы
            </a>
            
            @if (auth()->check())
                @if (auth()->user()->isStudent())
                    <a href="{{ route('student.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                        Мои курсы
                    </a>
                @endif
                
                @if (auth()->user()->isInstructor())
                    <a href="{{ route('instructor.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                        Преподавателю
                    </a>
                @endif
                
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                        Админка
                    </a>
                @endif
            @endif
        </div>
    </div>
</nav>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navigation', () => ({
            mobileMenuOpen: false
        }))
    })
</script>
