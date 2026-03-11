<?php

namespace App\Models;

use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes, Slugable;

    protected $attributes = [
        'star_reward_video' => 0,
        'star_reward_quiz' => 0,
        'has_quiz' => false,
    ];

    protected $fillable = [
        'name',
        'slug',
        'course_id',
        'group_name',
        'video_url',
        'description',
        'duration_minutes',
        'star_reward_video',
        'star_reward_quiz',
        'has_quiz',
    ];

    protected $casts = [
        'has_quiz' => 'boolean',
        'duration_minutes' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }
}
