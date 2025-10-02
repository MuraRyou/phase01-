<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tag_tweet', function (Blueprint $table) {
            // 外部キー制約付きで 'tag_id' を追加
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            // 外部キー制約付きで 'tweet_id' を追加
            $table->foreignId('tweet_id')->constrained()->onDelete('cascade');

            // tag_id と tweet_id の組み合わせを主キーに設定し、重複を防ぐ
            $table->primary(['tag_id', 'tweet_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tag_tweet');
    }
};
