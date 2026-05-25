<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем ID преподавателя и категорий
        $instructor = DB::table('users')->where('email', 'instructor@course.local')->first();
        $programmingCategory = DB::table('categories')->where('slug', 'programming')->first();
        $designCategory = DB::table('categories')->where('slug', 'web-design')->first();

        $courses = [
            [
                'instructor_id' => $instructor->id,
                'category_id' => $programmingCategory->id,
                'title' => 'Laravel 10 для начинающих',
                'slug' => 'laravel-10-for-beginners',
                'description' => 'Полный курс по созданию веб-приложений на Laravel 10',
                'content' => 'Этот курс научит вас создавать современные веб-приложения с использованием фреймворка Laravel 10. Мы изучим основы MVC, работу с базами данных, аутентификацию и многое другое.',
                'price' => 4999.00,
                'discount_price' => 2999.00,
                'level' => 'beginner',
                'language' => 'ru',
                'duration_minutes' => 1200, // 20 часов
                'thumbnail' => null,
                'preview_video' => null,
                'requirements' => json_encode([
                    'Базовые знания HTML и CSS',
                    'Основы программирования на PHP',
                    'Установленный PHP и Composer'
                ]),
                'what_you_will_learn' => json_encode([
                    'Создавать веб-приложения на Laravel',
                    'Работать с базами данных через Eloquent',
                    'Реализовывать аутентификацию и авторизацию',
                    'Создавать RESTful API',
                    'Развертывать приложения на сервере'
                ]),
                'target_audience' => json_encode([
                    'Начинающие разработчики',
                    'PHP-программисты',
                    'Студенты IT-специальностей'
                ]),
                'is_published' => true,
                'is_featured' => true,
                'rating' => 4.8,
                'reviews_count' => 25,
                'students_count' => 150,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'instructor_id' => $instructor->id,
                'category_id' => $designCategory->id,
                'title' => 'UI/UX Design: от идеи до прототипа',
                'slug' => 'ui-ux-design-from-idea-to-prototype',
                'description' => 'Курс по созданию пользовательских интерфейсов и улучшению пользовательского опыта',
                'content' => 'Изучите принципы дизайна, научитесь создавать прототипы и улучшать пользовательский опыт веб-приложений и мобильных приложений.',
                'price' => 3999.00,
                'discount_price' => null,
                'level' => 'intermediate',
                'language' => 'ru',
                'duration_minutes' => 900, // 15 часов
                'thumbnail' => null,
                'preview_video' => null,
                'requirements' => json_encode([
                    'Базовые знания графического дизайна',
                    'Работа в Figma или Sketch'
                ]),
                'what_you_will_learn' => json_encode([
                    'Принципы UI/UX дизайна',
                    'Создание прототипов в Figma',
                    'Пользовательские исследования',
                    'Тестирование юзабилити',
                    'Адаптивный дизайн'
                ]),
                'target_audience' => json_encode([
                    'Дизайнеры интерфейсов',
                    'Веб-разработчики',
                    'Продуктовые менеджеры'
                ]),
                'is_published' => true,
                'is_featured' => false,
                'rating' => 4.6,
                'reviews_count' => 18,
                'students_count' => 85,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('courses')->insert($courses);
    }
}
