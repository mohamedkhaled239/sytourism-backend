<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'user_type' => 'required|in:tourist,investor',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'country' => $request->country,
            'user_type' => $request->user_type,
            'is_approved' => $request->user_type === 'investor' ? false : true,
            'approved_at' => $request->user_type === 'investor' ? null : now(),
            'password' => Hash::make($request->password),
            'email_verification_code' => $verificationCode,
        ]);

        try {
            Mail::send('emails.verification-code', ['code' => $verificationCode], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('رمز التحقق من البريد الإلكتروني - منصة سياحة');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $user->isInvestor()
                ? 'تم إنشاء حساب المستثمر بنجاح. يرجى تأكيد البريد الإلكتروني وانتظار موافقة الإدارة.'
                : 'تم إنشاء الحساب بنجاح. يرجى التحقق من بريدك الإلكتروني',
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email,
                'requires_verification' => true,
                'requires_admin_approval' => $user->isInvestor(),
            ]
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني غير موجود',
                'error_code' => 'EMAIL_NOT_FOUND',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني مؤكد بالفعل',
                'error_code' => 'EMAIL_ALREADY_VERIFIED',
            ], 400);
        }

        if ($user->email_verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح',
                'error_code' => 'INVALID_VERIFICATION_CODE',
            ], 400);
        }

        if ($user->email_verification_code_expires && $user->email_verification_code_expires < now()) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق منتهي الصلاحية. يرجى طلب رمز جديد',
                'error_code' => 'VERIFICATION_CODE_EXPIRED',
                'can_resend' => true,
            ], 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires' => null,
        ]);

        cache()->forget('resend_verification_' . $user->id);

        if ($user->isInvestor() && !$user->is_approved) {
            return response()->json([
                'success' => true,
                'message' => 'تم تأكيد البريد الإلكتروني بنجاح. سيتم تفعيل حساب المستثمر بعد موافقة الإدارة.',
                'data' => [
                    'user' => $user->makeHidden(['password', 'remember_token']),
                    'requires_admin_approval' => true,
                ]
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تأكيد البريد الإلكتروني بنجاح',
            'data' => [
                'user' => $user->makeHidden(['password', 'remember_token']),
                'token' => $token,
            ]
        ]);
    }

    public function resendEmailVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'البريد الإلكتروني مؤكد بالفعل',
                'error_code' => 'EMAIL_ALREADY_VERIFIED',
            ], 400);
        }

        $cacheKey = 'resend_verification_' . $user->id;
        $resendCount = cache()->get($cacheKey, 0);

        if ($resendCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'تم تجاوز الحد الأقصى لإعادة الإرسال. يرجى المحاولة بعد ساعة',
                'error_code' => 'RESEND_LIMIT_EXCEEDED',
                'retry_after' => 3600,
            ], 429);
        }

        $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'email_verification_code' => $verificationCode,
            'email_verification_code_expires' => now()->addMinutes(15),
        ]);

        cache()->put($cacheKey, $resendCount + 1, 3600);

        try {
            Mail::send('emails.verification-code', ['code' => $verificationCode, 'user' => $user], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('إعادة إرسال رمز التحقق - منصة سياحة');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to resend verification email: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إرسال البريد الإلكتروني. يرجى المحاولة لاحقًا',
                'error_code' => 'EMAIL_SEND_FAILED',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة إرسال رمز التحقق إلى بريدك الإلكتروني',
            'data' => [
                'email' => $user->email,
                'expires_in' => 15,
                'remaining_attempts' => 3 - ($resendCount + 1),
            ]
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة',
                'error_code' => 'INVALID_CREDENTIALS',
            ], 401);
        }

        if (!$user->email_verified_at) {
            $shouldSendNewCode = true;

            if ($user->email_verification_code && $user->email_verification_code_expires && $user->email_verification_code_expires > now()) {
                $shouldSendNewCode = false;
            }

            if ($shouldSendNewCode) {
                $verificationCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                $user->update([
                    'email_verification_code' => $verificationCode,
                    'email_verification_code_expires' => now()->addMinutes(15),
                ]);

                try {
                    Mail::send('emails.verification-code', ['code' => $verificationCode, 'user' => $user], function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('تأكيد البريد الإلكتروني - منصة سياحة');
                    });

                    $message = 'يرجى تأكيد بريدك الإلكتروني. تم إرسال رمز تحقق جديد';
                } catch (\Exception $e) {
                    \Log::error('Failed to send verification email during login: ' . $e->getMessage());
                    $message = 'يرجى تأكيد بريدك الإلكتروني. فشل إرسال رمز جديد';
                }
            } else {
                $message = 'يرجى تأكيد بريدك الإلكتروني. تم إرسال رمز التحقق مسبقًا';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'error_code' => 'EMAIL_NOT_VERIFIED',
                'requires_verification' => true,
                'data' => [
                    'email' => $user->email,
                    'code_sent' => $shouldSendNewCode,
                    'expires_in' => 15,
                ]
            ], 403);
        }

        if ($user->isInvestor() && !$user->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'تم تأكيد البريد الإلكتروني لكن الحساب ما زال بانتظار موافقة الإدارة',
                'error_code' => 'INVESTOR_PENDING_APPROVAL',
                'requires_admin_approval' => true,
            ], 403);
        }

        $user->update([
            'last_login_at' => now(),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => $user->makeHidden(['password', 'remember_token']),
                'token' => $token,
            ]
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();
        $token = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'reset_password_token' => $token,
            'reset_password_expires' => now()->addHours(2),
        ]);

        try {
            Mail::send('emails.password-reset', ['token' => $token], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('إعادة تعيين كلمة المرور - منصة سياحة');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز إعادة التعيين إلى بريدك الإلكتروني',
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:6',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::where('reset_password_token', $request->token)
            ->where('reset_password_expires', '>', now())
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'رمز إعادة التعيين غير صحيح أو منتهي الصلاحية',
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_password_token' => null,
            'reset_password_expires' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح',
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->makeHidden(['password', 'remember_token']),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|unique:users,username,' . $request->user()->id . '|max:255',
            'phone' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:100',
        ]);

        $request->user()->update($request->only(['full_name', 'username', 'phone', 'country']));

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث البيانات بنجاح',
            'data' => $request->user()->makeHidden(['password', 'remember_token']),
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة',
            ], 400);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح',
        ]);
    }

    public function toggleNotifications(Request $request)
    {
        $user = $request->user();
        $user->update([
            'notifications_enabled' => !$user->notifications_enabled,
        ]);

        return response()->json([
            'success' => true,
            'message' => $user->notifications_enabled ? 'تم تفعيل الإشعارات' : 'تم إيقاف الإشعارات',
            'data' => ['notifications_enabled' => $user->notifications_enabled],
        ]);
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور غير صحيحة',
            ], 400);
        }

        $request->user()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الحساب بنجاح',
        ]);
    }
}
