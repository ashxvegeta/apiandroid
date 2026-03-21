<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
    protected $fillable = [
        'name',
        'email',
        'phone',
        'course',
        'total_fee',
        'paid_fee',
        'joining_date',
        'status'
    ];
}
