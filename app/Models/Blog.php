<?php

namespace App\Models;

use App\Traits\Slugable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken;

class Blog extends Model
{
    use HasFactory, Slugable, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'slug',
        'status',
        'required_star',
        'tag_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'required_star' => 'integer',
        ];
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(BlogTag::class, 'tag_id');
    }

    public function unlocks(): HasMany
    {
        return $this->hasMany(BlogUnlock::class);
    }

    public function getUserCanViewAttribute(): bool
    {
        if ((int) $this->required_star <= 0) {
            return true;
        }

        $token = request()->header('User-Authorization');
        if (! is_string($token) || $token === '') {
            return false;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        $user = $accessToken?->tokenable;

        if (! $user instanceof User) {
            return false;
        }

        return BlogUnlock::query()
            ->where('blog_id', $this->id)
            ->where('user_id', $user->id)
            ->exists();
    }
}
