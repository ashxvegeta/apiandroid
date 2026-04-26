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
