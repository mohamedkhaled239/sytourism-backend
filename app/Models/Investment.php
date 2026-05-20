<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'main_image',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    protected $appends = [
        'main_image_url'
    ];

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'investment_locations');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'investment_categories');
    }

    public function getMainImageUrlAttribute()
    {
        if (!$this->main_image) {
            return null;
        }

        return Storage::url($this->main_image);
    }
}
