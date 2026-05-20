# إعداد OneSignal للـ iOS

## المعلومات المطلوبة من OneSignal:
- **App ID**: `6c90f2f7-bf1d-4854-84a7-f42ebd727a52`
- **Bundle ID**: `com.tolba.sytourism`
- **Certificate**: `.p12 Certificate` (ينتهي في 7 أكتوبر 2026)

## الخطوات المطلوبة:

### 1. إعداد iOS في مشروع Flutter

#### أ. تحديث `ios/Runner/Info.plist`:
```xml
<!-- إضافة هذه الأسطر داخل <dict> -->
<key>UIBackgroundModes</key>
<array>
    <string>remote-notification</string>
</array>

<!-- إضافة إعدادات OneSignal -->
<key>OneSignal_APPID</key>
<string>6c90f2f7-bf1d-4854-84a7-f42ebd727a52</string>

<!-- إعدادات النوتيفيكيشن -->
<key>NSUserNotificationAlertStyle</key>
<string>alert</string>
```

#### ب. تحديث `ios/Runner/AppDelegate.swift`:
```swift
import UIKit
import Flutter
import OneSignalFramework

@UIApplicationMain
@objc class AppDelegate: FlutterAppDelegate {
  override func application(
    _ application: UIApplication,
    didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?
  ) -> Bool {
    GeneratedPluginRegistrant.register(with: self)
    
    // Remove this method to stop OneSignal Debugging
    OneSignal.Debug.setLogLevel(.LL_VERBOSE)
    
    // OneSignal initialization
    OneSignal.initialize("6c90f2f7-bf1d-4854-84a7-f42ebd727a52", withLaunchOptions: launchOptions)
    
    // requestPermission will show the native iOS notification permission prompt.
    OneSignal.Notifications.requestPermission({ accepted in
      print("User accepted notifications: \(accepted)")
    }, fallbackToSettings: true)
    
    return super.application(application, didFinishLaunchingWithOptions: launchOptions)
  }
}
```

#### ج. تحديث `pubspec.yaml`:
```yaml
dependencies:
  flutter:
    sdk: flutter
  onesignal_flutter: ^5.0.4  # أحدث إصدار
  # باقي dependencies...
```

### 2. إعداد Apple Developer Console

#### أ. إنشاء App ID:
1. اذهب إلى [Apple Developer Console](https://developer.apple.com/account/)
2. اختر "Certificates, Identifiers & Profiles"
3. اختر "Identifiers" ثم "App IDs"
4. أنشئ App ID جديد بـ Bundle ID: `com.tolba.sytourism`
5. فعّل "Push Notifications" capability

#### ب. إنشاء Push Notification Certificate:
1. في نفس القسم، اختر "Certificates"
2. أنشئ certificate جديد من نوع "Apple Push Notification service SSL"
3. اختر App ID الذي أنشأته
4. حمّل Certificate Signing Request (CSR)
5. حمّل الـ certificate وحوّله إلى .p12

#### ج. رفع Certificate إلى OneSignal:
1. اذهب إلى [OneSignal Dashboard](https://app.onesignal.com/)
2. اختر التطبيق الخاص بك
3. اذهب إلى Settings > Platforms
4. اختر "Apple iOS (APNs)"
5. ارفع ملف .p12 certificate
6. أدخل password الـ certificate

### 3. إعداد Deep Links للـ iOS

#### أ. تحديث `ios/Runner/Info.plist`:
```xml
<!-- إضافة URL Schemes -->
<key>CFBundleURLTypes</key>
<array>
    <dict>
        <key>CFBundleURLName</key>
        <string>com.tolba.sytourism.deeplink</string>
        <key>CFBundleURLSchemes</key>
        <array>
            <string>tourism_app</string>
            <string>seaha</string>
        </array>
    </dict>
</array>
```

### 4. اختبار النوتيفيكيشن

#### أ. اختبار من OneSignal Dashboard:
1. اذهب إلى "Messages" > "New Push"
2. اكتب العنوان والمحتوى
3. في "Audience" اختر "Send to Test Device"
4. أدخل Player ID الخاص بجهازك
5. أرسل النوتيفيكيشن

#### ب. اختبار من Laravel:
```php
// في Laravel Controller أو Artisan Command
use App\Services\OneSignalService;

$oneSignal = new OneSignalService();
$oneSignal->sendNotification(
    'اختبار iOS',
    'هذه رسالة اختبار للـ iOS',
    [
        'type' => 'test',
        'app_url' => 'tourism_app://test/1'
    ]
);
```

### 5. نصائح مهمة للـ iOS:

1. **الصلاحيات**: iOS يتطلب موافقة صريحة من المستخدم للنوتيفيكيشن
2. **Background App Refresh**: تأكد من تفعيله في إعدادات الجهاز
3. **Certificate Expiry**: تابع تاريخ انتهاء الـ certificate (7 أكتوبر 2026)
4. **Testing**: استخدم جهاز iOS حقيقي للاختبار (المحاكي لا يدعم النوتيفيكيشن)
5. **Bundle ID**: تأكد من أن Bundle ID في Xcode يطابق ما في OneSignal

### 6. استكشاف الأخطاء:

#### إذا لم تصل النوتيفيكيشن:
1. تحقق من Player ID في OneSignal Dashboard
2. تأكد من أن Certificate صحيح ولم ينته
3. تحقق من إعدادات النوتيفيكيشن في الجهاز
4. راجع logs في Xcode Console

#### إذا لم تعمل Deep Links:
1. تحقق من URL Schemes في Info.plist
2. تأكد من أن Deep Link Service يتعامل مع الـ URLs بشكل صحيح
3. اختبر الـ Deep Links يدوياً من Safari

### 7. الملفات المطلوب تحديثها:

- ✅ `app/Services/OneSignalService.php` - تم تحديثه لدعم iOS
- ✅ `.env` - تم تحديث App ID
- ✅ `notification_service.dart` - تم إنشاؤه
- ⏳ `ios/Runner/Info.plist` - يحتاج تحديث
- ⏳ `ios/Runner/AppDelegate.swift` - يحتاج تحديث
- ⏳ `pubspec.yaml` - تحقق من إصدار onesignal_flutter

بعد تطبيق هذه الخطوات، ستكون النوتيفيكيشن تعمل على كل من Android و iOS بنفس المعمارية.
