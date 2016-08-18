<?php
namespace App\Http\Controllers;

use App\Post;
use App\user;
use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Like;
use DB;
use App\Http\Controllers\UserController;



class PostController extends Controller
{
    public function getDashboard()
    {
        $posts=Post::orderBy('created_at','desc')->paginate(20);
        
        $latestusers=User::orderBy('created_at','desc')->take(5)->get();
       
        $comments=Comment::orderBy('created_at','asc')->get();
	   
        $total_post_likes=Like::count();
       
        return view('dashboard',['posts'=>$posts,'latestusers'=>$latestusers,'comments'=>$comments,'total_post_likes'=>$total_post_likes]);
    }


    	
    public function postCreatePost(Request $request)
    {
        $this->validate($request,[
            'body'=>'required|max:1000'
        ]);
        $post=new Post();
        $post->body=$request['body'];
        $message='There was an error posting';
        if($request->user()->posts()->save($post)){
            $message='Post sucessfully created!';

        };
        return redirect()->route('dashboard')->with(['message'=>$message]);
    }

    public function getDeletePost($post_id){
        $post=Post::where('id',$post_id)->first();
        if(Auth::user()!=$post->user){
            return redirect()->back();
        }
        $post->delete();
        return redirect()->route('dashboard')->with(['message'=>'Successfully Deleted!']);

    }

    public function postEditPost(request $request)
    {
        $this->validate($request, [
            'body'=>'required',
        ]);
        $post=Post::find($request['postId']);
        if(Auth::user()!=$post->user){
            return redirect()->back();
        }
        $post->body=$request['body'];
        $post->update();
        return response()->json(['new_body'=>$post->body],200);
    }

    public function postLikePost(Request $request){
        $post_id=$request['postId'];
        $is_Like=$request['isLike']=== 'true';
        $update=false;
        $post=Post::find($post_id);
        if(!$post){
            return null;
        }
        $user=Auth::user();
        $like=$user->likes()->where('post_id',$post_id)->first();
        if($like){
            $already_liked=$like->like;
            $update=true;
            if($already_liked==$is_Like){
                $like->delete();
                return null;
            }
        }else{
            $like=new Like();
        }
        $like->like=$is_Like;
        $like->user_id=$user->id;
        $like->post_id=$post->id;
        if($update){
            $like->update();
        }else{
            $like->save();
        }
        return null;
    }
	
	public function createComment(Request $request)
	{
		$this->validate($request,[
            'body'=>'required|max:1000'
        ]);
		
		$post=Post::find($request ['post_id']);
		
        $comment=new Comment();
        $comment->body=$request['body'];
		$comment->user_id=$request->user()->id;
		
        $message='There was an error posting your comment';
		
        if($post->comments()->save($comment)){
            $message='Comment sucessfully created!';
        };
        return redirect()->route('dashboard')->with(['message'=>$message]);
	}
}