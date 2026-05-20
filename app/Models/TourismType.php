<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourismType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'icon',
        'color',
        'description',
        'description_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
