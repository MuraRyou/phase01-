<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    //
    use HasFactory;

    // データベースに name カラムのみを保存可能にする
    protected $fillable = ['name'];

    /**
     * このタグに紐づくツイートを取得
     */
    public function tweets()
    {
        // Tagモデルは Tweetモデルと多対多の関係にある（中間テーブル: tag_tweet）
        return $this->belongsToMany(Tweet::class);
    }
}
