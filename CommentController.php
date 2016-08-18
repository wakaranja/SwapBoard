<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Comment;

class CommentController extends Controller
{
    //
	
	
	public function deleteComment($comment_id){
        $comment=Comment::where('id',$comment_id)->first();
        if(Auth::user()!=$comment->user){
            return redirect()->back();
        }
        $comment->delete();
        return redirect()->back()->with(['message'=>'Comment Successfully Deleted!']);

    }
}
