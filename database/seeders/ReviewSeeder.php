<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем данные
        $student = DB::table('users')->where('email', 'student@course.local')->first();
        $courses = DB::table('courses')->get();

        $reviews = [];

        foreach ($courses as $course) {
            $enrollment = DB::table('enrollments')
                ->where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->first();

            if ($enrollment && $enrollment->completed_at) {
                $reviews[] = [
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'enrollment_id' => $enrollment->id,
                    'rating' => rand(4, 5),
                    'comment' => $this->getRandomComment($course->title),
                    'is_visible' => true,
                    'created_at' => now()->subDays(rand(1, 20)),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('reviews')->insert($reviews);
    }

    private function getRandomComment(string $courseTitle): string
    {
        $comments = [
            "Отличный курс! Все понятно объяснено. Рекомендую.",
            "Хорошая структура курса, много практических заданий.",
            "Преподаватель отлично объясняет материал. Все понятно.",
            "Курс помог мне разобраться в теме. Спасибо!",
            "Отличное соотношение теории и практики. Рекомендую.",
            "Очень полезный курс, получил много новых знаний.",
            "Структура курса логичная, материал подается последовательно.",
            "Прекрасный курс для начинающих. Все доступно объясняется.",
        ];

        return $comments[array_rand($comments)];
    }
}
