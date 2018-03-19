<?php

namespace App\Transformers;

use App\Post;
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user', 'user'];

    public function transform(Post $post)
    {
        return [
            'id' => $post->id,
            'body' => $post->body,
            'like_count' => $post->likes->count(),
            'created_at' => $post->created_at->toDateTimeString(),
            'created_at_human' => $post->created_at->diffForHumans(),
        ];
    }

    public function includeUser(Post $post)
    {
        return $this->item($post->user, new UserTransformer);
    }

    public function includeLikes(Post $post)
    {
        return $this->collection($post->like->pluck('user'), new UserTransformer);
    }
}