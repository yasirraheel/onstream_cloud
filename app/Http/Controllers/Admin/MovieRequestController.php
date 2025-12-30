<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\MovieRequest;
use Illuminate\Http\Request;
use Session;

class MovieRequestController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        check_verify_purchase();
    }

    public function index()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Movie Requests";

        $requests = MovieRequest::orderBy('id', 'DESC')->paginate(20);

        return view('admin.pages.movie_requests.list', compact('page_title', 'requests'));
    }

    public function delete($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $request = MovieRequest::findOrFail($id);
        $request->delete();

        Session::flash('flash_message', 'Request deleted successfully');

        return redirect()->back();
    }

    public function status($id, $status)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $request = MovieRequest::findOrFail($id);
        $request->status = $status;
        $request->save();

        Session::flash('flash_message', 'Status updated successfully');

        return redirect()->back();
    }
}
