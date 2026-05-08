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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('status')->default('active')->after('linkedin_url');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('status')->default('active')->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
