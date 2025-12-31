<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\VideoView;
use App\Movies;
use App\Series;
use App\Episodes;
use App\Sports;
use App\LiveTV;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends MainAdminController
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
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Views Analytics";

        // 1. Total Views
        $total_views = VideoView::count();

        // 2. Today's Views
        $today_views = VideoView::whereDate('created_at', Carbon::today())->count();

        // 3. Yesterday's Views
        $yesterday_views = VideoView::whereDate('created_at', Carbon::yesterday())->count();

        // 4. Last 30 Days Views
        $last_30_days_views = VideoView::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // 5. Top 10 Videos
        $top_videos = VideoView::select('video_id', 'video_type', DB::raw('count(*) as total'))
            ->groupBy('video_id', 'video_type')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        foreach($top_videos as $video) {
            if($video->video_type == 'Movies') {
                $video->title = Movies::getMoviesInfo($video->video_id, 'video_title');
            } elseif($video->video_type == 'Episodes') {
                $video->title = Episodes::getEpisodesInfo($video->video_id, 'video_title');
            } elseif($video->video_type == 'Sports') {
                $video->title = Sports::getSportsInfo($video->video_id, 'video_title');
            } elseif($video->video_type == 'LiveTV') {
                $video->title = LiveTV::getLiveTvInfo($video->video_id, 'channel_name');
            } else {
                $video->title = 'Unknown';
            }
        }

        // 6. Top 10 Countries
        $top_countries = VideoView::select('country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('admin.pages.dashboard.analytics', compact('page_title', 'total_views', 'today_views', 'yesterday_views', 'last_30_days_views', 'top_videos', 'top_countries'));
    }
}
