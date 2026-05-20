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
        Schema::table('locations', function (Blueprint $table) {
            $table->foreignId('governorate_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tourism_type_id')->nullable()->constrained()->onDelete('set null');
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('features')->nullable();
            $table->text('features_ar')->nullable();
            $table->text('rating_description')->nullable();
            $table->text('rating_description_ar')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->json('opening_hours')->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign(['governorate_id']);
            $table->dropForeign(['tourism_type_id']);
            $table->dropColumn([
                'governorate_id',
                'tourism_type_id',
                'phone',
                'description',
                'description_ar',
                'features',
                'features_ar',
                'rating_description',
                'rating_description_ar',
                'website',
                'email',
                'opening_hours',
                'rating',
                'is_active'
            ]);
        });
    }
};
