<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Student extends Model
{
    //
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'course',
        'total_fee',
        'paid_fee',
        'joining_date',
        'status'
    ];

    // one Student has many Fee records.
    public function fees()
    {
      return $this->hasMany(Fee::class);
    }

    public function user()
    {
      return $this->belongsTo(User::class);
    }
}
