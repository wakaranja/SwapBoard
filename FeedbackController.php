<?php
namespace App\Http\Controllers;

use App\Feedback;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class FeedbackController extends Controller
{
	public function getFeedback()
	{
		$feedbacks=Feedback::all();
		
		return view('feedback',['feedbacks'=>$feedbacks]);
	}
	
	public function viewFeedback()
	{
		$feedbacks=Feedback::orderBy('created_at','desc')->get();
		
		return view('viewmessages',['feedbacks'=>$feedbacks]);
	}
	
	public function feedbackCreateFeedback(Request $request)
	{
		$this->validate($request,[
            'message'=>'required|max:1000'
        ]);
        $feedback=new Feedback();
        $feedback->message=$request['message'];
        $feedback->read=0;
        $message='There was an error sending your feedback';
        if($request->user()->feedbacks()->save($feedback)){
            $message='Feedback sucessfully submitted!';

        };
       return redirect()->route('dashboard')->with(['message'=>$message]); 
	}
	
}