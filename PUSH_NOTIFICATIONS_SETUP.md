# Push Notifications Setup Guide

## Overview
This system implements push notifications for the Seaha tourism platform using Firebase Cloud Messaging (FCM). Notifications are automatically sent to all users who have notifications enabled whenever new content is added (locations, events, news, investments).

## Features Implemented

### 1. Database Structure
- **notifications table**: Stores all notification records
- **FCM token field**: Added to users table for device identification

### 2. API Endpoints
All endpoints require authentication (`auth:sanctum` middleware):

- `GET /api/notifications/latest` - Get the latest notification for each type
- `GET /api/notifications` - Get all notifications with pagination
- `GET /api/notifications/stats` - Get notification statistics
- `POST /api/notifications/fcm-token` - Update user's FCM token

### 3. Notification Types
- `location` - New tourism location added
- `event` - New event created
- `news` - New news article published
- `investment` - New investment opportunity added

### 4. Automatic Triggers
Push notifications are automatically sent when:
- Admin creates a new location
- Admin creates a new event
- Admin creates a new news article
- Admin creates a new investment

## Setup Instructions

### 1. Environment Configuration
Add these variables to your `.env` file:

```env
# Firebase Cloud Messaging
FCM_SERVER_KEY=your_firebase_server_key_here
FCM_SENDER_ID=your_firebase_sender_id_here
```

### 2. Database Migration
Run the migrations to create the necessary tables:

```bash
php artisan migrate
```

### 3. Firebase Project Setup
1. Go to [Firebase Console](https://console.firebase.google.com/)
2. Create a new project or select existing one
3. Go to Project Settings > Cloud Messaging
4. Copy the Server Key and Sender ID to your `.env` file

### 4. Flutter App Integration
In your Flutter app, you need to:

1. **Install Firebase Messaging**:
   ```yaml
   dependencies:
     firebase_messaging: ^14.0.0
   ```

2. **Initialize FCM and get token**:
   ```dart
   FirebaseMessaging messaging = FirebaseMessaging.instance;
   String? token = await messaging.getToken();
   ```

3. **Send token to API**:
   ```dart
   // Send token to your API endpoint
   await http.post(
     Uri.parse('${baseUrl}/api/notifications/fcm-token'),
     headers: {
       'Authorization': 'Bearer $userToken',
       'Content-Type': 'application/json',
     },
     body: json.encode({'fcm_token': token}),
   );
   ```

4. **Handle notifications**:
   ```dart
   FirebaseMessaging.onMessage.listen((RemoteMessage message) {
     // Handle foreground notifications
   });
   
   FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
     // Handle notification tap
   });
   ```

## API Usage Examples

### Get Latest Notifications
```http
GET /api/notifications/latest
Authorization: Bearer {token}
```

Response:
```json
{
  "success": true,
  "message": "تم جلب آخر الإشعارات بنجاح",
  "data": {
    "location": {
      "id": 1,
      "title": "موقع جديد تم إضافته!",
      "body": "تم إضافة موقع جديد: الأهرامات",
      "type": "location",
      "data": {
        "location_id": 5,
        "location_name": "الأهرامات"
      },
      "created_at": "2024-01-01T12:00:00Z"
    }
  }
}
```

### Update FCM Token
```http
POST /api/notifications/fcm-token
Authorization: Bearer {token}
Content-Type: application/json

{
  "fcm_token": "device_fcm_token_here"
}
```

### Get All Notifications
```http
GET /api/notifications?type=location&per_page=10
Authorization: Bearer {token}
```

## Notification Data Structure

Each notification contains:
- `title`: Notification title in Arabic
- `body`: Notification message in Arabic
- `type`: One of: location, event, news, investment
- `data`: Additional data (ID, name, etc.)
- `sent_at`: When the notification was sent
- `created_at`: When the notification was created

## Testing

1. **Create test content** through the admin panel
2. **Check notification creation** in the database
3. **Verify FCM delivery** using Firebase Console
4. **Test API endpoints** with authenticated requests

## Troubleshooting

### Common Issues:
1. **FCM Server Key not set**: Check `.env` file
2. **No notifications received**: Verify user has `notifications_enabled = true` and valid `fcm_token`
3. **API errors**: Check authentication and required parameters

### Logs:
Check Laravel logs for push notification errors:
```bash
tail -f storage/logs/laravel.log
```

## Security Notes

- FCM Server Key should be kept secure in `.env` file
- Never expose server key in client-side code
- Validate all API inputs
- Use proper authentication for all endpoints

## Future Enhancements

- Add notification preferences per type
- Implement notification scheduling
- Add rich notifications with images
- Support for multiple languages
- Notification analytics and tracking
