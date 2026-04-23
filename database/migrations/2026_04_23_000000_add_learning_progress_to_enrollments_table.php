<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table): void {
            $table->unsignedBigInteger('current_lesson_id')->nullable()->after('enrolled_at');
            $table->json('completed_lesson_ids')->nullable()->after('current_lesson_id');
            $table->index('current_lesson_id');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table): void {
            $table->dropIndex(['current_lesson_id']);
            $table->dropColumn(['current_lesson_id', 'completed_lesson_ids']);
        });
    }
};
