<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ImageHelper;

class Location extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope('admin_tourism_type', function ($builder) {
            if (auth('admin')->check()) {
                $admin = auth('admin')->user();

                if ($admin->tourism_type_id) {
                    $builder->where('tourism_type_id', $admin->tourism_type_id);
                }

                if ($admin->governorate_id) {
                    $builder->where('governorate_id', $admin->governorate_id);
                }
            }
        });
    }

    protected $fillable = [
        'name',
        'name_ar',
        'latitude',
        'longitude',
        'address',
        'address_ar',
        'governorate_id',
        'city_id',
        'tourism_type_id',
        'phone',
        'description',
        'description_ar',
        'features',
        'features_ar',
        'rating_description',
        'rating_description_ar',
        'website',
        'email',
        'opening_hours',
        'rating',
        'is_active',
        'views',
        'main_image'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'opening_hours' => 'array',
        'rating' => 'decimal:2',
        'is_active' => 'boolean',
        'views' => 'integer'
    ];

    protected $appends = ['main_image_url'];

    public function getMainImageUrlAttribute()
    {
        return ImageHelper::getLocationMainImageUrl($this->main_image);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_locations');
    }

    public function investments()
    {
        return $this->belongsToMany(Investment::class, 'investment_locations');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'location_categories');
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function tourismType()
    {
        return $this->belongsTo(TourismType::class);
    }

    public function locationTypes()
    {
        return $this->belongsToMany(LocationType::class, 'location_location_types');
    }

    public function images()
    {
        return $this->hasMany(LocationImage::class)->orderBy('order');
    }

    public function activeImages()
    {
        return $this->hasMany(LocationImage::class)->where('is_active', true)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGovernorate($query, $governorateId)
    {
        return $query->where('governorate_id', $governorateId);
    }

    public function scopeByTourismType($query, $tourismTypeId)
    {
        return $query->where('tourism_type_id', $tourismTypeId);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('city_id', $cityId);
    }
}
