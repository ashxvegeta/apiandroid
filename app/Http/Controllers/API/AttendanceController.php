<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    //

public function punchIn(Request $request)
{
    $student = Student::where('user_id', auth()->id())->first();
    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student profile not linked to this user'
        ], 404);
    }

    $studentId = $student->id;
    $today = now()->toDateString();

    // Check if already punched in
    $attendance = Attendance::where('student_id', $studentId)
        ->where('date', $today)
        ->first();

    if ($attendance) {
        return response()->json([
            'status' => false,
            'message' => 'Already punched in',
            'data' => $attendance
        ], 400);
    }

    // Create new record
    $attendance = Attendance::create([
        'student_id' => $studentId,
        'date' => $today,
        'punch_in' => now()->format('H:i:s')
    ]);

    

    return response()->json([
        'status' => true,
        'message' => 'Punched In',
        'data' => $attendance
    ]);
}

public function punchOut(Request $request)
{
    $student = Student::where('user_id', auth()->id())->first();
    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student profile not linked to this user'
        ], 404);
    }

    $studentId = $student->id;
    $today = now()->toDateString();

    $attendance = Attendance::where('student_id', $studentId)
        ->where('date', $today)
        ->first();

    if (!$attendance || !$attendance->punch_in) {
        return response()->json([
            'status' => false,
            'message' => 'Punch in first'
        ], 400);
    }

    $punchIn = Carbon::parse($attendance->punch_in);
    $punchOut = now();

    $minutes = $punchIn->diffInMinutes($punchOut);

    $attendance->update([
        'punch_out' => $punchOut->format('H:i:s'),
        'total_minutes' => $minutes
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Punched Out',
        'total_minutes' => $minutes
    ]);
}


public function myMonthlyReport(Request $request)
{
    $student = Student::where('user_id', auth()->id())->first();
    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student profile not linked to this user'
        ], 404);
    }

    $studentId = $student->id;

    $data = Attendance::where('student_id', $studentId)
        ->selectRaw('MONTH(date) as month, SUM(total_minutes) as total_time')
        ->groupBy('month')
        ->get();

    return response()->json($data);
}
}
