<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //

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
