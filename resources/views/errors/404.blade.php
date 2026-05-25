@extends('layouts.app')

@section('title', 'Страница не найдена')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
        <div class="mb-8">
            <i class="fas fa-exclamation-triangle text-gray-300 text-6xl"></i>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Страница не найдена</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            К сожалению, запрошенная вами страница не существует или была перемещена.
        </p>
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('home') }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                На главную
            </a>
            <a href="{{ route('courses.index') }}" 
               class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">
                Каталог курсов
            </a>
        </div>
    </div>
</div>
@endsection
