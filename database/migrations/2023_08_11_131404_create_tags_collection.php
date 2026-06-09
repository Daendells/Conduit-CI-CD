<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use MongoDB\Laravel\Schema\Blueprint;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::create('tags', function (Blueprint $collection) {
            $collection->unique('name');
        });
    }

    public function down(): void
    {
        Schema::drop('tags');
    }
};
