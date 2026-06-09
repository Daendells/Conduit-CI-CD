<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'name',
    ];

    /**
     * Tagged articles.
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, null, 'tag_ids', 'article_ids');
    }

    /**
     * Get most popular tags by article count.
     * Uses MongoDB aggregation instead of SQL JOIN.
     */
    public static function favoriteTags($count = 5)
    {
        return self::raw(function ($collection) use ($count) {
            return $collection->aggregate([
                [
                    '$project' => [
                        'name' => 1,
                        'article_count' => [
                            '$size' => ['$ifNull' => ['$article_ids', []]],
                        ],
                    ],
                ],
                ['$sort' => ['article_count' => -1]],
                ['$limit' => $count],
            ]);
        });
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
}
