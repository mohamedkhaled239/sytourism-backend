<?php
/**
 * اختبار ميزة إعادة التوجيه للمسارات غير الموجودة
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار إعادة التوجيه 404</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .test-section {
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            border-right: 4px solid #007cba;
        }
        .button {
            display: inline-block;
            background: #007cba;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            font-weight: bold;
        }
        .success { background: #28a745; }
        .danger { background: #dc3545; }
        .warning { background: #ffc107; color: #000; }
        .info { background: #17a2b8; }
        
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .status.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 اختبار ميزة إعادة التوجيه للمسارات غير الموجودة (404)</h1>
        
        <div class="status success">
            <strong>✅ الميزة مفعلة الآن!</strong><br>
            أي مسار غير موجود سيتم توجيهه تلقائياً إلى صفحة تسجيل دخول الأدمن مع مسح الـ sessions والـ cookies
        </div>

        <div class="test-section">
            <h3>🎯 اختبار المسارات غير الموجودة</h3>
            <p>هذه المسارات غير موجودة ويجب أن تعيد توجيهك إلى صفحة تسجيل دخول الأدمن:</p>
            
            <a href="/random-page-123" class="button danger">
                /random-page-123
            </a>
            
            <a href="/non-existent-route" class="button danger">
                /non-existent-route
            </a>
            
            <a href="/admin/loginfds" class="button danger">
                /admin/loginfds (مثالك)
            </a>
            
            <a href="/some/deep/path/that/does/not/exist" class="button danger">
                /some/deep/path/that/does/not/exist
            </a>
            
            <div class="status warning">
                <strong>⚠️ تحذير:</strong> الضغط على هذه الروابط سيمسح جميع الـ sessions والـ cookies!
            </div>
        </div>

        <div class="test-section">
            <h3>🛡️ المسارات المحمية (لا يتم إعادة توجيهها)</h3>
            <p>هذه المسارات محمية ولن يتم إعادة توجيهها:</p>
            
            <a href="/api/non-existent" class="button info">
                /api/non-existent (API route)
            </a>
            
            <a href="/clear-session" class="button success">
                /clear-session (أداة مسح البيانات)
            </a>
            
            <a href="/test-routes.php" class="button info">
                /test-routes.php (ملف PHP)
            </a>
            
            <div class="status info">
                <strong>ℹ️ معلومة:</strong> هذه المسارات ستعرض 404 عادي أو تعمل بشكل طبيعي
            </div>
        </div>

        <div class="test-section">
            <h3>✅ المسارات التي تعمل بشكل طبيعي</h3>
            <p>هذه المسارات موجودة ويجب أن تعمل بشكل طبيعي:</p>
            
            <a href="/admin/login" class="button success">
                /admin/login (صفحة تسجيل الدخول)
            </a>
            
            <a href="/admin/dashboard" class="button info">
                /admin/dashboard (لوحة التحكم)
            </a>
            
            <a href="/" class="button">
                / (الصفحة الرئيسية)
            </a>
        </div>

        <div class="test-section">
            <h3>📋 كيف تعمل الميزة</h3>
            <ol>
                <li><strong>المستخدم يدخل مسار غير موجود</strong> (مثل /random-page)</li>
                <li><strong>النظام يكتشف أنه 404</strong></li>
                <li><strong>يتم مسح جميع الـ sessions والـ cookies</strong></li>
                <li><strong>يتم إعادة التوجيه إلى /admin/login</strong></li>
                <li><strong>المستخدم يبدأ بجلسة نظيفة تماماً</strong></li>
            </ol>
        </div>

        <div class="test-section">
            <h3>🔧 الاستثناءات</h3>
            <p>هذه المسارات لا يتم إعادة توجيهها:</p>
            <ul>
                <li>✅ <code>/api/*</code> - جميع مسارات API</li>
                <li>✅ <code>/clear-session</code> - أداة مسح البيانات</li>
                <li>✅ <code>/test-*</code> - ملفات الاختبار</li>
                <li>✅ <code>/debug_*</code> - ملفات التشخيص</li>
                <li>✅ <code>*.php</code> - الملفات المباشرة</li>
                <li>✅ طلبات JSON - AJAX requests</li>
            </ul>
        </div>

        <div class="test-section">
            <h3>📊 إحصائيات الاختبار</h3>
            <div style="background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace;">
                <strong>الوقت:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
                <strong>الخادم:</strong> <?php echo $_SERVER['HTTP_HOST']; ?><br>
                <strong>المسار الحالي:</strong> <?php echo $_SERVER['REQUEST_URI']; ?><br>
                <strong>عدد الاختبارات المتاحة:</strong> 7 مسارات غير موجودة + 3 مسارات محمية + 3 مسارات عادية
            </div>
        </div>

        <div class="test-section">
            <h3>🔄 أدوات إضافية</h3>
            <a href="?" class="button">إعادة تحميل هذه الصفحة</a>
            <a href="/admin/login" class="button success">الذهاب إلى صفحة تسجيل الدخول</a>
            <a href="/clear-session" class="button warning">مسح الجلسة والـ Cookies</a>
        </div>
    </div>

    <script>
        // إضافة تأكيد للروابط التي تمسح البيانات
        document.querySelectorAll('a.danger').forEach(function(link) {
            link.addEventListener('click', function(e) {
                if (!confirm('هذا الرابط سيمسح جميع الـ sessions والـ cookies ويعيد توجيهك إلى صفحة تسجيل دخول الأدمن. هل تريد المتابعة؟')) {
                    e.preventDefault();
                }
            });
        });
        
        // إضافة تأكيد لأداة مسح البيانات
        document.querySelectorAll('a[href="/clear-session"]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                if (!confirm('هل أنت متأكد من مسح الجلسة والـ cookies؟')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
