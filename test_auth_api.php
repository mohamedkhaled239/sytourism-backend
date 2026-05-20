<?php
/**
 * ملف اختبار API التحقق من البريد الإلكتروني المحدث
 * يمكن تشغيله من terminal أو browser لاختبار endpoints
 */

// تكوين أساسي
$baseUrl = 'http://localhost/seaha2/api';
$headers = [
    'Content-Type: application/json',
    'Accept: application/json'
];

// بيانات تجريبية للاختبار
$testUser = [
    'full_name' => 'مستخدم تجريبي',
    'username' => 'testuser_' . time(),
    'email' => 'test_' . time() . '@example.com',
    'phone' => '1234567890',
    'country' => 'السعودية',
    'user_type' => 'tourist',
    'password' => 'password123',
    'password_confirmation' => 'password123'
];

echo "=== اختبار API التحقق من البريد الإلكتروني ===\n\n";

// دالة لإرسال طلب HTTP
function sendRequest($url, $data = null, $method = 'GET', $headers = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

// 1. اختبار التسجيل
echo "1. اختبار التسجيل...\n";
$registerResponse = sendRequest($baseUrl . '/auth/register', $testUser, 'POST', $headers);
echo "كود الاستجابة: " . $registerResponse['code'] . "\n";
echo "الاستجابة: " . json_encode($registerResponse['body'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";

if ($registerResponse['code'] === 201 && $registerResponse['body']['success']) {
    echo "✅ التسجيل نجح!\n\n";
    
    // 2. اختبار إعادة إرسال رمز التحقق
    echo "2. اختبار إعادة إرسال رمز التحقق...\n";
    $resendData = ['email' => $testUser['email']];
    $resendResponse = sendRequest($baseUrl . '/auth/resend-email-verification', $resendData, 'POST', $headers);
    echo "كود الاستجابة: " . $resendResponse['code'] . "\n";
    echo "الاستجابة: " . json_encode($resendResponse['body'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
    
    // 3. اختبار تسجيل الدخول قبل التحقق
    echo "3. اختبار تسجيل الدخول قبل التحقق من البريد...\n";
    $loginData = [
        'email' => $testUser['email'],
        'password' => $testUser['password']
    ];
    $loginResponse = sendRequest($baseUrl . '/auth/login', $loginData, 'POST', $headers);
    echo "كود الاستجابة: " . $loginResponse['code'] . "\n";
    echo "الاستجابة: " . json_encode($loginResponse['body'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
    
    if ($loginResponse['code'] === 403 && $loginResponse['body']['error_code'] === 'EMAIL_NOT_VERIFIED') {
        echo "✅ منع تسجيل الدخول قبل التحقق يعمل بشكل صحيح!\n\n";
    }
    
    // 4. محاكاة التحقق من البريد الإلكتروني (رمز وهمي)
    echo "4. اختبار التحقق من البريد الإلكتروني برمز وهمي...\n";
    $verifyData = [
        'email' => $testUser['email'],
        'code' => '123456' // رمز وهمي للاختبار
    ];
    $verifyResponse = sendRequest($baseUrl . '/auth/verify-email', $verifyData, 'POST', $headers);
    echo "كود الاستجابة: " . $verifyResponse['code'] . "\n";
    echo "الاستجابة: " . json_encode($verifyResponse['body'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n\n";
    
    if ($verifyResponse['code'] === 400 && $verifyResponse['body']['error_code'] === 'INVALID_VERIFICATION_CODE') {
        echo "✅ التحقق من صحة الرمز يعمل بشكل صحيح!\n\n";
    }
    
} else {
    echo "❌ فشل التسجيل!\n\n";
}

// 5. اختبار endpoints أخرى
echo "5. اختبار endpoints عامة...\n";

// اختبار endpoint غير موجود
$notFoundResponse = sendRequest($baseUrl . '/nonexistent', null, 'GET', $headers);
echo "اختبار endpoint غير موجود - كود: " . $notFoundResponse['code'] . "\n";

// اختبار بدون headers صحيحة
$noHeadersResponse = sendRequest($baseUrl . '/auth/register', $testUser, 'POST', []);
echo "اختبار بدون JSON headers - كود: " . $noHeadersResponse['code'] . "\n\n";

echo "=== انتهى الاختبار ===\n";

// معلومات إضافية للمطور
echo "\n=== معلومات للمطور ===\n";
echo "• تأكد من تشغيل خادم الويب على localhost\n";
echo "• تأكد من أن قاعدة البيانات متصلة\n";
echo "• تحقق من logs Laravel في storage/logs/laravel.log\n";
echo "• يمكن تشغيل هذا الملف من المتصفح أو terminal\n";
echo "• للحصول على رمز التحقق الحقيقي، تحقق من جدول users في قاعدة البيانات\n";
?>
