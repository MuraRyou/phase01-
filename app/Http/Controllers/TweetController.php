<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Tweet;
use Illuminate\Http\Request;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         $query = Tweet::with(['user', 'liked', 'tags'])->latest();

          $currentTag = null; 

        // ★★★ タグによるフィルタリングロジック ★★★
        if ($request->filled('tags')) {
            // URLからタグIDを取得
            $tagId = $request->tags;
            
            // whereHasで、そのタグIDを持つツイートのみに絞り込む
            $query->whereHas('tags', function ($q) use ($tagId) {
                // 中間テーブル（taggables）の tag_id が指定された $tagId と一致するツイートに絞る
                $q->where('tags.id', $tagId);
            });

              $currentTag = Tag::find($tagId);
              
        }
        // ★★★ ロジックここまで ★★★

        // クエリを実行してツイートを取得
        $tweets = $query->get();

        return view('tweets.index', compact('tweets', 'currentTag'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('tweets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
      'tweet' => 'required|max:255',
      'tags' => 'nullable|string|max:255',
    ]);


 
    // 1. ツイートの保存
        $tweet = $request->user()->tweets()->create($request->only('tweet'));

        // 2. タグ処理
        if ($request->filled('tags')) {
            // カンマ区切りでタグ名を配列に分割し、空白を削除
            $tagNames = collect(explode(',', $request->tags))
                        ->map(fn($tag) => trim($tag))
                        ->filter(); // 空の要素を削除

            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // タグ名で検索し、存在しなければ新規作成
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName]
                );
                $tagIds[] = $tag->id;
            }

            // 3. ツイートとタグを中間テーブルで紐づける (多対多のリレーション)
            $tweet->tags()->attach($tagIds);
        }

        return redirect()->route('tweets.index')->with('success', 'ツイートが投稿されました！');
    }

    
    

    /**
     * Display the specified resource.
     */
    public function show(Tweet $tweet)
    {
        //
        $tweet->load('comments');
        return view('tweets.show', compact('tweet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tweet $tweet)
    {
        //
        return view('tweets.edit', compact('tweet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tweet $tweet)
    {
        //
        $request->validate([
      'tweet' => 'required|max:255',
    ]);

    $tweet->update($request->only('tweet'));

    return redirect()->route('tweets.show', $tweet);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tweet $tweet)
    {
        //
         $tweet->delete();

    return redirect()->route('tweets.index');
    }
    
    /**
     * Search for tweets containing the keyword.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {

         $query = Tweet::query();

         // キーワードが指定されている場合のみ検索を実行
         if ($request->filled('keyword')) {
          $keyword = $request->keyword;
          $query->where('tweet', 'like', '%' . $keyword . '%');
    }

         // ページネーションを追加（1ページに10件表示）
          $tweets = $query
           ->latest()
           ->paginate(10);

    return view('tweets.search', compact('tweets'));
    }
}