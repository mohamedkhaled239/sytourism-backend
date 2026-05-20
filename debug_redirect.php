<?php
/**
 * ملف تشخيص مشكلة إعادة التوجيه المتكررة
 * ضع هذا الملف في مجلد public واذهب إليه مباشرة
 */

// بدء الجلسة
session_start();

// مسح جميع الـ cookies
if (isset($_GET['clear_cookies'])) {
    // مسح جميع الـ cookies
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, '', time() - 3600, '/');
        setcookie($name, '', time() - 3600, '/admin');
        setcookie($name, '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
    }
    
    // مسح الجلسة
    session_destroy();
    
    echo "<h2>تم مسح جميع الـ cookies والـ sessions</h2>";
    echo "<p><a href='?'>إعادة تحميل الصفحة</a></p>";
    exit;
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تشخيص مشكلة إعادة التوجيه</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-right: 4px solid #007cba;
        }
        .button {
            display: inline-block;
            background: #007cba;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
        }
        .danger {
            background: #dc3545;
        }
        .success {
            background: #28a745;
        }
        .warning {
            background: #ffc107;
            color: #000;
        }
        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 تشخيص مشكلة إعادة التوجيه المتكررة</h1>
        
        <div class="section">
            <h3>📊 معلومات الطلب الحالي:</h3>
            <pre><?php
echo "URL الحالي: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
echo "الوقت: " . date('Y-m-d H:i:s') . "\n";
            ?></pre>
        </div>

        <div class="section">
            <h3>🍪 الـ Cookies الحالية:</h3>
            <pre><?php
if (!empty($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
        echo "$name: $value\n";
    }
} else {
    echo "لا توجد cookies";
}
            ?></pre>
        </div>

        <div class="section">
            <h3>📝 بيانات الجلسة:</h3>
            <pre><?php
echo "Session ID: " . session_id() . "\n";
echo "Session Status: " . session_status() . "\n";
if (!empty($_SESSION)) {
    print_r($_SESSION);
} else {
    echo "لا توجد بيانات في الجلسة";
}
            ?></pre>
        </div>

        <div class="section">
            <h3>🧪 اختبارات التوجيه:</h3>
            
            <p><strong>1. اختبار صفحة تسجيل دخول الأدمن:</strong></p>
            <a href="/admin/login" class="button">الذهاب إلى /admin/login</a>
            
            <p><strong>2. اختبار مسار غير موجود:</strong></p>
            <a href="/non-existent-page-test" class="button warning">اختبار مسار غير موجود</a>
            
            <p><strong>3. اختبار API route:</strong></p>
            <a href="/api/test-404" class="button">اختبار API 404</a>
            
            <p><strong>4. مسح جميع البيانات:</strong></p>
            <a href="?clear_cookies=1" class="button danger">مسح جميع الـ Cookies والـ Sessions</a>
        </div>

        <div class="section">
            <h3>🔧 خطوات حل المشكلة:</h3>
            <ol>
                <li>امسح جميع الـ cookies والـ sessions باستخدام الزر أعلاه</li>
                <li>جرب الذهاب إلى صفحة تسجيل دخول الأدمن</li>
                <li>إذا استمرت المشكلة، تحقق من إعدادات الخادم</li>
                <li>تأكد من أن Laravel routes تعمل بشكل صحيح</li>
            </ol>
        </div>

        <div class="section">
            <h3>📋 معلومات إضافية:</h3>
            <pre><?php
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "\n";
            ?></pre>
        </div>

        <div class="section">
            <h3>🔄 إعادة تحميل:</h3>
            <a href="?" class="button success">إعادة تحميل هذه الصفحة</a>
        </div>
    </div>

    <script>
        // تحديث الصفحة كل 30 ثانية لمراقبة التغييرات
        setTimeout(function() {
            if (confirm('هل تريد إعادة تحميل الصفحة لمراقبة التغييرات؟')) {
                location.reload();
            }
        }, 30000);
    </script>
</body>
</html>
