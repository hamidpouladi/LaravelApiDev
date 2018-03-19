<?php

namespace App\Http\Controllers;

use App\Transformers\TopicTransformer;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Topic;
use App\Post;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TopicController extends Controller
{
    protected $topic;
    protected $post;

    public function store(StoreTopicRequest $request, Topic $topic, Post $post)
    {

        $this->topic = $topic;
        $this->topic->title = $request->title;
        $this->topic->user()->associate($request->user());

        $this->post = $post;
        $this->post->body = $request->body;
        $this->post->user()->associate($request->user());

        $this->topic->save();
        $this->topic->posts()->save($this->post);

        return fractal()
            ->item($this->topic)
            ->parseIncludes(['user'])
            ->transformWith(new TopicTransformer)
            ->toArray();
    }

    public function index()
    {
        $topics = Topic::latestFirst()->paginate(3);
        $topicsColletion = $topics->getCollection();
        
        return fractal()
            ->collection($topicsColletion)
            ->parseIncludes(['user'])
            ->transformWith(new TopicTransformer)
            ->paginateWith(new IlluminatePaginatorAdapter($topics))
            ->toArray();
    }

    public function show(Topic $topic) {
        
        return fractal()
            ->item($topic)
            ->parseIncludes(['user', 'posts', 'posts.user', 'posts.likes'])
            ->transformWith(new TopicTransformer)
            ->toArray();
    }

    public function update(UpdateTopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->title = $request->get('title', $topic->title);
        $topic->save();

        return fractal()
            ->item($topic)
            ->parseIncludes(['user'])
            ->transformWith(new TopicTransformer)
            ->toArray();
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();

        return response(null, 204);
    }
}
