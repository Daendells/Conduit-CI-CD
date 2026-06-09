<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'user_id',
        'body',
    ];

    /**
     * Comment's article.
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Comment's user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
