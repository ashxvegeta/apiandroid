<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    // ✅ 1. List Students (Pagination + Search 🔥)
        public function index(Request $request)
    {
        
        $query = Student::query();


        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $students = $query->latest()->paginate(10);

        return response()->json($students);
    }

    // ✅ 2. Create Student


    public function store(Request $request)
{
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:students',
            'phone' => 'required'
        ]);

        return DB::transaction(function () use ($request) {

            $setting = Setting::first();

            if (!$setting) {
                return response()->json([
                    'status' => false,
                    'message' => 'Seat configuration not found'
                ], 500);
            }

            if ($setting->filled_seats >= $setting->total_seats) {
                return response()->json([
                    'status' => false,
                    'message' => 'No seats available'
                ], 400);
            }

            $student = Student::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'course' => $request->course ?? null
            ]);

            $setting->increment('filled_seats');

            return response()->json([
                'status' => true,
                'message' => 'Student created',
                'data' => $student
            ]);
        });
    }
    
    // ✅ 3. Show Student

        public function show($id)
    {
        $student = Student::findOrFail($id);

        return response()->json($student);
    }

    // ✅ 4. Update Student

        public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $student->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Student updated',
            'data' => $student
        ]);
    }

    // ✅ 5. Delete Student

            public function destroy($id)
        {
            $student = Student::findOrFail($id);
            $student->delete();

            return response()->json([
                'status' => true,
                'message' => 'Student deleted'
            ]);
        }

    public function seatStatus()
    {
        $setting = Setting::first();

        return response()->json([
            'total_seats' => $setting->total_seats,
            'filled_seats' => $setting->filled_seats,
            'available_seats' => $setting->total_seats - $setting->filled_seats
        ]);
    }
}