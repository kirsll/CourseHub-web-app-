<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Администратор',
                'description' => 'Полный доступ ко всем функциям системы',
                'permissions' => json_encode([
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'courses.view', 'courses.create', 'courses.edit', 'courses.delete', 'courses.publish',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
                    'orders.view', 'payments.view', 'reviews.moderate',
                    'system.settings', 'system.analytics'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'instructor',
                'display_name' => 'Преподаватель',
                'description' => 'Создание и управление курсами',
                'permissions' => json_encode([
                    'courses.view', 'courses.create', 'courses.edit', 'courses.delete',
                    'modules.view', 'modules.create', 'modules.edit', 'modules.delete',
                    'lessons.view', 'lessons.create', 'lessons.edit', 'lessons.delete',
                    'quizzes.view', 'quizzes.create', 'quizzes.edit', 'quizzes.delete',
                    'students.view', 'reviews.view', 'analytics.own'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'student',
                'display_name' => 'Студент',
                'description' => 'Доступ к обучению и покупка курсов',
                'permissions' => json_encode([
                    'courses.view', 'courses.purchase', 'courses.learn',
                    'progress.view', 'certificates.view', 'reviews.create'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('roles')->insert($roles);
    }
}
