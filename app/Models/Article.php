<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'body',
    ];

    /**
     * Check if user favored the article.
     */
    public function favoritedByUser(User $user): bool
    {
        return $this->favoritedUsers()
            ->whereKey($user->getKey())
            ->exists();
    }

    /**
     * Scope articles to favored by a user.
     */
    public function scopeFavoritedByUser($query, string $username): mixed
    {
        return $query->whereHas('favoritedUsers', function ($builder) use ($username) {
            $builder->where('username', $username);
        });
    }

    /**
     * Scope articles to authors followed by a user, including the user's own articles.
     * Uses direct where() calls (same as hasMany) instead of whereIn to avoid
     * potential ObjectId auto-conversion in mongodb-laravel.
     */
    public function scopeOfAuthorsFollowedByUser($query, User $user): mixed
    {
        $userId = (string) $user->id;

        $followingIds = $user->followings->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->filter()
            ->values()
            ->toArray();

        // Start with own articles
        $query->where('user_id', $userId);

        // Add followed authors' articles
        foreach ($followingIds as $id) {
            $query->orWhere('user_id', $id);
        }

        return $query;
    }

    /**
     * Attach tags to article.
     */
    public function attachTags(array $tags): void
    {
        $tagIds = [];
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $tagIds[] = $tag->id;
        }

        $this->tags()->sync($tagIds);
    }

    /**
     * Article user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Article tags.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, null, 'article_ids', 'tag_ids');
    }

    /**
     * Get comments for article.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get users that favorited the article.
     */
    public function favoritedUsers()
    {
        return $this->belongsToMany(User::class, null, 'favorite_article_ids', 'favorite_user_ids');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function toggleUserFavorite(User $user): bool
    {
        $isFavorited = false;

        if ($this->favoritedByUser($user)) {
            $user->favorites()->detach($this);
        } else {
            $user->favorites()->syncWithoutDetaching($this);
            $isFavorited = true;
        }

        return $isFavorited;
    }
}
