<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class StudentAuthController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->middleware('guest')->except('logout');
        $this->smsService = $smsService;
    }

    // --- Login ---
    public function showLoginForm()
    {
        return view('auth.student-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required',
        ]);

        // Attempt login using phone column by finding user first
        $user = User::where('phone', $request->phone)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->isStudent()) {
                Auth::login($user, $request->remember);
                return redirect()->intended(route('student.dashboard'));
            } else {
                 return back()->with('error', 'Please use Admin Login.');
            }
        }

        return back()->withErrors(['phone' => 'Invalid mobile number or password.']);
    }

    // --- Register Step 1: Phone ---
    public function showRegisterForm()
    {
        return view('auth.register.step1');
    }

    public function processPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric|digits_between:9,12|unique:users,phone',
        ]);

        $otp = rand(100000, 999999);
        $phone = $request->phone;

        // Save to session
        Session::put('register_phone', $phone);
        Session::put('register_otp', $otp);

        // Send SMS
        $message = "Your Verification Code is: $otp";
        $sent = $this->smsService->send($phone, $message);

        if ($sent) {
            return redirect()->route('student.register.otp');
        } else {
            // Fallback for demo/error (REMOVE IN PRODUCTION IF STRICT)
            // For now, if SMS fails, we might show error or allow proceed for testing if local
            if (env('APP_ENV') === 'local') {
                 return redirect()->route('student.register.otp')->with('info', "Local Dev OTP: $otp");
            }
            return back()->with('error', 'Failed to send SMS. Please try again.');
        }
    }

    // --- Register Step 2: OTP ---
    public function showOtpForm()
    {
        if (!Session::has('register_phone')) {
            return redirect()->route('student.register');
        }
        return view('auth.register.step2');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        if ($request->otp == Session::get('register_otp')) {
            Session::put('otp_verified', true);
            return redirect()->route('student.register.complete');
        }

        return back()->withErrors(['otp' => 'Invalid OTP Code.']);
    }

    // --- Register Step 3: Details ---
    public function showCompleteForm()
    {
        if (!Session::has('otp_verified') || !Session::has('register_phone')) {
            return redirect()->route('student.register');
        }
        return view('auth.register.step3');
    }

    public function completeRegistration(Request $request)
    {
        if (!Session::has('otp_verified')) {
            return redirect()->route('student.register');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // New Fields Validation
            'whatsapp_number' => 'nullable|string|max:15',
            'nic' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'school' => 'nullable|string|max:255',
            'exam_year' => 'nullable|string|max:4',
            'medium' => 'nullable|string|in:Sinhala,English,Tamil',
            'address_line_1' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:100',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:15',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => Session::get('register_phone'),
            'password' => Hash::make($request->password),
            'role' => 'student',
            'is_active' => true,
            // Map new fields
            'whatsapp_number' => $request->whatsapp_number,
            'nic' => $request->nic,
            'dob' => $request->dob,
            'school' => $request->school,
            'exam_year' => $request->exam_year,
            'medium' => $request->medium,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'district' => $request->district,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
        ]);

        // Clear session
        Session::forget(['register_phone', 'register_otp', 'otp_verified']);

        // Auto login
        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', 'Registration successful!');
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('student.login');
    }

    // --- Password Reset Flow ---
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.phone');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate(['phone' => 'required|exists:users,phone']);

        $otp = rand(100000, 999999);
        $phone = $request->phone;

        Session::put('reset_phone', $phone);
        Session::put('reset_otp', $otp);

        $this->smsService->send($phone, "Your Password Reset OTP is: $otp");

        // Info for dev - REMOVED
        // if (env('APP_ENV') === 'local') {
        //      return redirect()->route('password.otp')->with('info', "Local Dev OTP: $otp");
        // }

        return redirect()->route('password.otp')->with('info', 'OTP sent to your mobile number.');
    }

    public function showResetOtpForm()
    {
        if (!Session::has('reset_phone')) return redirect()->route('password.request');
        return view('auth.passwords.otp');
    }

    public function verifyResetOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        if ($request->otp == Session::get('reset_otp')) {
            Session::put('reset_otp_verified', true);
            return redirect()->route('password.reset');
        }

        return back()->withErrors(['otp' => 'Invalid OTP Code.']);
    }

    public function showResetPasswordForm()
    {
        if (!Session::has('reset_otp_verified')) return redirect()->route('password.request');
        return view('auth.passwords.reset');
    }

    public function resetPassword(Request $request)
    {
        if (!Session::has('reset_otp_verified')) return redirect()->route('password.request');

        $request->validate(['password' => 'required|string|min:8|confirmed']);

        $user = User::where('phone', Session::get('reset_phone'))->first(); // Use first() instead of firstOrFail to allow graceful handling if needed, but validation exists:users covers it mostly.
        
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        Session::forget(['reset_phone', 'reset_otp', 'reset_otp_verified']);

        return redirect()->route('student.login')->with('success', 'Password reset successfully. Please login.');
    }
}
