<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();

        if($posts->isEmpty()){
            return response()->json(['message'=>'No posts found.'],404);
        }

        return response()->json($posts);
    }

    public function addPost(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|min:3',
            'content' => 'required|string|min:10',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }

        Post::create($request->all());

        return response()->json(['message' => 'Post created successfully.', 201]);
    }

    /*
     * Get a post by ID
     */
    public function getPost($id){
        $post = Post::find($id);
        if(is_null($post)){
            return response()->json(['message'=>'Post not found.'],404);
        }
        return response()->json($post);
    }

    /*
     * Update post by ID
     */
    public function updatePost(Request $request, $id){
        $post = Post::find($id);

        if(is_null($post)){
            return response()->json(['message'=>'Post not found.'],404);
        }
        $validator = Validator::make($request->all(),[
            'title' => 'sometimes|string|max:255|min:3',
            'content' => 'sometimes|string|min:10',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }
        if ($request->has('title')) {
            $post->title = $request->get('title');
        }
        if ($request->has('content')) {
            $post->content = $request->get('content');
        }
        $post->update();

        return response()->json(['message' => 'Post updated successfully.']);
    }

    public function deletePost($id){

        $post = Post::find($id);

        if(is_null($post)){
            return response()->json(['message'=>'Post not found.'],404);
        }
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }
    //
}
