<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventOrganizer extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'name_ar',
        'contact'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
