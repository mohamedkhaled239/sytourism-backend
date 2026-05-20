// database/migrations/2024_01_01_000011_create_investment_locations_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('investment_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['investment_id', 'location_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('investment_locations');
    }
};
