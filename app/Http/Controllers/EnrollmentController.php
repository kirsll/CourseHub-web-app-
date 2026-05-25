<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Models\Payment;
// use App\Models\Coupon; // Удаляем неиспользуемую модель
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    public function enroll($courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        
        // Проверяем, не записан ли уже студент
        if ($course->enrollments()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Вы уже записаны на этот курс');
        }

        // Бесплатные курсы записываем сразу
        if ($course->current_price == 0) {
            $this->createFreeEnrollment($course, $user);
            
            return redirect()->route('student.course', $course->id)
                ->with('success', 'Вы успешно записаны на курс!');
        }

        // Платные курсы направляем на оплату
        return redirect()->route('student.checkout', $course->id);
    }

    public function checkout($courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        
        // Проверяем, не записан ли уже студент
        if ($course->enrollments()->where('user_id', $user->id)->exists()) {
            return redirect()->route('student.course', $course->id)
                ->with('info', 'Вы уже записаны на этот курс');
        }

        // Бесплатные курсы не должны быть здесь
        if ($course->current_price == 0) {
            return redirect()->route('student.enroll', $course->id);
        }

        return view('student.checkout', compact('course'));
    }

    public function processPayment(Request $request, $courseId)
    {
        $user = Auth::user();
        $course = Course::findOrFail($courseId);
        
        $request->validate([
            'payment_method' => 'required|string|in:card,yookassa,robokassa',
        ]);

        try {
            DB::beginTransaction();

            // Создаем заказ
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . time() . '-' . $user->id,
                'total_amount' => $course->current_price,
                'final_amount' => $course->current_price,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            // Создаем запись на курс
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'order_id' => $order->id,
                'paid_amount' => $course->current_price,
                'enrolled_at' => now(),
            ]);

            // Создаем платеж
            $commission = $course->current_price * 0.15; // 15% комиссия платформы
            $instructorEarnings = $course->current_price - $commission;

            $payment = Payment::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'amount' => $course->current_price,
                'commission' => $commission,
                'instructor_earnings' => $instructorEarnings,
                'payment_gateway' => $request->payment_method,
                'status' => 'pending',
            ]);

            // Имитируем успешную оплату (для демонстрации)
            $payment->markAsCompleted('TEST-' . uniqid());
            $order->markAsPaid();

            DB::commit();

            return redirect()->route('student.payment.success')
                ->with('success', 'Оплата успешно обработана!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Ошибка при обработке платежа: ' . $e->getMessage());
        }
    }

    protected function createFreeEnrollment(Course $course, $user)
    {
        return Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'paid_amount' => 0,
            'enrolled_at' => now(),
        ]);
    }

    public function cancelEnrollment(Course $course)
    {
        $user = Auth::user();
        
        $enrollment = $course->enrollments()
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Проверяем, можно ли отменить запись (например, в течение 14 дней)
        if ($enrollment->created_at->diffInDays(now()) > 14) {
            return back()->with('error', 'Невозможно отменить запись после 14 дней');
        }

        // Если был платеж, нужно обработать возврат
        if ($enrollment->paid_amount > 0) {
            // Здесь должна быть логика возврата средств через платежную систему
            // Для примера просто отменяем
            
            if ($enrollment->order) {
                $enrollment->order->update(['status' => 'cancelled']);
            }
            
            if ($enrollment->payments) {
                $enrollment->payments()->update(['status' => 'refunded']);
            }
        }

        $enrollment->delete();

        return back()->with('success', 'Запись на курс отменена');
    }

    public function certificate(Course $course)
    {
        $user = Auth::user();
        
        $enrollment = $course->enrollments()
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->firstOrFail();

        $certificate = $enrollment->certificates()
            ->where('is_active', true)
            ->first();

        if (!$certificate) {
            return back()->with('error', 'Сертификат не найден');
        }

        return view('student.certificate', compact('course', 'enrollment', 'certificate'));
    }
}
