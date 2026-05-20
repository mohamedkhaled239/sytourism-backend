<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'main_image',
        'start_date',
        'end_date',
        'category_id',

        'status',
        'is_published'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_published' => 'boolean'
    ];

    protected $appends = [
        'main_image_url',
        'status_ar',
        'status_color'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'event_locations');
    }

    public function organizers()
    {
        return $this->hasMany(EventOrganizer::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite_events');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'event_categories_pivot');
    }

    public function getMainImageUrlAttribute()
    {
        if (!$this->main_image) {
            return null;
        }

        return Storage::url($this->main_image);
    }

    public function getStatusArAttribute()
    {
        $statuses = [
            'not_started' => 'لم يبدأ',
            'active' => 'نشط',
            'ended' => 'منتهي'
        ];

        return $statuses[$this->status] ?? 'غير محدد';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'not_started' => 'secondary',
            'active' => 'success',
            'ended' => 'danger'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isEnded()
    {
        return $this->status === 'ended';
    }

    public function hasNotStarted()
    {
        return $this->status === 'not_started';
    }
}
