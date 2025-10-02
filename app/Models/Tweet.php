<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;

class Tweet extends Model
{
    /** @use HasFactory<\Database\Factories\TweetFactory> */
    use HasFactory;

    protected $fillable = ['tweet'];

    public function comments()
  {
    return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

   public function liked()
  {
      return $this->belongsToMany(User::class)->withTimestamps();
  }

  
    public function tags()
    {
        // tag_tweet テーブルを使った多対多（ツイートとタグの関係）
        return $this->belongsToMany(Tag::class, 'tag_tweet', 'tweet_id', 'tag_id');
    }
}
