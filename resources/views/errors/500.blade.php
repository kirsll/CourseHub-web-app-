@extends('layouts.app')

@section('title', 'Ошибка сервера')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
        <div class="mb-8">
            <i class="fas fa-exclamation-circle text-red-300 text-6xl"></i>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">500</h1>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Ошибка сервера</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Произошла внутренняя ошибка сервера. Пожалуйста, попробуйте обновить страницу или вернитесь позже.
        </p>
        
        <div class="flex justify-center space-x-4">
            <button onclick="history.back()" 
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Назад
            </button>
            <a href="{{ route('home') }}" 
               class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300">
                На главную
            </a>
        </div>
        
        @if (config('app.debug'))
            <div class="mt-8 text-left">
                <details class="bg-white rounded-lg shadow p-4">
                    <summary class="cursor-pointer font-medium text-gray-900">
                        Детали ошибки (только для разработки)
                    </summary>
                    <div class="mt-2 text-sm text-gray-600">
                        @if (isset($exception))
                            <pre class="bg-gray-100 p-2 rounded overflow-auto">{{ $exception->getMessage() }}</pre>
                            <pre class="bg-gray-100 p-2 rounded overflow-auto mt-2">{{ $exception->getTraceAsString() }}</pre>
                        @endif
                    </div>
                </details>
            </div>
        @endif
    </div>
</div>
@endsection
