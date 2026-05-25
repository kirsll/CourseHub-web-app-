<footer class="bg-gray-800 text-white mt-12">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- О компании -->
            <div>
                <div class="flex items-center mb-4">
                    <i class="fas fa-graduation-cap text-blue-400 text-2xl mr-2"></i>
                    <span class="text-xl font-bold">CourseHub</span>
                </div>
                <p class="text-gray-400 text-sm">
                    Современная платформа онлайн-обучения с лучшими курсами от опытных преподавателей.
                </p>
                <div class="flex space-x-4 mt-4">
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Курсы -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Курсы</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('courses.index') }}" class="text-gray-400 hover:text-white text-sm">Все курсы</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Программирование</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Дизайн</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Маркетинг</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Бизнес</a></li>
                </ul>
            </div>

            <!-- Поддержка -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Поддержка</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Помощь</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">FAQ</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Контакты</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Правила использования</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Политика конфиденциальности</a></li>
                </ul>
            </div>

            <!-- Преподавателям -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Преподавателям</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Стать преподавателем</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Как создать курс</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Правила платформы</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white text-sm">Доходы и выплаты</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    © {{ date('Y') }} CourseHub. Все права защищены.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="text-gray-400 hover:text-white text-sm">Условия использования</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm">Политика конфиденциальности</a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm">Cookie</a>
                </div>
            </div>
        </div>
    </div>
</footer>
