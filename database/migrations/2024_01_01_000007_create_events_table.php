// database/migrations/2024_01_01_000007_create_events_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ar');
            $table->text('description');
            $table->text('description_ar');
            $table->string('main_image');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->foreignId('category_id')->constrained('event_categories');
            $table->foreignId('location_id')->constrained('locations');
            $table->enum('status', ['not_started', 'active', 'ended'])->default('not_started');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
