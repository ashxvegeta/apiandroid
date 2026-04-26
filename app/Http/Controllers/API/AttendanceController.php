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
    // die('ddd');
    $request->validate([
        'seat_number' => 'required|integer|min:1'
    ]);

    $user = auth()->user();
    $student = Student::where('user_id', $user->id)->first();

    if (!$student) {
        // If an admin created the student first, auto-link by matching email.
        $studentByEmail = Student::where('email', $user->email)->first();
        if ($studentByEmail) {
            if (is_null($studentByEmail->user_id)) {
                $studentByEmail->update(['user_id' => $user->id]);
                $student = $studentByEmail;
            } elseif ((int) $studentByEmail->user_id === (int) $user->id) {
                $student = $studentByEmail;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Student email is already linked to another user'
                ], 409);
            }
        }
    }

    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student not found for this account. Ask admin to create a student with your email.'
        ], 404);
    }

    $today = now()->toDateString();

    // ❌ Already punched in
    $existing = Attendance::where('student_id', $student->id)
        ->where('date', $today)
        ->first();

    if ($existing) {
        return response()->json([
            'status' => false,
            'message' => 'Already punched in'
        ], 400);
    }

    // ❌ Seat already occupied
    $seatTaken = Attendance::where('seat_number', $request->seat_number)
        ->whereNull('punch_out')
        ->exists();

    if ($seatTaken) {
        return response()->json([
            'status' => false,
            'message' => 'Seat already occupied'
        ], 400);
    }

    // ✅ Create attendance
    $attendance = Attendance::create([
        'student_id' => $student->id,
        'date' => $today,
        'seat_number' => $request->seat_number,
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
    $user = auth()->user();
    $student = Student::where('user_id', $user->id)->first();

    if (!$student) {
        $studentByEmail = Student::where('email', $user->email)->first();
        if ($studentByEmail) {
            if (is_null($studentByEmail->user_id)) {
                $studentByEmail->update(['user_id' => $user->id]);
                $student = $studentByEmail;
            } elseif ((int) $studentByEmail->user_id === (int) $user->id) {
                $student = $studentByEmail;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Student email is already linked to another user'
                ], 409);
            }
        }
    }
    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student not found for this account'
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
        'total_minutes' => $minutes,
        'seat_number' => null // 🔥 seat released
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Punched Out',
        'total_minutes' => $minutes
    ]);
}


public function myMonthlyReport(Request $request)
{


    $user = auth()->user();
    $student = Student::where('user_id', $user->id)->first();

    if (!$student) {
        $studentByEmail = Student::where('email', $user->email)->first();
        if ($studentByEmail) {
            if (is_null($studentByEmail->user_id)) {
                $studentByEmail->update(['user_id' => $user->id]);
                $student = $studentByEmail;
            } elseif ((int) $studentByEmail->user_id === (int) $user->id) {
                $student = $studentByEmail;
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Student email is already linked to another user'
                ], 409);
            }
        }
    }
    if (!$student) {
        return response()->json([
            'status' => false,
            'message' => 'Student not found for this account'
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
