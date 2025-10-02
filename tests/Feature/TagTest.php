<?php

use App\Models\Tag;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// このテストファイルでデータベースをリセットして利用することを宣言
uses(RefreshDatabase::class);

/**
 * タグ機能（ツイート作成時の保存、一覧での絞り込み）に関するフィーチャテスト
 */


// --- Test Case 1: 新規タグ付きツイートの作成 ---
test('authenticated user can create a tweet with new tags, and the tags are correctly stored and attached', function () {
    // 準備：ユーザーを作成
    $user = User::factory()->create();

    // 実行：タグ付きでツイートを投稿
    $response = $this->actingAs($user)->post(route('tweets.store'), [
        'tweet' => 'このツイートには #Laravel #PHP というタグが付いています',
        'tags' => 'Laravel, PHP, 開発', // カンマ区切りのタグ名
    ]);

    // 期待される結果：
    // 1. ツイート一覧にリダイレクトされる
    $response->assertRedirect(route('tweets.index'));
    $response->assertSessionHas('success');

    // 2. ツイートが保存されたことを確認
    $this->assertDatabaseHas('tweets', [
        'user_id' => $user->id,
        'tweet' => 'このツイートには #Laravel #PHP というタグが付いています',
    ]);

    // 3. タグがデータベースに作成されたことを確認（3つ）
    $this->assertDatabaseHas('tags', ['name' => 'Laravel']);
    $this->assertDatabaseHas('tags', ['name' => 'PHP']);
    $this->assertDatabaseHas('tags', ['name' => '開発']);
    $this->assertCount(3, Tag::all());

    // 4. ツイートとタグが正しく紐づけられたことを確認
    $tweet = Tweet::where('user_id', $user->id)->first();
    $this->assertCount(3, $tweet->tags, '中間テーブルに3つのタグが紐づいていること');
});


// --- Test Case 2: 既存のタグによるツイートの絞り込み ---
test('it filters tweets by a specific tag when tag id is provided in the request', function () {
    // 準備：ユーザーとタグ、ツイートを作成
    $user = User::factory()->create();
    $tagLaravel = Tag::factory()->create(['name' => 'Laravel']);
    $tagPHP = Tag::factory()->create(['name' => 'PHP']);

    // ツイートA (Laravel, PHP): 表示されるべき
    $tweetA = Tweet::factory()->for($user)->create(['tweet' => 'Tweet A: LaravelとPHP']);
    $tweetA->tags()->attach([$tagLaravel->id, $tagPHP->id]);

    // ツイートB (Laravelのみ): 表示されるべき
    $tweetB = Tweet::factory()->for($user)->create(['tweet' => 'Tweet B: Laravelのみ']);
    $tweetB->tags()->attach([$tagLaravel->id]);

    // ツイートC (PHPのみ): 表示されないべき
    $tweetC = Tweet::factory()->for($user)->create(['tweet' => 'Tweet C: PHPのみ']);
    $tweetC->tags()->attach([$tagPHP->id]);


    // 実行：'Laravel' タグIDで絞り込みをリクエスト
    $response = $this->actingAs($user)->get(route('tweets.index', ['tags' => $tagLaravel->id]));

    // 期待される結果：
    $response->assertOk();
    
    // 1. 絞り込み中メッセージが表示されること
    $response->assertSee('「#'.$tagLaravel->name.'」で絞り込み中');

    // 2. Laravelタグを持つツイートAとBが表示されること
    $response->assertSee('Tweet A: LaravelとPHP');
    $response->assertSee('Tweet B: Laravelのみ');

    // 3. PHPタグのみのツイートCは表示されないこと
    $response->assertDontSee('Tweet C: PHPのみ');
});

// --- Test Case 3: タグIDがない場合の全件表示 ---
test('it displays all tweets when no tag id is provided', function () {
    // 準備
    $user = User::factory()->create();
    $tagLaravel = Tag::factory()->create(['name' => 'Laravel']);
    $tagPHP = Tag::factory()->create(['name' => 'PHP']);

    $tweet1 = Tweet::factory()->for($user)->create(['tweet' => 'First tweet']);
    $tweet1->tags()->attach($tagLaravel->id);
    
    $tweet2 = Tweet::factory()->for($user)->create(['tweet' => 'Second tweet']);
    $tweet2->tags()->attach($tagPHP->id);

    // 実行：クエリパラメータなしで一覧にアクセス
    $response = $this->actingAs($user)->get(route('tweets.index'));

    // 期待される結果：
    $response->assertOk();
    // 全てのツイートが表示されること
    $response->assertSee('First tweet');
    $response->assertSee('Second tweet');
    // 絞り込み中メッセージがないこと
    $response->assertDontSee('で絞り込み中'); 
});
