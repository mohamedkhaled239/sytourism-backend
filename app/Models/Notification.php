<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'type',
        'data',
        'sent_at',
        'created_by'
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Notification types
    const TYPE_LOCATION = 'location';
    const TYPE_EVENT = 'event';
    const TYPE_NEWS = 'news';
    const TYPE_INVESTMENT = 'investment';

    public static function getTypes()
    {
        return [
            self::TYPE_LOCATION,
            self::TYPE_EVENT,
            self::TYPE_NEWS,
            self::TYPE_INVESTMENT
        ];
    }

    // Relationship with the user who created the notification
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Get latest notification for each type
    public static function getLatestByType()
    {
        $types = self::getTypes();
        $latest = [];

        foreach ($types as $type) {
            $notification = self::where('type', $type)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($notification) {
                $latest[$type] = $notification;
            }
        }

        return $latest;
    }
}
