<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AdminReportController extends Controller
{
    // ✅ 1. Daily Attendance
    public function dailyAttendance()
    {
        $today = now()->toDateString();

        $data = Attendance::with('student')
            ->where('date', $today)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Daily Attendance',
            'data' => $data
        ]);
    }

    // ✅ 2. Monthly Attendance (Total time per student)
    public function monthlyAttendance()
    {
        $data = Attendance::selectRaw('student_id, SUM(total_minutes) as total_time')
            ->with('student')
            ->groupBy('student_id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Monthly Attendance',
            'data' => $data
        ]);
    }

    // ✅ 3. Student-wise Report
    public function studentReport($id)
    {
        $data = Attendance::with('student')
            ->where('student_id', $id)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Student Report',
            'data' => $data
        ]);
    }

    // ✅ 4. Top Students (Highest study time)
    public function topStudents()
    {
        $data = Attendance::selectRaw('student_id, SUM(total_minutes) as total_time')
            ->with('student')
            ->groupBy('student_id')
            ->orderByDesc('total_time')
            ->limit(5)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Top Students',
            'data' => $data
        ]);
    }
}