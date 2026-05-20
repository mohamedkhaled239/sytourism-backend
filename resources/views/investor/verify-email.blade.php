<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة المستثمرين - تفعيل البريد الإلكتروني</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background: linear-gradient(180deg, #f4f7ef 0%, #ffffff 100%); }
        .verify-container { max-width: 520px; margin: 80px auto; background: white; padding: 32px; border-radius: 18px; box-shadow: 0 18px 40px rgba(62, 88, 40, 0.12); }
        .btn-primary { background: #3E5828; border: none; }
        .btn-primary:hover { background: #31471f; }
        .code-input { text-align: center; letter-spacing: 0.45em; font-size: 1.4rem; font-weight: 700; }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="وزارة السياحة" style="width: 82px; height: 82px; margin-bottom: 12px;">
            <h4 class="mb-1">تفعيل البريد الإلكتروني</h4>
            <p class="text-muted mb-0">أدخل كود التحقق المرسل إلى بريدك، وبعد التفعيل سيظل الحساب بانتظار موافقة الإدارة</p>
        </div>

        <div id="alert-box"></div>

        <form id="verify-email-form">
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ $email }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">كود التحقق</label>
                <input type="text" name="code" id="code" class="form-control code-input" maxlength="6" inputmode="numeric" autocomplete="one-time-code" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2" id="verify-btn">تفعيل الحساب</button>
            <button type="button" class="btn btn-outline-secondary w-100" id="resend-btn">إعادة إرسال الكود</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('investor.login') }}" class="text-decoration-none">العودة إلى تسجيل الدخول</a>
        </div>
    </div>

    <script>
        const alertBox = document.getElementById('alert-box');
        const emailInput = document.getElementById('email');
        const codeInput = document.getElementById('code');
        const savedEmail = localStorage.getItem('investor_verification_email');

        if (!emailInput.value && savedEmail) {
            emailInput.value = savedEmail;
        }

        codeInput.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });

        document.getElementById('verify-email-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            const verifyBtn = document.getElementById('verify-btn');
            verifyBtn.disabled = true;
            verifyBtn.textContent = 'جاري التفعيل...';
            alertBox.innerHTML = '';

            try {
                const response = await fetch('{{ url('/api/auth/verify-email') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: emailInput.value,
                        code: codeInput.value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    alertBox.innerHTML = `<div class="alert alert-danger">${data.message || 'تعذر تفعيل الحساب'}</div>`;
                    return;
                }

                localStorage.setItem('investor_verification_email', emailInput.value);
                alertBox.innerHTML = '<div class="alert alert-success">تم تفعيل البريد الإلكتروني بنجاح. حسابك الآن بانتظار موافقة الأدمن، وسيصلك إشعار عند اعتماده.</div>';
                document.getElementById('verify-email-form').reset();
                emailInput.value = localStorage.getItem('investor_verification_email') || '';
            } catch (error) {
                alertBox.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء الاتصال بالخدمة. حاول مرة أخرى.</div>';
            } finally {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'تفعيل الحساب';
            }
        });

        document.getElementById('resend-btn').addEventListener('click', async function() {
            const resendBtn = this;
            resendBtn.disabled = true;
            resendBtn.textContent = 'جاري الإرسال...';
            alertBox.innerHTML = '';

            try {
                const response = await fetch('{{ url('/api/auth/resend-email-verification') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: emailInput.value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    alertBox.innerHTML = `<div class="alert alert-danger">${data.message || 'تعذر إعادة إرسال الكود'}</div>`;
                    return;
                }

                alertBox.innerHTML = '<div class="alert alert-info">تم إرسال كود جديد إلى بريدك الإلكتروني.</div>';
            } catch (error) {
                alertBox.innerHTML = '<div class="alert alert-danger">تعذر الاتصال بالخدمة حاليًا. حاول مرة أخرى.</div>';
            } finally {
                resendBtn.disabled = false;
                resendBtn.textContent = 'إعادة إرسال الكود';
            }
        });
    </script>
</body>
</html>
