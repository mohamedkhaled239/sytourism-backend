<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    private $appId;
    private $restApiKey;
    private $apiUrl = 'https://api.onesignal.com/notifications';
    private $deepLinkScheme;

    public function __construct()
    {
        // Trim to avoid hidden whitespace/newline issues in .env values
        $this->appId = trim((string) env('ONESIGNAL_APP_ID', ''));
        $this->restApiKey = trim((string) env('ONESIGNAL_REST_API_KEY', ''));
        $this->deepLinkScheme = trim((string) env('DEEP_LINK_SCHEME', 'seaha'));
    }

    private function buildAppUrl(string $type, int $id): string
    {
        $scheme = $this->deepLinkScheme ?: 'seaha';
        return sprintf('%s://%s/%d', $scheme, $type, $id);
    }

    /**
     * Send push notification to all users with notifications enabled
     */
    public function sendToAllUsers($title, $body, $type, $data = [])
    {
        // Collect player IDs for users who enabled notifications
        $playerIds = User::where('notifications_enabled', true)
            ->whereNotNull('onesignal_player_id')
            ->pluck('onesignal_player_id')
            ->filter()
            ->unique()
            ->values();

        if ($playerIds->isEmpty()) {
            Log::warning('No OneSignal player IDs found for users with notifications enabled');
            return false;
        }

        // Create notification record
        Notification::create([
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
            'sent_at' => now()
        ]);

        // Send in batches (OneSignal recommends up to ~2000 ids per request)
        $success = true;
        foreach ($playerIds->chunk(1000) as $chunk) {
            $ok = $this->sendToPlayers($chunk->all(), $title, $body, $data);
            $success = $success && $ok;
        }

        return $success;
    }

    /**
     * Send push notification via OneSignal
     */
    public function sendNotification($title, $body, $data = [])
    {
        if (empty($this->appId) || empty($this->restApiKey)) {
            Log::error('OneSignal credentials not configured');
            return false;
        }

        // Lift optional app_url from data if provided
        $appUrl = null;
        if (isset($data['app_url']) && is_string($data['app_url'])) {
            $appUrl = $data['app_url'];
            unset($data['app_url']);
        }

        $payload = [
            'app_id' => $this->appId,
            'headings' => [
                'en' => $title,
                'ar' => $title
            ],
            'contents' => [
                'en' => $body,
                'ar' => $body
            ],
            'data' => $data,
            // Send to all subscribed users
            'included_segments' => ['Subscribed Users'],
            // You can also use filters to target specific users
            // 'filters' => [
            //     ['field' => 'tag', 'key' => 'notifications_enabled', 'relation' => '=', 'value' => 'true']
            // ],
            // Android specific settings
            'android_accent_color' => 'FF2196F3',
            'small_icon' => 'ic_notification',
            'large_icon' => 'ic_launcher',
            'priority' => 10,
            // iOS specific settings
            'ios_badgeType' => 'Increase',
            'ios_badgeCount' => 1,
            'ios_sound' => 'default',
            'ios_category' => 'general',
            'apns_alert' => [
                'title' => $title,
                'body' => $body
            ]
        ];

        if ($appUrl) {
            $payload['app_url'] = $appUrl;
        }

        try {
            $client = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json'
            ]);

            // Prefer using a trusted CA bundle if available
            $certPath = base_path('certs/cacert.pem');
            if (file_exists($certPath)) {
                $client = $client->withOptions(['verify' => $certPath]);
            } elseif (app()->environment('local')) {
                // Safe fallback for local dev only to bypass cURL error 60
                $client = $client->withOptions(['verify' => false]);
            }

            $response = $client->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                // Log entire response for diagnostics
                Log::info('OneSignal response (success)', [
                    'response' => $responseData,
                    'title' => $title,
                    'payload_sample' => [
                        'app_id' => $payload['app_id'] ?? null,
                        'included_segments' => $payload['included_segments'] ?? null,
                    ],
                ]);

                // If API returns errors in a 200 response
                if (isset($responseData['errors'])) {
                    Log::warning('OneSignal returned errors with 200 OK', [
                        'errors' => $responseData['errors'],
                    ]);
                    return false;
                }

                // Warn when id missing or recipients == 0 (audience empty)
                if (empty($responseData['id']) || (($responseData['recipients'] ?? 0) === 0)) {
                    Log::warning('OneSignal accepted request but audience appears empty', [
                        'id' => $responseData['id'] ?? null,
                        'recipients' => $responseData['recipients'] ?? 0,
                    ]);
                }

                return true;
            } else {
                Log::error('OneSignal request failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('OneSignal notification failed', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
            return false;
        }
    }

    /**
     * Send to specific OneSignal player IDs (direct target)
     */
    public function sendToPlayers(array $playerIds, string $title, string $body, array $data = [])
    {
        if (empty($playerIds)) {
            Log::warning('sendToPlayers called with empty playerIds');
            return false;
        }

        // Lift optional app_url from data if provided
        $appUrl = null;
        if (isset($data['app_url']) && is_string($data['app_url'])) {
            $appUrl = $data['app_url'];
            unset($data['app_url']);
        }

        $payload = [
            'app_id' => $this->appId,
            'include_player_ids' => array_values(array_filter($playerIds)),
            'headings' => ['en' => $title, 'ar' => $title],
            'contents' => ['en' => $body, 'ar' => $body],
            'data' => $data,
            'target_channel' => 'push',
            // Android specific settings
            'android_accent_color' => 'FF2196F3',
            'small_icon' => 'ic_notification',
            'large_icon' => 'ic_launcher',
            'priority' => 10,
            // iOS specific settings
            'ios_badgeType' => 'Increase',
            'ios_badgeCount' => 1,
            'ios_sound' => 'default',
            'ios_category' => 'general',
            'apns_alert' => [
                'title' => $title,
                'body' => $body
            ]
        ];

        if ($appUrl) {
            $payload['app_url'] = $appUrl;
        }

        try {
            $client = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json'
            ]);

            $certPath = base_path('certs/cacert.pem');
            if (file_exists($certPath)) {
                $client = $client->withOptions(['verify' => $certPath]);
            } elseif (app()->environment('local')) {
                $client = $client->withOptions(['verify' => false]);
            }

            $response = $client->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('OneSignal direct send response (success)', [
                    'response' => $responseData,
                    'player_ids' => $playerIds,
                ]);

                if (isset($responseData['errors'])) {
                    Log::warning('OneSignal direct send returned errors', [
                        'errors' => $responseData['errors'],
                    ]);
                    return false;
                }

                return true;
            }

            Log::error('OneSignal direct send request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('OneSignal direct send failed', [
                'error' => $e->getMessage(),
                'player_ids' => $playerIds,
            ]);
            return false;
        }
    }

    /**
     * Send notification when a new location is added
     */
    public function sendLocationNotification($location)
    {
        $title = 'موقع جديد تم إضافته! 📍';
        $body = "تم إضافة موقع جديد: {$location->name_ar}";
        $appUrl = $this->buildAppUrl('location', (int) $location->id);
        $data = [
            'type' => 'location',
            'location_id' => $location->id,
            'location_name' => $location->name_ar,
            'action' => 'view_location',
            'url' => $appUrl,
            'app_url' => $appUrl
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_LOCATION, $data);
    }

    /**
     * Send notification when a new event is added
     */
    public function sendEventNotification($event)
    {
        $title = 'حدث جديد! 🎉';
        $body = "تم إضافة حدث جديد: {$event->title_ar}";
        $appUrl = $this->buildAppUrl('event', (int) $event->id);
        $data = [
            'type' => 'event',
            'event_id' => $event->id,
            'event_title' => $event->title_ar,
            'action' => 'view_event',
            'url' => $appUrl,
            'app_url' => $appUrl
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_EVENT, $data);
    }

    /**
     * Send notification when a new news is added
     */
    public function sendNewsNotification($news)
    {
        $title = 'خبر جديد! 📰';
        $body = "تم نشر خبر جديد: {$news->title_ar}";
        $appUrl = $this->buildAppUrl('news', (int) $news->id);
        $data = [
            'type' => 'news',
            'news_id' => $news->id,
            'news_title' => $news->title_ar,
            'action' => 'view_news',
            'url' => $appUrl,
            'app_url' => $appUrl
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_NEWS, $data);
    }

    /**
     * Send notification when a new investment is added
     */
    public function sendInvestmentNotification($investment)
    {
        $title = 'استثمار جديد! 💰';
        $body = "تم إضافة فرصة استثمارية جديدة: {$investment->title_ar}";
        $appUrl = $this->buildAppUrl('investment', (int) $investment->id);
        $data = [
            'type' => 'investment',
            'investment_id' => $investment->id,
            'investment_title' => $investment->title_ar,
            'action' => 'view_investment',
            'url' => $appUrl,
            'app_url' => $appUrl
        ];

        return $this->sendToAllUsers($title, $body, Notification::TYPE_INVESTMENT, $data);
    }

    /**
     * Add user to OneSignal with tags
     */
    public function addUserTags($playerId, $tags = [])
    {
        $url = "https://onesignal.com/api/v1/players/{$playerId}";

        $payload = [
            'app_id' => $this->appId,
            'tags' => $tags
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->restApiKey,
                'Content-Type' => 'application/json'
            ])->put($url, $payload);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('OneSignal add tags failed', [
                'error' => $e->getMessage(),
                'player_id' => $playerId
            ]);
            return false;
        }
    }
}
