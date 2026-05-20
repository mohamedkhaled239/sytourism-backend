<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'icon',
        'pin_image',
        'color',
        'description',
        'description_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected $appends = ['pin_image_url'];

    /**
     * Get the full URL for the pin image.
     */
    public function getPinImageUrlAttribute(): ?string
    {
        if (!$this->pin_image) {
            return null;
        }
        return asset('images/location-type-pins/' . $this->pin_image);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'location_location_types');
    }
}
