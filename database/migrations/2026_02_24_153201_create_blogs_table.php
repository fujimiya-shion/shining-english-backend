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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('slug');
            $table->boolean('status')->default(true);
            $table->integer('required_star')->default(0);
            $table->unsignedBigInteger('tag_id')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['title', 'slug']);

            $table->index('title');
            $table->index('slug');
            $table->index('deleted_at');
            $table->index('tag_id');
            $table->index(['title', 'status', 'deleted_at']);
            $table->index(['title', 'slug']);

            $table->fullText('title');
            $table->fullText(['title', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
