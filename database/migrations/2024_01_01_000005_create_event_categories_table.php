// database/migrations/2024_01_01_000005_create_event_categories_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar');
            $table->string('color')->default('#000000');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_categories');
    }
};
