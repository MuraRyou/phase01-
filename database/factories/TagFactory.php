<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * 対応するモデル名
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * モデルのデフォルト状態を定義します。
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tagモデルのファクトリ定義
        return [
            // タグ名はユニークな単語を生成
            "name" => $this->faker->unique()->word(),
        ];
    }
}
