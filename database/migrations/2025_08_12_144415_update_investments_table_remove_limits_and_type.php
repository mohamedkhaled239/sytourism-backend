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
        Schema::table('investments', function (Blueprint $table) {
            $table->dropColumn(['min_investment', 'max_investment', 'investment_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->decimal('min_investment', 15, 2)->nullable();
            $table->decimal('max_investment', 15, 2)->nullable();
            $table->string('investment_type')->nullable();
        });
    }
};
