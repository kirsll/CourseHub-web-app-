<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем модули
        $modules = DB::table('modules')->get();
        
        $lessons = [];

        // Уроки для модуля "Введение в Laravel"
        $introModule = $modules->where('title', 'Введение в Laravel')->first();
        if ($introModule) {
            $lessons = array_merge($lessons, [
                [
                    'module_id' => $introModule->id,
                    'title' => 'Установка Laravel',
                    'description' => 'Установка и настройка окружения для разработки на Laravel',
                    'content' => 'В этом уроке мы изучим, как установить Laravel, настроить окружение и создать первый проект.',
                    'type' => 'video',
                    'video_url' => 'https://www.youtube.com/watch?v=example1',
                    'duration_minutes' => 30,
                    'sort_order' => 1,
                    'is_published' => true,
                    'is_free' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'module_id' => $introModule->id,
                    'title' => 'Структура проекта Laravel',
                    'description' => 'Изучение структуры директорий и файлов Laravel',
                    'content' => 'Разбираем структуру Laravel проекта: что за что отвечает и где что находится.',
                    'type' => 'video',
                    'video_url' => 'https://www.youtube.com/watch?v=example2',
                    'duration_minutes' => 25,
                    'sort_order' => 2,
                    'is_published' => true,
                    'is_free' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // Уроки для модуля "MVC архитектура"
        $mvcModule = $modules->where('title', 'MVC архитектура')->first();
        if ($mvcModule) {
            $lessons = array_merge($lessons, [
                [
                    'module_id' => $mvcModule->id,
                    'title' => 'Модели и Eloquent',
                    'description' => 'Работа с моделями данных в Laravel',
                    'content' => 'Изучаем, как создавать модели и работать с базами данных через Eloquent ORM.',
                    'type' => 'video',
                    'video_url' => 'https://www.youtube.com/watch?v=example3',
                    'duration_minutes' => 45,
                    'sort_order' => 1,
                    'is_published' => true,
                    'is_free' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'module_id' => $mvcModule->id,
                    'title' => 'Контроллеры и маршруты',
                    'description' => 'Создание контроллеров и настройка маршрутизации',
                    'content' => 'Учимся создавать контроллеры, определять маршруты и обрабатывать запросы.',
                    'type' => 'text',
                    'video_url' => null,
                    'duration_minutes' => 35,
                    'sort_order' => 2,
                    'is_published' => true,
                    'is_free' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // Уроки для модуля "Основы UI дизайна"
        $uiModule = $modules->where('title', 'Основы UI дизайна')->first();
        if ($uiModule) {
            $lessons = array_merge($lessons, [
                [
                    'module_id' => $uiModule->id,
                    'title' => 'Принципы композиции',
                    'description' => 'Основные принципы композиции в дизайне',
                    'content' => 'Изучаем правила композиции, баланс, иерархию и визуальный вес элементов.',
                    'type' => 'video',
                    'video_url' => 'https://www.youtube.com/watch?v=example4',
                    'duration_minutes' => 40,
                    'sort_order' => 1,
                    'is_published' => true,
                    'is_free' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'module_id' => $uiModule->id,
                    'title' => 'Цветовая теория',
                    'description' => 'Работа с цветом в дизайне',
                    'content' => 'Учимся подбирать цветовые палитры и использовать цвет для создания нужной атмосферы.',
                    'type' => 'video',
                    'video_url' => 'https://www.youtube.com/watch?v=example5',
                    'duration_minutes' => 30,
                    'sort_order' => 2,
                    'is_published' => true,
                    'is_free' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        DB::table('lessons')->insert($lessons);
    }
}
