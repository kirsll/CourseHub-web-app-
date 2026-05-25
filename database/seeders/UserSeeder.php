<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем ID ролей
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $instructorRole = DB::table('roles')->where('name', 'instructor')->first();
        $studentRole = DB::table('roles')->where('name', 'student')->first();

        $users = [
            [
                'role_id' => $adminRole->id,
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@course.local',
                'password' => Hash::make('password'),
                'phone' => '+7 (999) 123-45-67',
                'bio' => 'Системный администратор платформы онлайн-курсов',
                'balance' => 0.00,
                'is_active' => true, 
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $instructorRole->id,
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'instructor@course.local',
                'password' => Hash::make('password'),
                'phone' => '+7 (999) 234-56-78',
                'bio' => 'Преподаватель с 10-летним опытом в веб-разработке. Специализируюсь на PHP, Laravel и JavaScript.',
                'balance' => 15000.00,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_id' => $studentRole->id,
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'email' => 'student@course.local',
                'password' => Hash::make('password'),
                'phone' => '+7 (999) 345-67-89',
                'bio' => 'Студент, изучаю веб-разработку и программирование',
                'balance' => 5000.00,
                'is_active' => true,
                'email_verified_at' => now(),
                'last_login_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('users')->insert($users);
    }
}
