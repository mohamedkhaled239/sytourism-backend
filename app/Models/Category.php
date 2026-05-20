<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'type',
        'color',
        'description',
        'description_ar',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scope for different types
    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    public function scopeEvents($query)
    {
        return $query->where('type', 'events');
    }

    public function scopeInvestments($query)
    {
        return $query->where('type', 'investments');
    }

    public function scopeLocations($query)
    {
        return $query->where('type', 'locations');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Relationships
    public function news()
    {
        return $this->belongsToMany(News::class, 'news_categories');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_categories_pivot');
    }

    public function investments()
    {
        return $this->belongsToMany(Investment::class, 'investment_categories');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'location_categories');
    }
}
