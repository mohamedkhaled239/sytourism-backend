<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إضافة حقل انتهاء صلاحية رمز التحقق إذا لم يكن موجوداً
            if (!Schema::hasColumn('users', 'email_verification_code_expires')) {
                $table->timestamp('email_verification_code_expires')->nullable()->after('email_verification_code');
            }
            
            // إضافة حقل آخر تسجيل دخول إذا لم يكن موجوداً
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_verification_code_expires', 'last_login_at']);
        });
    }
};
