<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'course']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default show pending first, then latest
            $query->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')");
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Update the specified resource in storage (Approve/Reject).
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'admin_note' => 'nullable|string'
        ]);

        $payment->status = $validated['status'];
        $payment->admin_note = $request->admin_note;
        
        if ($validated['status'] === 'approved') {
             $payment->paid_at = now();

             // Activate Enrollment
             Enrollment::updateOrCreate(
                [
                    'user_id' => $payment->user_id,
                    'course_id' => $payment->course_id
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now()
                ]
             );

             // Send SMS Notification
             try {
                $user = $payment->user;
                $course = $payment->course;
                if ($user->phone) {
                    $smsService = new \App\Services\SmsService();
                    $message = "Payment Approved! You have been successfully enrolled in '{$course->title}'. Student ID: {$user->student_id}. Log in to start learning.";
                    $smsService->send($user->phone, $message);
                }
             } catch (\Exception $e) {
                 // Log SMS failure but don't stop the approval process
                 \Illuminate\Support\Facades\Log::error("SMS Notification Failed for Payment ID {$payment->id}: " . $e->getMessage());
             }
        }

        $payment->save();

        return redirect()->route('admin.payments.index')->with('success', 'Payment status updated to ' . ucfirst($validated['status']));
    }
}
