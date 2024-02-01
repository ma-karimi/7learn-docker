<?php

namespace Database\Seeders;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;

class ElasticsearchSeeder extends Seeder
{
    protected $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = ClientBuilder::create()
            ->setHosts(config('elasticsearch.hosts'))
            ->build();
    }

    public function run()
    {
        // Define the index where you want to store your posts
        ini_set('memory_limit', '-1');
        $count = 1;
        Post::with(['tags', 'tags.category'])->chunk(1000, function ($posts) use ($count) {
            echo "-----" . PHP_EOL;
            echo "run chunk number $count" . PHP_EOL;
            foreach ($posts as $post) {
                echo "run post id $post->id" . PHP_EOL;

                $data = [
                    'body' => [
                        'id' => $post->id,
                        'title' => $post->title,
                        'content' => $post->content,
                        'tags' => $post->tags->map(function ($tag) {
                            return [
                                'id' => $tag->id,
                                'name' => $tag->name,
                                'category' => $tag->category->only(['id', 'name']),
                            ];
                        }),
                    ],
                    'index' => 'posts',
                    'id' => $post->id,
                ];

                $this->elasticsearch->index($data);
            }
            $count+=1;
        });
        echo "Elasticsearch seeding completed!" . PHP_EOL;

    }

    public function run2()
    {
        $client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();

        // Seeding Categories and Tags
        Category::factory(20)->create()->each(function ($category) use ($client) {
            $tags = Tag::factory(rand(2, 8))->make(); // 'make' instead of 'create' because we are not saving it to the DB
            $categoryData = $category->toArray();
            $categoryData['tags'] = $tags->toArray();

            $client->index([
                'index' => 'categories',
                'id'    => $category->id,
                'body'  => $categoryData,
            ]);
        });

        // Seeding Posts with Tags
        Post::factory(10)->make()->each(function ($post) use ($client) {
            $tags = Tag::query()->inRandomOrder()->limit(rand(2, 10))->get()->toArray();
            $postData = $post->toArray();
            $postData['tags'] = $tags;

            $client->index([
                'index' => 'posts',
                'id'    => $post->id,
                'body'  => $postData,
            ]);
        });

        echo "Elasticsearch seeding completed!" . PHP_EOL;
    }
}
