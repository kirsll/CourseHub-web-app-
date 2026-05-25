<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function process(Request $request)
    {
        // Этот метод будет обрабатывать webhook от платежных систем
        // Для примера показана базовая структура
        
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_gateway' => 'required|string',
            'transaction_id' => 'required|string',
            'status' => 'required|string|in:completed,failed',
            'amount' => 'required|numeric',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Проверяем, что заказ принадлежит текущему пользователю
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            if ($request->status === 'completed') {
                // Подтверждаем платеж
                $payment = $order->payments()->first();
                $payment->markAsCompleted($request->transaction_id);
                
                // Подтверждаем заказ
                $order->markAsPaid();
                
                return redirect()->route('student.payment.success')
                    ->with('success', 'Оплата успешно обработана!');
                    
            } else {
                // Отказ в платеже
                $payment = $order->payments()->first();
                $payment->markAsFailed();
                
                return redirect()->route('student.payment.cancel')
                    ->with('error', 'Платеж не прошел. Попробуйте еще раз.');
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Ошибка при обработке платежа: ' . $e->getMessage());
        }
    }

    public function success()
    {
        return view('student.payment.success');
    }

    public function cancel()
    {
        return view('student.payment.cancel');
    }

    // Методы для администраторов
    public function webhook(Request $request, $gateway)
    {
        // Обработка webhook от платежных шлюзов
        // YooKassa, Robokassa, Stripe и т.д.
        
        switch ($gateway) {
            case 'yookassa':
                return $this->handleYooKassaWebhook($request);
            case 'robokassa':
                return $this->handleRobokassaWebhook($request);
            default:
                return response()->json(['error' => 'Unknown gateway'], 400);
        }
    }

    protected function handleYooKassaWebhook(Request $request)
    {
        $payload = $request->all();
        
        // Верификация подписи и обработка события
        if ($payload['event'] === 'payment.succeeded') {
            $paymentId = $payload['object']['id'];
            $orderId = $payload['object']['metadata']['order_id'];
            
            $payment = Payment::where('transaction_id', $paymentId)->first();
            
            if ($payment) {
                $payment->markAsCompleted($paymentId);
                $payment->order->markAsPaid();
            }
        }
        
        return response()->json(['status' => 'ok']);
    }

    protected function handleRobokassaWebhook(Request $request)
    {
        // Логика обработки webhook от Robokassa
        // ...
        
        return response()->json(['status' => 'ok']);
    }
}
