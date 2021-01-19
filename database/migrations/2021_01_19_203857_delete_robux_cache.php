<?php

use Illuminate\Database\Migrations\Migration;

class DeleteRobuxCache extends Migration
{
    public function up(): void
    {
        Cache::forget('robux');
    }
}
