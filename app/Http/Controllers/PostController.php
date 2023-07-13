<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $session = $request->cookie("laravel_session");
        // $user = $request->user("web");
        // $loggedin = false;
        // $guards = empty($guards) ? [null] : $guards;

        // foreach ($guards as $guard) {
        //     if (Auth::guard($guard)->check()) {
        //         $loggedin = true;
        //     }else{
        //         $loggedin = false; 
        //     }
        // }

        $posts = Post::all();
        // foreach ($posts as $post) {
        //     $post->comments = json_decode($post->comments, true);
        // }
        // $posts['session'] = $session;
        // $posts['loggedin'] = $loggedin;
        // $posts['user'] = $user;
        return $posts;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Response $response)
    {
        try {
            $user = $request->user("web");
            if (!$user && $user->role !== "admin") {
                return json_encode([
                    "status" => false,
                    "message" => "You are not authorized!!"
                ]);
            }
            $body = $request->all();
            $title = $body["title"];
            $description = $body["description"];
            isset($body["comments"]) ? $jsonData = json_encode($body["comments"]) : null;
            $post = new Post();
            $post->title = $title;
            $post->description = $description;
            isset($body["comments"]) ? $post->comments = $jsonData : null;
            $post->save();
            return json_encode([
                "status" => true,
                "message" => "Post Created Successfully!!"
            ]);
        } catch (Exception $e) {
            //throw $th;
            return json_encode(["Error" => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        return $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user("web");
        if (!$user && $user->role !== "admin") {
            return json_encode([
                "status" => false,
                "message" => "You are not authorized!!"
            ]);
        }
        $body = $request->all();
        $post = Post::find($id);
        if ($post) {
            $postUpdated = $post->update($body);
            return $postUpdated ? json_encode(["status" => "Sucess!!", "message" => "Post Updated Sucessfully!!"]) : json_encode(["status" => false, "message" => "Error!! While updating post. Try again later!!"]);
        } else {
            return json_encode([
                "status" => false,
                "message" => "Post not found!!"
            ]);
        }
    }

    /** Api for adding comments into the post table */
    public function addComments(Request $request)
    {
        $postId = $request->input("postId");
        $message = $request->input("message");
        $user = $request->user("web");
        if (!$user) {
            return json_encode([
                "status" => false,
                "message" => "You are not authenticated!!"
            ]);
        } elseif (!$postId) {
            return json_encode([
                "status" => false,
                "message" => "Please provide postId to continue!!"
            ]);
        } elseif (!$message) {
            return json_encode([
                "status" => false,
                "message" => "Please provide message to continue!!"
            ]);
        } else {
            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => "Post not found!!"
                ]);
            }
            $comment = [
                "name" => $user->name,
                "message" => $message
            ];
            $post->comments = array_merge($post->comments ?? [], [$comment]);
            $post->save();

            return response()->json([
                'status' => true,
                'message' => "Comment Added!!"
            ]);
        }
    }

    /** API for deleting a comment from the post table */
    public function deleteComment(Request $request)
    {
        $postId = $request->input("postId");
        $commentIndex = $request->input("commentIndex");
        $user = $request->user("web");
        if (!$user) {
            return json_encode([
                "status" => false,
                "message" => "You are not authenticated!!"
            ]);
        }

        if (!$postId || !$commentIndex) {
            return response()->json([
                'status' => false,
                'message' => "Please provide postId and commentIndex to continue!!"
            ]);
        }

        $post = Post::find($postId);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => "Post not found!!"
            ]);
        }

        $comments = $post->comments ?? [];
        if ($commentIndex >= 0 && $commentIndex < count($comments)) {
            unset($comments[$commentIndex]);
            $post->comments = array_values($comments); // Re-index the comments array
            $post->save();

            return response()->json([
                'status' => true,
                'message' => "Comment deleted!!"
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => "Invalid comment index!!"
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $user = $request->user("web");
            if (!$user && $user->role !== "admin") {
                return json_encode([
                    "status" => false,
                    "message" => "You are not authorized!!"
                ]);
            }
            $post = Post::destroy($id);
            return response()->json([
                'status' => true,
                'message' => "Post Deleted deleted!!"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                "Error" =>  $e->getMessage() . "",
                'message' => "Error Deleting Post!!Try Again later!!"
            ]);
        }
    }
}
