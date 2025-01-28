<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $table = 'hotels';

    protected $fillable = [
        'name',
        'address',
        'city',
        'nit',
        'rooms_quantity'
    ];

    public function rooms(){
        return $this->hasMany(Room::class);
    }
}
