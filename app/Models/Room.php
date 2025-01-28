<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'hotel_id',
        'type',
        'accommodation',
        'quantity',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
