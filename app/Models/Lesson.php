<?php

namespace App\Models;

use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, Slugable, SoftDeletes;

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
        'documents',
        'document_names',
        'description',
        'duration_minutes',
        'star_reward_video',
        'star_reward_quiz',
        'has_quiz',
    ];

    protected $casts = [
        'has_quiz' => 'boolean',
        'duration_minutes' => 'integer',
        'documents' => 'array',
        'document_names' => 'array',
    ];

    public function setDocumentNamesAttribute(?array $value): void
    {
        if ($value === null) {
            $this->attributes['document_names'] = null;

            return;
        }

        $normalized = [];

        foreach ($value as $path => $name) {
            if (! is_string($path)) {
                continue;
            }

            $fallbackName = basename($path);
            $displayName = is_string($name) ? trim($name) : '';

            if ($displayName === '') {
                $normalized[$path] = $fallbackName;

                continue;
            }

            $extension = pathinfo($fallbackName, PATHINFO_EXTENSION);

            if (
                $extension !== ''
                && strtolower(pathinfo($displayName, PATHINFO_EXTENSION)) !== strtolower($extension)
            ) {
                $displayName .= ".{$extension}";
            }

            $normalized[$path] = $displayName;
        }

        $this->attributes['document_names'] = json_encode($normalized);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LessonComment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
    }
}
