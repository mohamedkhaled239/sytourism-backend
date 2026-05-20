<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Cloud Messaging push notifications
    |
    */

    'server_key' => env('FCM_SERVER_KEY'),
    
    'sender_id' => env('FCM_SENDER_ID'),
    
    'fcm_url' => 'https://fcm.googleapis.com/fcm/send',
    
    'legacy_url' => 'https://fcm.googleapis.com/fcm/send',
    
    'v1_url' => 'https://fcm.googleapis.com/v1/projects/',

    /*
    |--------------------------------------------------------------------------
    | Default Notification Settings
    |--------------------------------------------------------------------------
    */
    
    'default_sound' => 'default',
    
    'default_badge' => 1,
    
    'default_priority' => 'high',

];
