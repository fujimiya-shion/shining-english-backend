<?php

namespace App\Models;

use App\Models\Scopes\CourseActiveScope;
use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, Slugable {
        Slugable::booted as bootSlugable;
    }
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'status',
        'thumbnail',
        'category_id',
        'level_id',
        'description',
        'rating',
        'learned',
    ];

    protected static function booted(): void
    {
        static::bootSlugable();
        static::addGlobalScope(new CourseActiveScope);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}
