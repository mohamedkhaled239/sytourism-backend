<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class NewsImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id',
        'image_path',
        'order'
    ];

    protected $appends = [
        'image_url'
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        return Storage::url($this->image_path);
    }
}
