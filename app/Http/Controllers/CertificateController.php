<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// use PDF; // Закомментируем до установки dompdf

class CertificateController extends Controller
{
    public function index()
    {
        /** @var User $student */
        $student = Auth::user();
        
        $certificates = $student->certificates()
            ->with('course')
            ->where('is_active', true)
            ->orderBy('issued_at', 'desc')
            ->paginate(12);

        return view('student.certificates', compact('certificates'));
    }

    public function show(Certificate $certificate)
    {
        /** @var User $student */
        $student = Auth::user();
        
        // Проверяем, что сертификат принадлежит текущему пользователю
        if ($certificate->user_id !== $student->id) {
            abort(403);
        }

        $certificate->load(['course.instructor', 'enrollment']);

        return view('student.certificate-show', compact('certificate'));
    }

    public function download(Certificate $certificate)
    {
        /** @var User $student */
        $student = Auth::user();
        
        // Проверяем, что сертификат принадлежит текущему пользователю
        if ($certificate->user_id !== $student->id) {
            abort(403);
        }

        // Проверяем, существует ли файл
        if (!Storage::disk('public')->exists($certificate->file_path)) {
            // Если файл не существует, генерируем его
            $this->generateCertificateFile($certificate);
        }

        /** @var \Illuminate\Contracts\Filesystem\Filesystem $storage */
        $storage = Storage::disk('public');
        return $storage->download($certificate->file_path, 'certificate.pdf');
    }

    public function verify($certificateNumber)
    {
        $certificate = Certificate::where('certificate_number', $certificateNumber)
            ->where('is_active', true)
            ->with(['user', 'course.instructor'])
            ->firstOrFail();

        return view('certificates.verify', compact('certificate'));
    }

    protected function generateCertificateFile(Certificate $certificate)
    {
        // Генерация PDF сертификата (требует установки dompdf)
        // $pdf = PDF::loadView('certificates.template', [
        //     'certificate' => $certificate,
        //     'studentName' => $certificate->student_name,
        //     'courseTitle' => $certificate->course_title,
        //     'instructorName' => $certificate->instructor_name,
        //     'completionDate' => $certificate->completion_date,
        //     'certificateNumber' => $certificate->certificate_number,
        //     'totalHours' => $certificate->total_hours,
        // ]);

        // $filePath = $certificate->file_path;
        
        // // Сохраняем PDF
        // Storage::disk('public')->put($filePath, $pdf->output());
        
        // // Обновляем путь в базе данных
        // $certificate->update(['file_path' => $filePath]);
        
        // Временно создаем пустой файл
        $filePath = $certificate->file_path;
        Storage::disk('public')->put($filePath, 'Certificate placeholder');
        $certificate->update(['file_path' => $filePath]);
    }

    // Методы для администраторов
    public function create(Enrollment $enrollment)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $certificate = Certificate::create([
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'enrollment_id' => $enrollment->id,
            'certificate_number' => Certificate::generateCertificateNumber(),
            'template' => 'default',
            'certificate_data' => [
                'student_name' => $enrollment->user->full_name,
                'course_title' => $enrollment->course->title,
                'instructor_name' => $enrollment->course->instructor->full_name,
                'completion_date' => $enrollment->completed_at->format('d.m.Y'),
                'total_hours' => $enrollment->course->formatted_duration,
            ],
            'file_path' => 'certificates/temp.pdf', // Будет сгенерирован при первом запросе
            'issued_at' => now(),
        ]);

        // Генерируем файл сертификата
        $this->generateCertificateFile($certificate);

        return back()->with('success', 'Сертификат создан');
    }

    public function deactivate(Certificate $certificate)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $certificate->deactivate();

        return back()->with('success', 'Сертификат деактивирован');
    }

    public function activate(Certificate $certificate)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $certificate->activate();

        return back()->with('success', 'Сертификат активирован');
    }
}
