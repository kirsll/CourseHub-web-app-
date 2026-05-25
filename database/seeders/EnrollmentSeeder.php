<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем пользователей и курсы
        $student = DB::table('users')->where('email', 'student@course.local')->first();
        $courses = DB::table('courses')->get();

        $enrollments = [];

        foreach ($courses as $course) {
            $enrollments[] = [
                'user_id' => $student->id,
                'course_id' => $course->id,
                'enrolled_at' => now()->subDays(rand(1, 30)),
                'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
                'progress_percentage' => rand(0, 100),
                'paid_amount' => $course->discount_price ?? $course->price,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('enrollments')->insert($enrollments);
    }
}
