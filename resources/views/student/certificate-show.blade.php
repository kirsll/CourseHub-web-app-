@extends('layouts.app')

@section('title', 'Сертификат')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- Заголовок сертификата -->
        <div class="text-center mb-8">
            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-certificate text-blue-600 text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Сертификат</h1>
            <p class="text-gray-600">о прохождении курса</p>
        </div>

        <!-- Информация о курсе -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                "{{ $certificate->course->title }}"
            </h2>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <p class="text-gray-700 mb-4">
                    Настоящим удостоверяется, что
                </p>
                <h3 class="text-xl font-semibold text-blue-600 mb-4">
                    {{ Auth::user()->full_name }}
                </h3>
                <p class="text-gray-700">
                    успешно завершил(а) курс "{{ $certificate->course->title }}"
                </p>
            </div>

            <!-- Детали сертификата -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="text-left">
                    <p class="text-sm text-gray-600 mb-2">Преподаватель:</p>
                    <p class="font-medium text-gray-900">
                        {{ $certificate->course->instructor->full_name }}
                    </p>
                </div>
                <div class="text-left">
                    <p class="text-sm text-gray-600 mb-2">Дата выдачи:</p>
                    <p class="font-medium text-gray-900">
                        {{ $certificate->issued_at->format('d F Y') }}
                    </p>
                </div>
                <div class="text-left">
                    <p class="text-sm text-gray-600 mb-2">Номер сертификата:</p>
                    <p class="font-medium text-gray-900">
                        #{{ $certificate->id }}
                    </p>
                </div>
                <div class="text-left">
                    <p class="text-sm text-gray-600 mb-2">Статус:</p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1"></i> Активен
                    </span>
                </div>
            </div>

            <!-- Время обучения -->
            @if ($certificate->enrollment && $certificate->enrollment->completed_at)
                <div class="bg-blue-50 rounded-lg p-4 mb-8">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-clock mr-1"></i>
                        Время обучения: с {{ $certificate->enrollment->enrolled_at->format('d.m.Y') }} 
                        по {{ $certificate->enrollment->completed_at->format('d.m.Y') }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Кнопки действий -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('student.certificates.download', $certificate) }}" 
               class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center">
                <i class="fas fa-download mr-2"></i> Скачать PDF
            </a>
            <a href="{{ route('student.certificates') }}" 
               class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> К списку сертификатов
            </a>
        </div>

        <!-- Подпись -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-center">
                    <div class="border-b border-gray-400 w-32 mb-2"></div>
                    <p class="text-sm text-gray-600">
                        {{ $certificate->course->instructor->full_name }}
                    </p>
                    <p class="text-xs text-gray-500">Преподаватель</p>
                </div>
                <div class="text-center">
                    <div class="border-b border-gray-400 w-32 mb-2"></div>
                    <p class="text-sm text-gray-600">Платформа обучения</p>
                    <p class="text-xs text-gray-500">Электронная подпись</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
