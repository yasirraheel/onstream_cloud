<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\SearchHistory;
use Illuminate\Http\Request;
use Session;

use Illuminate\Support\Facades\DB;

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

    public function analytics()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Search Analytics";

        // Top 10 Keywords
        $top_keywords = SearchHistory::select('keyword', DB::raw('count(*) as total'))
            ->groupBy('keyword')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Top Countries
        $top_countries = SearchHistory::select('country', 'country_code', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country', 'country_code')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Daily Searches (Last 30 Days)
        $daily_searches = SearchHistory::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Recent Unique Searches
        $recent_searches = SearchHistory::with('user')->select('keyword', 'created_at', 'user_id', 'country')
            ->orderBy('id', 'DESC')
            ->take(10)
            ->get();

        return view('admin.pages.search_history.analytics', compact('page_title', 'top_keywords', 'top_countries', 'daily_searches', 'recent_searches'));
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
