<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Comment;
use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Str;

class CommentsController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');
		parent::__construct();
        check_verify_purchase();
    }

    public function comments_list()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
         }

        $page_title = 'Comments'; // You might want to add translation later
        
        $comments_list = Comment::orderBy('id', 'DESC')->paginate(15);

        return view('admin.pages.comments.list', compact('page_title', 'comments_list'));
    }

    public function approve($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $comment = Comment::findOrFail($id);
        $comment->status = 1;
        $comment->save();

        \Session::flash('flash_message', 'Comment approved successfully.'); // Add translation
        return redirect()->back();
    }

    public function delete($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $comment = Comment::findOrFail($id);
        $comment->delete();

        \Session::flash('flash_message', 'Comment deleted successfully.'); // Add translation
        return redirect()->back();
    }
}
