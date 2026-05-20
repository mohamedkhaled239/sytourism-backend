<?php
/**
 * ملف اختبار مباشر لصفحة تسجيل دخول الأدمن
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار صفحة الأدمن</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .button {
            display: inline-block;
            background: #007cba;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
            font-size: 16px;
        }
        .success {
            background: #28a745;
        }
        .danger {
            background: #dc3545;
        }
        .info {
            background: #17a2b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 اختبار صفحة تسجيل دخول الأدمن</h1>
        
        <p>هذا اختبار مباشر للتأكد من أن الروابط تعمل بشكل صحيح</p>
        
        <div>
            <h3>اختبارات التوجيه:</h3>
            
            <a href="/admin/login" class="button success">
                الذهاب إلى صفحة تسجيل دخول الأدمن
            </a>
            
            <a href="/admin/dashboard" class="button info">
                الذهاب إلى لوحة تحكم الأدمن
            </a>
            
            <a href="/non-existent-page" class="button danger">
                اختبار صفحة غير موجودة
            </a>
        </div>
        
        <div style="margin-top: 30px;">
            <h3>معلومات الخادم:</h3>
            <p><strong>الوقت:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>الخادم:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
            <p><strong>المسار:</strong> <?php echo $_SERVER['REQUEST_URI']; ?></p>
        </div>
        
        <div style="margin-top: 20px;">
            <small>إذا واجهت مشكلة في إعادة التوجيه المتكررة، امسح cookies المتصفح وحاول مرة أخرى</small>
        </div>
    </div>
</body>
</html>
