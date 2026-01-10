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

    public function index(Request $request)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Movie Requests";

        $total_requests = MovieRequest::count();
        $pending_requests = MovieRequest::where('status', 'Pending')->count();
        $completed_requests = MovieRequest::where('status', 'Completed')->count();

        $status_filter = $request->get('status');
        
        $query = MovieRequest::orderBy('id', 'DESC');

        if($status_filter){
             $query->where('status', $status_filter);
        }

        $requests = $query->paginate(20);
        $requests->appends($request->all());

        return view('admin.pages.movie_requests.list', compact('page_title', 'requests', 'total_requests', 'pending_requests', 'completed_requests', 'status_filter'));
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
