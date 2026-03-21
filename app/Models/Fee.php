<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    //

    protected $fillable = [
    'student_id',
    'amount',
    'payment_date',
    'payment_mode',
    'receipt_no'
    ];

// each Fee record belongs to one Student.
    public function student()
    {
    return $this->belongsTo(Student::class);
    }
}
