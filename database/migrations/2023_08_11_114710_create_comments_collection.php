<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::create('comments', function (Blueprint $collection) {
            $collection->index('article_id');
            $collection->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::drop('comments');
    }
};
