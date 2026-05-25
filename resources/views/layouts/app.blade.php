<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Платформа онлайн-курсов') - CourseHub</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Навигация -->
    @include('partials.navigation')

    <!-- Основной контент -->
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-4">
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 my-4">
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 my-4">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            @if (session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 my-4">
                    <p>{{ session('info') }}</p>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Футер -->
    @include('partials.footer')

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/app.js') }}"></script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>
