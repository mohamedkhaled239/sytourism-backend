<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('account_type')->default('admin')->after('is_super_admin');
            $table->foreignId('governorate_id')->nullable()->after('account_type')->constrained()->nullOnDelete();
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropColumn(['account_type', 'governorate_id', 'last_login_at']);
        });
    }
};
