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
            // تغيير نوع البيانات لرمز إعادة التعيين ليكون 6 أرقام
            $table->string('reset_password_token', 6)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // إرجاع نوع البيانات للحالة السابقة
            $table->string('reset_password_token')->nullable()->change();
        });
    }
};
