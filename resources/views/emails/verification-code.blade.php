<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            position: relative;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
            letter-spacing: 5px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
        }
        .copy-button {
            background-color: #007bff;
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
            background-color: #0056b3;
        }
        .copy-button:active {
            background-color: #004085;
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
            <h1>🔐 رمز التحقق</h1>
            <p>مرحباً بك في منصة سياحة</p>
        </div>
        
        <div class="content">
            <h2>رمز التحقق الخاص بك</h2>
            <p>استخدم الرمز التالي لتأكيد بريدك الإلكتروني:</p>
            
            <div class="code-container">
                <div class="code" id="verificationCode">{{ $code }}</div>
                <button class="copy-button" onclick="copyCode()">📋 نسخ الرمز</button>
                <div id="copyMessage" style="color: green; margin-top: 10px; display: none;">✅ تم نسخ الرمز بنجاح!</div>
            </div>
            
            <div class="warning">
                ⚠️ هذا الرمز صالح لمرة واحدة فقط. لا تشاركه مع أي شخص آخر.
            </div>
            
            <p>إذا لم تطلب هذا الرمز، يرجى تجاهل هذا الإيميل.</p>
        </div>
        
        <div class="footer">
            <p>© 2024 منصة سياحة. جميع الحقوق محفوظة.</p>
            <p>هذا إيميل تلقائي، يرجى عدم الرد عليه.</p>
        </div>
    </div>

    <script>
        function copyCode() {
            const code = document.getElementById('verificationCode').textContent;
            
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
