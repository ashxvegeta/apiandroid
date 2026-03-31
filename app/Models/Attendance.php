<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
    protected $fillable = [
        'student_id',
        'date',
        'punch_in',
        'punch_out',
        'total_minutes',
        'seat_number'
    ];


    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class);
    }
}
