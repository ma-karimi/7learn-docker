<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ini_set('memory_limit', '-1');
        Category::factory(20)->create()->each(function ($category){
            $tags = Tag::factory(rand(2, 8))->create();
            $category->tags()->saveMany($tags);
        });

        Post::factory(1000000)->create()->each(function ($post){
            $tags = Tag::query()->inRandomOrder()->limit(rand(2, 10))->get();
            $post->tags()->attach($tags);
        });

        // index all posts on elastic
        Artisan::call('posts:index');
    }
}
