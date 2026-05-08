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
        Schema::create('service_pillars', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('icon', 80)->nullable();
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->json('bullets')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('cta_url')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->string('status')->default('active')->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('aeo_summary')->nullable();
            $table->longText('schema_markup')->nullable();
            $table->timestamps();

            $table->index(['status', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_pillars');
    }
};
