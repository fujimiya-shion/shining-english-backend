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
        Schema::table('lessons', function (Blueprint $table): void {
            $table->string('group_name')->nullable()->after('course_id');
            $table->text('description')->nullable()->after('video_url');
            $table->unsignedInteger('duration_minutes')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table): void {
            $table->dropColumn(['group_name', 'description', 'duration_minutes']);
        });
    }
};
