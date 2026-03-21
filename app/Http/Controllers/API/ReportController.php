<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Fee;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    //

        public function pendingFees()
    {
        $students = Student::whereColumn('total_fee', '>', 'paid_fee')->get();

        return response()->json([
            'status' => true,
            'data' => $students
        ]);
    }


        public function paidStudents()
    {
        $students = Student::whereColumn('total_fee', '=', 'paid_fee')->get();

        return response()->json([
            'status' => true,
            'data' => $students
        ]);
    }


        public function monthlyCollection()
    {
        $data = Fee::select(
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }


}
