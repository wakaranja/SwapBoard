<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests;
use App\Message;
use App\User;
use DB;

class MessageController extends Controller
{
    //Get messages sent by the logged in User
    public function outbox()
    {

    $user=Auth::User();
    $all_messages=$user->messages()->get();


    return view('messages',['all_messages'=>$all_messages]);

    }
    

    //Get Messages received by the logged in User
    public function inbox()
    {

        $user=Auth::User()->id;

        $all_messages=Message::where('receiver_id',$user)->get();


        return view('messages',['all_messages'=>$all_messages]);

    }

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'body'=>'required',
            'receiver'=>'required'
        ]);

        $message=new Message();

        $receiver_id=$request['receiver'];
        $body=$request['body'];
        $user=Auth::User();



        $message->receiver_id=$receiver_id;
        $message->body=$body;
        $message->read=0;
        

        $message_back='There was an error sending your message';

        if($request->user()->messages()->save($message)){
            $message_back='Message sucessfully sent!';
        };

        $user_id=$user->id;

        $thread=Message::where('user_id',$user_id)->where('receiver_id',$receiver_id)->get();

        return redirect()->back();

    }
}
