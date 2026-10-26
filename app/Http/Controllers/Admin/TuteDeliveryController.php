<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class TuteDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'course'])
            ->where('status', 'approved') // Only show approved payments
            ->latest('paid_at');

        if ($request->has('status') && $request->status != 'all') {
            $query->where('delivery_status', $request->status);
        }

        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        $payments = $query->paginate(15);
        $courses = \App\Models\Course::all();

        return view('admin.tute_delivery.index', compact('payments', 'courses'));
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'delivery_status' => 'required|in:pending,delivered'
        ]);

        $payment->delivery_status = $request->delivery_status;
        $payment->save();

        return back()->with('success', 'Delivery status updated successfully!');
    }
}
