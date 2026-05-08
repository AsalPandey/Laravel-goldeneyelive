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
            $table->longText('description');
            $table->longText('course_outline');
            $table->string('rating_star');
            $table->string('rating_count');
            $table->string('capacity');
            $table->string('photo');
            $table->string('price');
            $table->string('duration');
            $table->string('instructor');
            $table->enum('category', ['computer classes', 'language classes', 'other classes']);
            $table->enum('category_slug', ['computer-classes', 'language-classes', 'other-classes']);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->longText('schema_markup')->nullable();
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
