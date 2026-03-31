<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class IDCardController extends Controller
{
    //

    public function generate()
{
    
        $student = Student::where('user_id', auth()->id())->first();

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'course' => $student->course,
            ],
        ]);
    }
}
