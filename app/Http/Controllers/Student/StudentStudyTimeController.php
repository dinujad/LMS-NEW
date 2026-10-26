<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentStudyTimeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch last 7 days of data
        $sessions = DB::table('study_sessions')
            ->where('user_id', $user->id)
            ->where('session_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('session_date', 'asc')
            ->get();

        // Calculate totals
        $today = Carbon::today()->toDateString();
        $todaySession = $sessions->firstWhere('session_date', $today);
        $todaySeconds = $todaySession ? $todaySession->duration_seconds : 0;
        
        $totalSeconds = $sessions->sum('duration_seconds');

        // Prepare data for charts
        $chartData = [
            'dates' => [],
            'hours' => []
        ];

        // Fill in last 7 days (including empty ones)
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $daySession = $sessions->firstWhere('session_date', $date);
            
            $chartData['dates'][] = Carbon::parse($date)->format('M d');
            // Convert seconds to hours (float)
            $chartData['hours'][] = $daySession ? round($daySession->duration_seconds / 3600, 2) : 0;
        }

        return view('student.study_time.index', compact('todaySeconds', 'totalSeconds', 'chartData'));
    }

    public function heartbeat(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Check if record exists
        $exists = DB::table('study_sessions')
            ->where('user_id', $user->id)
            ->where('session_date', $today)
            ->exists();

        if ($exists) {
            // Increment duration by 60s
            DB::table('study_sessions')
                ->where('user_id', $user->id)
                ->where('session_date', $today)
                ->update([
                    'duration_seconds' => DB::raw('duration_seconds + 60'),
                    'last_activity_at' => $now,
                    'updated_at' => $now
                ]);
        } else {
            // Create new record
            DB::table('study_sessions')->insert([
                'user_id' => $user->id,
                'session_date' => $today,
                'duration_seconds' => 60, // First heartbeat
                'last_activity_at' => $now,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
        
        return response()->json(['status' => 'success']);
    }
}
