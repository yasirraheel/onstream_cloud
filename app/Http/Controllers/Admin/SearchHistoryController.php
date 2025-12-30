<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\SearchHistory;
use Illuminate\Http\Request;
use Session;

class SearchHistoryController extends MainAdminController
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

        $page_title = "Search History";

        $search_history = SearchHistory::orderBy('id', 'DESC')->paginate(20);

        return view('admin.pages.search_history.list', compact('page_title', 'search_history'));
    }

    public function delete($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $history = SearchHistory::findOrFail($id);
        $history->delete();

        Session::flash('flash_message', 'Record deleted successfully');

        return redirect()->back();
    }

    public function clear_all()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        SearchHistory::truncate();

        Session::flash('flash_message', 'All search history cleared successfully');

        return redirect()->back();
    }
}
