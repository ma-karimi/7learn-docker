<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Elastic\Elasticsearch\ClientBuilder;

class PostController extends Controller
{
    private $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $itemsPerPage = $request->input('per_page', 50);

        $params = [
            'index' => 'posts',
            'body'  => [
                'from' => 0,
                'size' => $itemsPerPage,
                '_source' => ['id', 'title', 'content'],
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];

        $response = $this->client->search($params);

        return response()->json([
            'data' => array_column($response['hits']['hits'], '_source'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($post)
    {
        $params = [
            'index' => 'posts',
            'id'    => $post,
        ];
        $response = $this->client->get($params);
        return response()->json($response['_source']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
