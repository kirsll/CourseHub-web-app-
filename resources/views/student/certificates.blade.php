@extends('layouts.app')

@section('title', 'Мои сертификаты')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Мои сертификаты</h1>
        <p class="text-gray-600 mt-2">Ваши сертификаты о завершении курсов</p>
    </div>

    @forelse ($certificates as $certificate)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-certificate text-blue-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Сертификат о прохождении курса
                        </h3>
                        <p class="text-gray-600">{{ $certificate->course->title }}</p>
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                Выдан: {{ $certificate->issued_at->format('d.m.Y') }}
                            </span>
                            <span>
                                <i class="fas fa-user mr-1"></i>
                                Преподаватель: {{ $certificate->course->instructor->full_name }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('student.certificates.show', $certificate) }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-eye mr-2"></i> Просмотр
                    </a>
                    <a href="{{ route('student.certificates.download', $certificate) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i> Скачать
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12">
            <i class="fas fa-certificate text-gray-300 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Сертификатов пока нет</h3>
            <p class="text-gray-600 mb-4">Завершите курсы, чтобы получить сертификаты</p>
            <a href="{{ route('courses.index') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Перейти к курсам
            </a>
        </div>
    @endforelse

    <!-- Пагинация -->
    @if ($certificates->hasPages())
        <div class="mt-8">
            {{ $certificates->links() }}
        </div>
    @endif
</div>
@endsection
