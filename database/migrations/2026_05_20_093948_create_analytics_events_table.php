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
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 80)->index();
            $table->string('source_page', 500)->nullable();
            $table->string('source_section')->nullable()->index();
            $table->string('cta_label')->nullable();
            $table->string('selected_course')->nullable()->index();
            $table->string('audience_type')->nullable()->index();
            $table->string('inquiry_intent')->nullable()->index();
            $table->string('device_type', 32)->nullable()->index();
            $table->timestamp('occurred_at')->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
