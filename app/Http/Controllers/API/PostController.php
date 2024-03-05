<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function list(Request $request)
    {
        $perPage = $request->input('per_page', 5);

        $posts = Post::latest()->paginate($perPage);

        $post_data = $posts->map(function ($data) {
            return [
                'id' => $data->id,
                'title' => $data->title,
                'description' => $data->description,
                'images' => json_decode($data->multiple_images),
                'total_likes' => $data->likes->count(),
                'total_comments' => $data->comments->count(),
                'liked_by_me' => $data->likes->contains('user_id', Auth::user()->id),
                'likes' => $data->likes->map->only(['id', 'user_id']),
                'comments' => $data->comments->map->only(['id', 'user_id', 'content']),
                'posted_by' => $data->user->email,
                'created_at' => $data->created_at,
            ];
        });

        return response()->json([
            'status' => 200,
            'data' => $post_data,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
            'message' => 'Product List',
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'multiple_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        $images = [];

        $upload_images = $this->UploadMultipleImage($request, 'multiple_images', '/assets/images/post/', '450', '450');

        if (isset($images)) {
            foreach ($upload_images as $image) {
                $images[] = $image;
            }
        }

        $post = new Post([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'multiple_images' => json_encode($images),
            'posted_by' => Auth::id(),
        ]);

        $post->save();

        return response()->json([
            'status' => 200,
            'message' => 'Post created successfully'
        ]);
    }


    public function likePost($id)
    {
        $like = new Like([
            'post_id' => $id,
            'user_id' => Auth::id(),
        ]);

        $post = Post::findorFail($id);

        $post->likes()->save($like);

        return response()->json([
            'status' => 200,
            'message' => 'Post liked successfully'
        ]);
    }

    public function commentPost(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        $post = Post::findorFail($id);

        $post->comments()->save($comment);

        return response()->json([
            'status' => 200,
            'message' => 'Comment added successfully'
        ]);
    }
}
