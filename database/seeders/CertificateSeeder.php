<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем завершенные записи
        $completedEnrollments = DB::table('enrollments')
            ->whereNotNull('completed_at')
            ->get();

        $certificates = [];

        foreach ($completedEnrollments as $enrollment) {
            $certificates[] = [
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id,
                'enrollment_id' => $enrollment->id,
                'certificate_number' => 'CERT-' . str_pad($enrollment->id, 6, '0', STR_PAD_LEFT),
                'template' => 'default',
                'certificate_data' => json_encode([
                    'student_name' => 'Student Name',
                    'course_name' => 'Course Name',
                    'completion_date' => is_string($enrollment->completed_at) ? $enrollment->completed_at : $enrollment->completed_at->format('Y-m-d'),
                    'instructor_name' => 'Instructor Name'
                ]),
                'file_path' => 'certificates/certificate-' . $enrollment->id . '.pdf',
                'issued_at' => is_string($enrollment->completed_at) ? $enrollment->completed_at : $enrollment->completed_at,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($certificates)) {
            DB::table('certificates')->insert($certificates);
        }
    }
}
