<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز تسجيل الدخول</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .code-container {
            background-color: #f8f9fa;
            border: 2px dashed #28a745;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            position: relative;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #28a745;
            letter-spacing: 5px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .copy-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 15px;
            transition: background-color 0.3s;
        }
        .copy-button:hover {
            background-color: #218838;
        }
        .copy-button:active {
            background-color: #1e7e34;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .timer {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #721c24;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 5px;
            }
            .header, .content {
                padding: 20px;
            }
            .code {
                font-size: 24px;
                letter-spacing: 3px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔑 رمز تسجيل الدخول</h1>
            <p>مرحباً بك مرة أخرى في منصة سياحة</p>
        </div>
        
        <div class="content">
            <h2>رمز تسجيل الدخول الخاص بك</h2>
            <p>استخدم الرمز التالي لإكمال عملية تسجيل الدخول:</p>
            
            <div class="code-container">
                <div class="code" id="loginOtp">{{ $otp }}</div>
                <button class="copy-button" onclick="copyCode()">📋 نسخ الرمز</button>
                <div id="copyMessage" style="color: green; margin-top: 10px; display: none;">✅ تم نسخ الرمز بنجاح!</div>
            </div>
            
            <div class="timer">
                ⏰ هذا الرمز صالح لمدة 10 دقائق فقط من وقت الإرسال.
            </div>
            
            <div class="warning">
                ⚠️ إذا لم تحاول تسجيل الدخول، يرجى تجاهل هذا الإيميل وتغيير كلمة المرور فوراً.
            </div>
            
            <p>للأمان، لا تشارك هذا الرمز مع أي شخص آخر.</p>
        </div>
        
        <div class="footer">
            <p>© 2024 منصة سياحة. جميع الحقوق محفوظة.</p>
            <p>هذا إيميل تلقائي، يرجى عدم الرد عليه.</p>
        </div>
    </div>

    <script>
        function copyCode() {
            const code = document.getElementById('loginOtp').textContent;
            
            // طريقة حديثة للنسخ
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(function() {
                    showCopyMessage();
                }).catch(function(err) {
                    fallbackCopyTextToClipboard(code);
                });
            } else {
                // طريقة بديلة للمتصفحات القديمة
                fallbackCopyTextToClipboard(code);
            }
        }
        
        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showCopyMessage();
                }
            } catch (err) {
                console.error('فشل في نسخ النص: ', err);
            }
            
            document.body.removeChild(textArea);
        }
        
        function showCopyMessage() {
            const message = document.getElementById('copyMessage');
            message.style.display = 'block';
            setTimeout(function() {
                message.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
