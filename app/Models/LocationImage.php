<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImageHelper;

class LocationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'image_path',
        'caption',
        'caption_ar',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer'
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return ImageHelper::getLocationGalleryImageUrl($this->image_path);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
