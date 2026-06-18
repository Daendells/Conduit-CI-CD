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
     * Uses direct whereIn on user_id field for MongoDB compatibility.
     */
    public function scopeOfAuthorsFollowedByUser($query, User $user): mixed
    {
        $followingIds = $user->followings->pluck('id')->toArray();
        $followingIds[] = $user->id;

        return $query->whereIn('user_id', $followingIds);
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
