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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable'); // Creates imageable_id and imageable_type
            $table->string('path');
            $table->string('disk')->default('public');
            $table->string('type')->nullable(); // e.g., 'avatar', 'profile', 'cover'
            $table->integer('order')->default(0);
            $table->string('alt')->nullable();
            $table->integer('size')->nullable(); // in bytes
            $table->string('mime_type')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
