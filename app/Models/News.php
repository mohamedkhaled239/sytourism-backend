<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'content',
        'content_ar',
        'main_image',
        'is_published',
        'views'
    ];

    protected $casts = [
        'is_published' => 'boolean'
    ];

    protected $appends = [
        'main_image_url',
        'time_ago'
    ];

    public function images()
    {
        return $this->hasMany(NewsImage::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'news_categories');
    }

    public function getMainImageUrlAttribute()
    {
        if (!$this->main_image) {
            return null;
        }

        return Storage::url($this->main_image);
    }

    public function getTimeAgoAttribute()
    {
        $diff = now()->diffInMinutes($this->created_at);

        if ($diff < 60) {
            return $diff . ' دقيقة';
        } elseif ($diff < 1440) {
            return round($diff / 60) . ' ساعة';
        } elseif ($diff < 43200) {
            return round($diff / 1440) . ' يوم';
        } else {
            return round($diff / 43200) . ' شهر';
        }
    }
}
