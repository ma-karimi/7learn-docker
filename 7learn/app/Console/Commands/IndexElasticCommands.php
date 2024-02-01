<?php

namespace App\Console\Commands;

use App\Jobs\SendPostIndexingSMS;
use App\Models\Post;
use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class IndexElasticCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $elasticsearch;

    public function __construct()
    {
        parent::__construct();
        $this->elasticsearch = ClientBuilder::create()
            ->setHosts(config('elasticsearch.hosts'))
            ->build();
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking if Elasticsearch index is empty...');

        if (! $this->isIndexEmpty('posts')) {
            $this->info('Elasticsearch index already has data. Exiting.');
            return;
        }

        $this->info('Indexing all posts. This might take a while...');
        ini_set('memory_limit', '-1');

        foreach (Post::query()->cursor() as $post) {
            $this->elasticsearch->index([
                'body'  => [
                    'id'      => $post->id,
                    'title'   => $post->title,
                    'content' => $post->content,
                    'tags'    => $post->tags->map(function ($tag) {
                        return [
                            'id'       => $tag->id,
                            'name'     => $tag->name,
                            'category' => $tag->category->only(['id', 'name']),
                        ];
                    }),
                ],
                'index' => 'posts',
                'id'    => $post->id,
            ]);

            $this->output->write('.');
        }

        $number  = "09121234567";
        $message = "All posts indexed successfully";
        SendPostIndexingSMS::dispatch($number, $message);

        $this->info("\nDone!");

    }

    protected function isIndexEmpty($index)
    {
        $indexExists = $this->elasticsearch->indices()->exists(['index' => $index]);

        if (! $indexExists) return TRUE;

        $response = $this->elasticsearch->count(['index' => $index]);
        return $response['count'] == 0;

    }
}
