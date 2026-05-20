<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private $serverKey;
    private $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = env('FCM_SERVER_KEY');
    }

    /**
     * Send push notification to all users with notifications enabled
     */
    public function sendToAllUsers($title, $body, $type, $data = [])
    {
        // Get all users with notifications enabled and FCM token
        $users = User::where('notifications_enabled', true)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->toArray();

        if (empty($users)) {
            Log::info('No users with FCM tokens found for push notification');
            return false;
        }

        // Create notification record
        $notification = Notification::create([
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
            'sent_at' => now()
        ]);

        // Send to FCM
        return $this->sendToTokens($users, $title, $body, $data);
    }

    /**
     * Send push notification to specific FCM tokens
     */
    public function sendToTokens($tokens, $title, $body, $data = [])
    {
        if (empty($this->serverKey)) {
            Log::error('FCM Server Key not configured');
            return false;
        }

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1
            ],
            'data' => $data
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json'
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                Log::info('Push notification sent successfully', [
                    'tokens_count' => count($tokens),
                    'title' => $title
                ]);
                return true;
            } else {
                Log::error('FCM request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Push notification failed', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
            return false;
        }
    }

    /**
     * Send notification when a new location is added
     */
    public function sendLocationNotification($location)
    {
        $title = 'موقع جديد تم إضافته!';
        $body = "تم إضافة موقع جديد: {$location->name}";
        $data = [
            'type' => 'location',
            'location_id' => $location->id,
            'location_name' => $location->name
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_LOCATION, $data);
    }

    /**
     * Send notification when a new event is added
     */
    public function sendEventNotification($event)
    {
        $title = 'حدث جديد!';
        $body = "تم إضافة حدث جديد: {$event->title}";
        $data = [
            'type' => 'event',
            'event_id' => $event->id,
            'event_title' => $event->title
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_EVENT, $data);
    }

    /**
     * Send notification when a new news is added
     */
    public function sendNewsNotification($news)
    {
        $title = 'خبر جديد!';
        $body = "تم نشر خبر جديد: {$news->title}";
        $data = [
            'type' => 'news',
            'news_id' => $news->id,
            'news_title' => $news->title
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_NEWS, $data);
    }

    /**
     * Send notification when a new investment is added
     */
    public function sendInvestmentNotification($investment)
    {
        $title = 'استثمار جديد!';
        $body = "تم إضافة فرصة استثمارية جديدة: {$investment->title}";
        $data = [
            'type' => 'investment',
            'investment_id' => $investment->id,
            'investment_title' => $investment->title
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_INVESTMENT, $data);
    }
}
