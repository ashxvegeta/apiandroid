<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\Student;
use Illuminate\Support\Str;

class FeeController extends Controller
{

    // ✅ 1. Pay Fee  logic
    public function payFee(Request $request)
    {

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1'
        ]);
    
        $receiptNo = 'RCPT-' . strtoupper(Str::random(6));

   
        $fee = Fee::create([
            'student_id' => $request->student_id,
            'amount' => $request->amount,
            'payment_date' => now(),
            'payment_mode' => $request->payment_mode ?? 'cash',
            'receipt_no' => $receiptNo
        ]);


        die($fee);
        // Update student paid fee
        $student = Student::find($request->student_id);
        $student->paid_fee += $request->amount;
        $student->save();

        return response()->json([
            'status' => true,
            'message' => 'Fee paid successfully',
            'receipt_no' => $receiptNo,
            'data' => $fee
        ]);
    }


// ✅ 2. Get Student Fee History
public function studentFees($id)
{
    $fees = Fee::where('student_id', $id)->latest()->get();

    return response()->json($fees);
}
  

// ✅ 3. Pending Fees

public function pendingFees()
{
    $students = Student::whereColumn('total_fee', '>', 'paid_fee')->get();

    return response()->json($students);
}

}
