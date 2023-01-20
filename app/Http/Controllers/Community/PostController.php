<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Community\PostComment;
use App\Models\Community\PostLike;
use App\Models\Community\Post;
use App\Models\Community\PostTaggedUsers;
use App\Http\Resources\Community\PostResource;
use App\Http\Resources\Community\PostCommentResource;
use App\Http\Resources\Community\PostLikeResource;

class PostController extends Controller
{
	const PageLimit = 20;

    function loadCommunityPosts(Request $request){
    	$user = Auth::user();
    	if($user){

    	}
    	else{
    		return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => null]);
    	}
    	$off_set = 0;
    	if($request->has('off_set')){
    		$off_set = $request->off_set;
    	}
    	$posts = Post::skip($off_set)->take(PostController::PageLimit)->get();

    	return response()->json(['data' => PostResource::collection($posts), 'status' => true, 'message' => 'Posts list']);
    }

    function createPost(Request $request){
    	$user = Auth::user();
    	if($user){

    	}
    	else{
    		return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => null]);
    	}

    	$post = new Post;
    	$post->post_description = $request->post_description;
    	$post->user_id = $user->id;
    	if($request->hasFile('post_image'))
		{
			$data = $request->file('post_image')->store(\Config::get('constants.post_images_save'));
			$post->post_image = $data;

            $imageData = $request->post_image;
            $width = getimagesize($imageData)[0]; // getting the image width
                $height = getimagesize($imageData)[1]; // getting the image height
                $post->image_width = $width;
                $post->image_height = $height;
			
		}
		else
		{
			return ['message' => 'No post image'];
		}
		if($request->has('post_privacy')){
			$post->post_privacy = $request->post_privacy;
		}
		$saved = $post->save();
		if($saved){
            if($request->has('tagged_users')){
                $users = $request->tagged_users;
                foreach($users as $usr){
                    $taggedUser = new PostTaggedUsers;
                    $taggedUser->user_id = $usr;
                    $taggedUser->post_id = $post->id;
                    $savedTagged = $taggedUser->save();

                }
            }
			return response()->json(['data' => new PostResource($post), 'status' => true, 'message' => 'Posts list']);
		}
		else{
			return response()->json(['status' => false, 'message' => 'Error saving post', 'data' => null]);
		}

    }

    function addComment(Request $request){
        $user = Auth::user();
        if($user){

        }
        else{
            return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => null]);
        }

        $comment = $request->comment;
        $post_id = $request->post_id;

        $postComment = new PostComment;
        $postComment->comment = $comment;
        $postComment->user_id = $user->id;
        $postComment->post_id = $post_id;


        $saved = $postComment->save();
        if($saved){
            return response()->json(['status' => true, 'message' => 'Comment added', 'data' => new PostCommentResource($postComment)]);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Unable to add comment', 'data' => null]);
        }

    }


    function loadPostComments(Request $request){
        $user = Auth::user();
        if($user){

        }
        else{
            return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => null]);
        }
        $off_set = 0;
        if($request->has('off_set')){
            $off_set = $request->off_set;
        }
        $posts = PostComment::where('post_id',  $request->post_id)->skip($off_set)->take(PostController::PageLimit)->get();

        return response()->json(['data' => PostCommentResource::collection($posts), 'status' => true, 'message' => 'Post comments list']);
    }




    function addLike(Request $request){
        $user = Auth::user();
        if($user){

        }
        else{
            return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => null]);
        }

        
        $post_id = $request->post_id;

        $postComment = new PostLike;
        $postComment->user_id = $user->id;
        $postComment->post_id = $post_id;


        $saved = $postComment->save();
        if($saved){
            return response()->json(['status' => true, 'message' => 'Like added', 'data' => new PostLikeResource($postComment)]);
        }
        else{
            return response()->json(['status' => false, 'message' => 'Unable to like the post', 'data' => null]);
        }

    }

    function loadPostLikes(Request $request){
        $user = Auth::user();
        if($user){

        }
        else{
            return response()->json(['status' => false, 'message' => 'Unauthorized access', 'data' => null]);
        }
        $off_set = 0;
        if($request->has('off_set')){
            $off_set = $request->off_set;
        }
        $posts = PostLike::where('post_id',  $request->post_id)->skip($off_set)->take(PostController::PageLimit)->get();

        return response()->json(['data' => PostLikeResource::collection($posts), 'status' => true, 'message' => 'Post likes list']);
    }
}
