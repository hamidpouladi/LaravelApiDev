<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Topic;
use App\Like;

class PostLikeController extends Controller
{
    protected $like;

    public function store(Request $request, Topic $topic, Post $post, Like $like)
    {
        $this->authorize('like', $post);

        if($request->user()->hasLikedPost($post)) {
            return response(null, 409);
        }

        $this->like = $like;
        $this->like->user()->associate($request->user());

        $post->likes()->save($this->like);

        return response(null, 204);
    }
}
