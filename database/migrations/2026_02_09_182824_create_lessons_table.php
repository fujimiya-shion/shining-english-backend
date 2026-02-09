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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('course_id');
            $table->string('video_url');
            $table->integer('star_reward_video')->default(0);
            $table->integer('star_reward_quiz')->default(0);
            $table->boolean('has_quiz')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('course_id');
            $table->fullText(['name', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
