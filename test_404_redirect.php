<?php
/**
 * ملف اختبار سريع لتجربة نظام إعادة التوجيه مع مسح Sessions و Cookies
 * 
 * لاستخدام هذا الملف:
 * 1. ضعه في مجلد public
 * 2. اذهب إلى http://yoursite.com/test_404_redirect.php
 * 3. اتبع التعليمات لاختبار النظام
 */

// بدء الجلسة
session_start();

// إعداد بعض البيانات للاختبار
if (!isset($_SESSION['test_data'])) {
    $_SESSION['test_data'] = 'هذه بيانات اختبار في الجلسة';
    $_SESSION['user_id'] = 12345;
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
}

// إعداد بعض الـ cookies للاختبار
if (!isset($_COOKIE['test_cookie'])) {
    setcookie('test_cookie', 'قيمة اختبار للكوكيز', time() + 3600, '/');
    setcookie('another_cookie', 'قيمة أخرى', time() + 3600, '/');
}

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار نظام إعادة التوجيه 404</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            border-bottom: 3px solid #007cba;
            padding-bottom: 10px;
        }
        .section {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            border-right: 4px solid #007cba;
        }
        .test-button {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .test-button:hover {
            background: #c82333;
        }
        .success {
            background: #28a745;
        }
        .success:hover {
            background: #218838;
        }
        .info {
            background: #17a2b8;
        }
        .info:hover {
            background: #138496;
        }
        .data-display {
            background: #e9ecef;
            padding: 10px;
            border-radius: 3px;
            font-family: monospace;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 اختبار نظام إعادة التوجيه 404 مع مسح Sessions و Cookies</h1>
        
        <div class="warning">
            <strong>⚠️ تحذير:</strong> هذا الاختبار سيمسح جميع الـ sessions والـ cookies الخاصة بك!
        </div>

        <div class="section">
            <h3>📊 البيانات الحالية في الجلسة:</h3>
            <div class="data-display">
                <?php
                echo "Session ID: " . session_id() . "\n";
                echo "البيانات المحفوظة:\n";
                foreach ($_SESSION as $key => $value) {
                    echo "- $key: $value\n";
                }
                ?>
            </div>
        </div>

        <div class="section">
            <h3>🍪 الـ Cookies الحالية:</h3>
            <div class="data-display">
                <?php
                if (!empty($_COOKIE)) {
                    foreach ($_COOKIE as $name => $value) {
                        echo "- $name: $value\n";
                    }
                } else {
                    echo "لا توجد cookies محفوظة حالياً";
                }
                ?>
            </div>
        </div>

        <div class="section">
            <h3>🧪 اختبارات النظام:</h3>
            
            <p><strong>1. اختبار إعادة التوجيه مع مسح البيانات:</strong></p>
            <a href="/non-existent-page-test" class="test-button">
                اختبار مسار غير موجود (سيمسح البيانات)
            </a>
            
            <p><strong>2. اختبار API route (لا يجب أن يعيد التوجيه):</strong></p>
            <a href="/api/non-existent-api-route" class="test-button info">
                اختبار API route غير موجود
            </a>
            
            <p><strong>3. اختبار مسار موجود (يجب أن يعمل طبيعياً):</strong></p>
            <a href="/admin/login" class="test-button success">
                الذهاب إلى صفحة تسجيل دخول الأدمن
            </a>
        </div>

        <div class="section">
            <h3>📝 التعليمات:</h3>
            <ol>
                <li>لاحظ البيانات المعروضة أعلاه (Session و Cookies)</li>
                <li>اضغط على "اختبار مسار غير موجود" - يجب أن يتم توجيهك إلى صفحة الأدمن</li>
                <li>ارجع إلى هذه الصفحة وستلاحظ أن البيانات تم مسحها</li>
                <li>جرب اختبار API route - يجب أن ترى صفحة خطأ 404 JSON</li>
                <li>جرب الذهاب إلى صفحة الأدمن - يجب أن تعمل بشكل طبيعي</li>
            </ol>
        </div>

        <div class="section">
            <h3>🔄 إعادة تحميل البيانات:</h3>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="test-button info">
                إعادة تحميل الصفحة
            </a>
            <small>لإعادة إنشاء بيانات الاختبار</small>
        </div>

        <div class="section">
            <h3>ℹ️ معلومات إضافية:</h3>
            <p><strong>الوقت الحالي:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>عنوان IP:</strong> <?php echo $_SERVER['REMOTE_ADDR'] ?? 'غير معروف'; ?></p>
            <p><strong>User Agent:</strong> <?php echo $_SERVER['HTTP_USER_AGENT'] ?? 'غير معروف'; ?></p>
        </div>
    </div>

    <script>
        // إضافة تأكيد قبل اختبار المسح
        document.querySelector('a[href="/non-existent-page-test"]').addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد؟ سيتم مسح جميع بيانات الجلسة والـ cookies!')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
