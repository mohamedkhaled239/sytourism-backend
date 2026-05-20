<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة المستثمرين - إنشاء حساب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background: linear-gradient(180deg, #f4f7ef 0%, #ffffff 100%); }
        .register-container { max-width: 760px; margin: 50px auto; background: white; padding: 32px; border-radius: 18px; box-shadow: 0 18px 40px rgba(62, 88, 40, 0.12); }
        .btn-primary { background: #3E5828; border: none; }
        .btn-primary:hover { background: #31471f; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="text-center mb-4">
            <img src="{{ asset('images/logo.png') }}" alt="وزارة السياحة" style="width: 82px; height: 82px; margin-bottom: 12px;">
            <h4 class="mb-1">تسجيل مستثمر جديد</h4>
            <p class="text-muted mb-0">سيتم إنشاء الحساب عبر نفس API الحالي، ثم تنتقل مباشرةً لصفحة تفعيل البريد الإلكتروني</p>
        </div>

        <div id="alert-box"></div>

        <form id="investor-register-form">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الاسم الكامل</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">الدولة</label>
                <input type="text" name="country" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="submit-btn">إنشاء الحساب</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('investor.login') }}" class="text-decoration-none">العودة إلى تسجيل الدخول</a>
        </div>
    </div>

    <script>
        document.getElementById('investor-register-form').addEventListener('submit', async function(event) {
            event.preventDefault();

            const form = event.target;
            const alertBox = document.getElementById('alert-box');
            const submitBtn = document.getElementById('submit-btn');
            const formData = new FormData(form);
            const email = formData.get('email');
            formData.append('user_type', 'investor');

            submitBtn.disabled = true;
            submitBtn.textContent = 'جاري إنشاء الحساب...';
            alertBox.innerHTML = '';

            try {
                const response = await fetch('{{ url('/api/auth/register') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    const messages = data.errors ? Object.values(data.errors).flat() : [data.message || 'حدث خطأ أثناء التسجيل'];
                    alertBox.innerHTML = `<div class="alert alert-danger"><ul class="mb-0">${messages.map(msg => `<li>${msg}</li>`).join('')}</ul></div>`;
                    return;
                }

                localStorage.setItem('investor_verification_email', data?.data?.email || email);
                window.location.href = `{{ route('investor.verify-email') }}?email=${encodeURIComponent(data?.data?.email || email)}`;
            } catch (error) {
                alertBox.innerHTML = '<div class="alert alert-danger">تعذر الاتصال بالخدمة حاليًا. حاول مرة أخرى.</div>';
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'إنشاء الحساب';
            }
        });
    </script>
</body>
</html>
