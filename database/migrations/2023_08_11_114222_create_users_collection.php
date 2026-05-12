<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mongodb';

    public function up(): void
    {
        Schema::create('users', function (Blueprint $collection) {
            $collection->unique('email');
            $collection->unique('username');
        });
    }

    public function down(): void
    {
        Schema::drop('users');
    }
};
