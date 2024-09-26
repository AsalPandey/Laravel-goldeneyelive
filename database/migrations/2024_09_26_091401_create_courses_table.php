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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('description');
            $table->string('course_outline');
            $table->string('rating_star');
            $table->string('rating_count');
            $table->string('capacity');
            $table->string('photo');
            $table->string('price');
            $table->string('duration');
            $table->string('instructor');
            $table->string('category');
            $table->string('catagory_slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
