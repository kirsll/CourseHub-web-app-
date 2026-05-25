<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем курсы
        $laravelCourse = DB::table('courses')->where('slug', 'laravel-10-for-beginners')->first();
        $designCourse = DB::table('courses')->where('slug', 'ui-ux-design-from-idea-to-prototype')->first();

        $modules = [
            // Модули для курса Laravel
            [
                'course_id' => $laravelCourse->id,
                'title' => 'Введение в Laravel',
                'description' => 'Основы фреймворка Laravel, установка и настройка',
                'sort_order' => 1,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => $laravelCourse->id,
                'title' => 'MVC архитектура',
                'description' => 'Модель-Представление-Контроллер в Laravel',
                'sort_order' => 2,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => $laravelCourse->id,
                'title' => 'Работа с базами данных',
                'description' => 'Eloquent ORM и миграции',
                'sort_order' => 3,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Модули для курса дизайна
            [
                'course_id' => $designCourse->id,
                'title' => 'Основы UI дизайна',
                'description' => 'Принципы и элементы пользовательского интерфейса',
                'sort_order' => 1,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'course_id' => $designCourse->id,
                'title' => 'UX исследования',
                'description' => 'Пользовательские исследования и анализ',
                'sort_order' => 2,
                'is_published' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('modules')->insert($modules);
    }
}
