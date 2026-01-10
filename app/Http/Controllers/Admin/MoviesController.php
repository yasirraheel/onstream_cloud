<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Movies;
use App\Genres;
use App\Language;
use App\RecentlyWatched;
use App\ActorDirector;

use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use App\ApiUrl;
use App\GdUrl;

class MoviesController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');

		parent::__construct();
        check_verify_purchase();

    }
    public function movies_list()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');

         }

        $page_title=trans('words.movies_text');

        $language_list = Language::orderBy('language_name')->get();

        $genres_list = Genres::orderBy('genre_name')->get();

        $trial_movies = null;

        if(isset($_GET['s']))
        {
            $keyword = $_GET['s'];
            $movies_list = Movies::where("video_title", "LIKE","%$keyword%")->orderBy('video_title')->paginate(12);

            $movies_list->appends(\Request::only('s'))->links();
        }
        else if(isset($_GET['language_id']))
        {
            $language_id = $_GET['language_id'];
            $movies_list = Movies::where("movie_lang_id", "=",$language_id)->orderBy('id','DESC')->paginate(12);

            $movies_list->appends(\Request::only('language_id'))->links();
        }
        else if(isset($_GET['genres_id']))
        {
            $genres_id = $_GET['genres_id'];
            $movies_list = Movies::whereRaw("find_in_set('$genres_id',movie_genre_id)")->orderBy('id','DESC')->paginate(12);

            $movies_list->appends(\Request::only('genres_id'))->links();
        }
        else
        {
            $trialQuery = Movies::where('video_type', 'URL')
                ->where(function($q) {
                    $q->whereNull('video_url')
                      ->orWhere('video_url', '')
                      ->orWhere('video_url', 'LIKE', '%youtube%');
                })
                ->where('upcoming', 0)
                ->where('pending', 0); // Exclude pending

            $trial_movies = $trialQuery->orderBy('id', 'DESC')->get();
            $trial_ids = $trial_movies->pluck('id')->toArray();

            $movies_list = Movies::whereNotIn('id', $trial_ids)
                ->where('upcoming', 0)
                ->where('pending', 0) // Exclude pending
                ->orderBy('id','DESC')
                ->paginate(12);

            $allMovies = Movies::where('upcoming',0)->where('pending', 0)->orderBy('id','DESC')->get(); // Exclude pending

        }
        $allMovies = Movies::where('upcoming',0)->where('pending', 0)->orderBy('id','DESC')->get(); // Exclude pending

        // Pending Movies Count
        $pending_count = Movies::where('pending', 1)->count();

        // Calculate duplicate count
        $duplicate_titles = Movies::where('upcoming', 0)
            ->where('pending', 0)
            ->select('video_title')
            ->groupBy('video_title')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('video_title');

        $duplicate_count = Movies::where('upcoming', 0)
            ->where('pending', 0)
            ->whereIn('video_title', $duplicate_titles)
            ->count();

        return view('admin.pages.movies.list',compact('page_title','movies_list','language_list','genres_list','allMovies', 'trial_movies', 'duplicate_count', 'pending_count'));
    }

    public function duplicate_movies_list()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = 'Duplicate Movies';
        $language_list = Language::orderBy('language_name')->get();
        $genres_list = Genres::orderBy('genre_name')->get();

        $duplicate_titles = Movies::where('upcoming', 0)
            ->where('pending', 0)
            ->select('video_title')
            ->groupBy('video_title')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('video_title');

        $movies_list = Movies::where('upcoming', 0)
            ->where('pending', 0)
            ->whereIn('video_title', $duplicate_titles)
            ->orderBy('video_title')
            ->paginate(12);

        $allMovies = Movies::where('upcoming',0)->where('pending', 0)->orderBy('id','DESC')->get();
        $duplicate_count = $movies_list->total();

        // Pending Movies Count
        $pending_count = Movies::where('pending', 1)->count();

        return view('admin.pages.movies.list', compact('page_title', 'movies_list', 'language_list', 'genres_list', 'allMovies', 'duplicate_count', 'pending_count'));
    }

    public function upcoming_movies_list(){
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');

         }
         $language_list = Language::orderBy('language_name')->get();

         $genres_list = Genres::orderBy('genre_name')->get();
         $page_title = 'Upcoming Movies';
         $movies_list = Movies::where('upcoming',1)->where('pending', 0)->orderBy('id','DESC')->paginate(12); // Exclude pending
         $allMovies = Movies::where('upcoming',1)->where('pending', 0)->orderBy('id','DESC')->get(); // Exclude pending

         return view('admin.pages.movies.list',compact('movies_list','page_title','language_list','genres_list','allMovies'));
    }

    public function pending_movies_list(){
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }
         $language_list = Language::orderBy('language_name')->get();

         $genres_list = Genres::orderBy('genre_name')->get();
         $page_title = 'Pending Movies';
         $movies_list = Movies::where('video_type', '!=', 'Embed')->orderBy('id','DESC')->paginate(12);
         $allMovies = Movies::where('video_type', '!=', 'Embed')->orderBy('id','DESC')->get();

         return view('admin.pages.movies.list',compact('movies_list','page_title','language_list','genres_list','allMovies'));
    }


    public function addMovie(Request $request)    {

        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');

        }


        $page_title=trans('words.add_movie');

        $language_list = Language::orderBy('language_name')->get();
        $genre_list = Genres::orderBy('genre_name')->get();

        $actor_list = ActorDirector::where('ad_type','actor')->orderBy('ad_name')->get();
        $director_list = ActorDirector::where('ad_type','director')->orderBy('ad_name')->get();

        // Fetch URLs from ApiUrl table instead of scanning entire DB
        $api_urls_data = ApiUrl::orderBy('is_used', 'asc') // Available first
            ->orderBy('movie_name', 'asc')
            ->get();

        $import_id = $request->get('import_id');

        return view('admin.pages.movies.addedit',compact('page_title','language_list','genre_list','actor_list','director_list', 'api_urls_data', 'import_id'));
    }

public function addnew(Request $request)
{
    $video_embed_code = ''; // Initialize variable

    if ($request->video_type == 'Embed') {
        if (stripos($request->video_embed_code, '<iframe') !== false) {
            $video_embed_code = $request->video_embed_code;
        } else {
            $video_url = $request->video_embed_code;

            // Log for debugging
            \Log::info('Processing GD URL:', ['url' => $video_url]);

            preg_match('/\/d\/(.*?)\//', $video_url, $matches);
            $file_id = $matches[1] ?? '';

            \Log::info('Extracted file ID:', ['file_id' => $file_id, 'matches' => $matches]);

            if ($file_id) {
                $video_embed_url = "https://drive.google.com/file/d/{$file_id}/preview";
                $video_embed_code = "
<div class=\"responsive-video\">
    <iframe
        src=\"{$video_embed_url}\"
        allow=\"autoplay; fullscreen\"
        allowfullscreen>
    </iframe>
</div>";
                \Log::info('Generated embed code successfully');
            } else {
                $video_embed_code = $request->video_embed_code; // Keep original if not Google Drive format
                \Log::warning('Failed to extract file ID from URL');
            }
        }
    }

    $data = \Request::except(array('_token'));
    $inputs = $request->all();

    if (!empty($inputs['id'])) {
        // Updating → ignore unique imdb_id for current record
        $rule = array(
            'movie_language'    => 'required',
            'genres'            => 'required',
            'video_title'       => [
                'required',
                Rule::unique('movie_videos', 'video_title')->ignore($inputs['id']),
            ],
            'imdb_id'           => [

                // Rule::unique('movie_videos', 'imdb_id')->ignore($inputs['id']),
            ],
        );
    } else {
        // Creating
        $rule = [
            'movie_language'    => 'required',
            'genres'            => 'required',
            'video_title'       => 'required|unique:movie_videos,video_title',
            'video_image_thumb' => 'required',
        ];
    }

    $validator = \Validator::make($data, $rule);

    if ($validator->fails()) {
        return redirect()->back()->withInput()->withErrors($validator->messages());
    }

    if (!empty($inputs['id'])) {
        $movie_obj = Movies::findOrFail($inputs['id']);
    } else {
        $movie_obj = new Movies;
    }

    $video_slug = Str::slug($inputs['video_title'], '-', null);

    $movie_obj->upcoming = $inputs['upcoming'];
    $movie_obj->video_access = $inputs['video_access'];
    $movie_obj->movie_lang_id = $inputs['movie_language'];
    $movie_obj->movie_genre_id = implode(',', $inputs['genres']);
    $movie_obj->video_title = addslashes($inputs['video_title']);
    $movie_obj->video_slug = $video_slug;
    $movie_obj->video_description = addslashes($inputs['video_description']);

    if (isset($inputs['actors_id'])) {
        $movie_obj->actor_id = implode(',', $inputs['actors_id']);
    } else {
        $movie_obj->actor_id = null;
    }

    if (isset($inputs['director_id'])) {
        $movie_obj->director_id = implode(',', $inputs['director_id']);
    } else {
        $movie_obj->director_id = null;
    }

    $movie_obj->release_date = strtotime($inputs['release_date']);
    $movie_obj->duration = $inputs['duration'];

    if (isset($inputs['thumb_link']) && $inputs['thumb_link'] != '') {
        $image_source = $inputs['thumb_link'];
        $save_to = public_path('/upload/images/' . $inputs['video_image_thumb']);
        grab_image($image_source, $save_to);
        $movie_obj->video_image_thumb = 'upload/images/' . $inputs['video_image_thumb'];
    } else {
        $movie_obj->video_image_thumb = $inputs['video_image_thumb'];
    }

    if (isset($inputs['poster_link']) && $inputs['poster_link'] != '') {
        $image_source = $inputs['poster_link'];
        $save_to = public_path('/upload/images/' . $inputs['video_image']);
        grab_image($image_source, $save_to);
        $movie_obj->video_image = 'upload/images/' . $inputs['video_image'];
    } else {
        $movie_obj->video_image = $inputs['video_image'];
    }

    $movie_obj->imdb_id = $inputs['imdb_id'];
    $movie_obj->imdb_rating = $inputs['imdb_rating'];
    $movie_obj->imdb_votes = $inputs['imdb_votes'];
    $movie_obj->content_rating = $inputs['content_rating'];
    $movie_obj->status = $inputs['status'];

    $movie_obj->seo_title = addslashes($inputs['video_title']);
    $max_length = 160;
    $video_description = strip_tags($inputs['video_description']);
    $seo_description = strlen($video_description) > $max_length
        ? substr($video_description, 0, strrpos(substr($video_description, 0, $max_length), ' ')) . '...'
        : $video_description;
    $movie_obj->seo_description = addslashes($seo_description);
    $movie_obj->seo_keyword = addslashes($inputs['seo_keyword']);
    $movie_obj->trailer_url = $inputs['trailer_url'];

        if (isset($inputs['upcoming'])) {
            $movie_obj->upcoming = $inputs['upcoming'];
        }

        // Handle Pending logic via Save Action Button
        if (isset($inputs['save_action']) && $inputs['save_action'] == 'pending') {
            $movie_obj->pending = 1;
        } elseif (isset($inputs['pending'])) {
            // Keep existing logic if input is present (e.g. from hidden input or edit)
            // But if we clicked "Save" (not pending), force pending to 0 unless it was specifically set?
            // Actually, if we click "Save" we usually imply "Publish" or "Save as is".
            // If we are editing a pending movie and click "Save", should it remain pending?
            // The user request implies "Save as Pending" sets it to 1.
            // If they click "Save", it should probably un-pend it if it was pending, OR respect the dropdown if we had one.
            // Since we removed the dropdown, "Save" should probably mean "Not Pending" (Publish) or "Keep Current State".
            // Let's assume "Save" means "Publish" (Pending = 0) to allow workflow: Pending -> Edit -> Save (Publish).
            // UNLESS we are just editing details.
            // However, typically "Save as Pending" is the explicit action to pend. "Save" is the explicit action to publish/update.
            // Let's set pending = 0 if save_action is 'save'.

            if (isset($inputs['save_action']) && $inputs['save_action'] == 'save') {
                 $movie_obj->pending = 0;
            } else {
                 $movie_obj->pending = $inputs['pending'];
            }
        } else {
             // Default fallback
             if (isset($inputs['save_action']) && $inputs['save_action'] == 'pending') {
                $movie_obj->pending = 1;
             } else {
                $movie_obj->pending = 0;
             }
        }

        if ($inputs['upcoming'] == 0 && $movie_obj->pending == 0) {
            $movie_obj->video_type = $inputs['video_type'];

        if (isset($inputs['video_quality'])) {
            $movie_obj->video_quality = $inputs['video_quality'];
        }

        if ($inputs['video_type'] == "URL") {
            $movie_obj->video_url = $inputs['video_url'];
            $movie_obj->video_url_480 = $inputs['video_url_480'];
            $movie_obj->video_url_720 = $inputs['video_url_720'];
            $movie_obj->video_url_1080 = $inputs['video_url_1080'];
        } elseif ($inputs['video_type'] == "Embed") {
            $movie_obj->video_url = $video_embed_code;
        } elseif ($inputs['video_type'] == "HLS") {
            $movie_obj->video_url = $inputs['video_url_hls'];
        } elseif ($inputs['video_type'] == "DASH") {
            $movie_obj->video_url = $inputs['video_url_dash'];
        } else {
            $movie_obj->video_url = $inputs['video_url_local'];
            $movie_obj->video_url_480 = $inputs['video_url_local_480'];
            $movie_obj->video_url_720 = $inputs['video_url_local_720'];
            $movie_obj->video_url_1080 = $inputs['video_url_local_1080'];
        }

        if (isset($inputs['download_enable'])) {
            $movie_obj->download_enable = $inputs['download_enable'];
            $movie_obj->download_url = $inputs['download_url'];
        }

        if (isset($inputs['subtitle_on_off'])) {
            $movie_obj->subtitle_on_off = $inputs['subtitle_on_off'];
        }

        $movie_obj->subtitle_language1 = $inputs['subtitle_language1'];
        $movie_obj->subtitle_url1 = $inputs['subtitle_url1'];
        $movie_obj->subtitle_language2 = $inputs['subtitle_language2'];
        $movie_obj->subtitle_url2 = $inputs['subtitle_url2'];
        $movie_obj->subtitle_language3 = $inputs['subtitle_language3'];
        $movie_obj->subtitle_url3 = $inputs['subtitle_url3'];
    }

    if (!empty($inputs['id']) and $inputs['status'] == 0) {
        DB::table("recently_watched")
            ->where("video_type", "=", "Movies")
            ->where("video_id", "=", $inputs['id'])
            ->delete();
    }

    $movie_obj->save();

    if (!empty($inputs['id'])) {
        \Session::flash('flash_message', trans('words.successfully_updated'));
        return \Redirect::back();
    } else {
        \Session::flash('flash_message', trans('words.added'));
        return \Redirect::back();
    }
}


    public function editMovie($movie_id)
    {
          if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {

                \Session::flash('flash_message', trans('words.access_denied'));

                return redirect('dashboard');

        }

          $page_title=trans('words.edit_movie');

          $language_list = Language::orderBy('language_name')->get();
          $genre_list = Genres::orderBy('genre_name')->get();

          $actor_list = ActorDirector::where('ad_type','actor')->orderBy('ad_name')->get();
          $director_list = ActorDirector::where('ad_type','director')->orderBy('ad_name')->get();

          $movie = Movies::findOrFail($movie_id);

          // Fetch URLs from ApiUrl table
          $api_urls_data = ApiUrl::orderBy('is_used', 'asc') // Available first
            ->orderBy('movie_name', 'asc')
            ->get();

          // Fetch GD URLs for Embed dropdown
          $gd_urls_data = GdUrl::orderBy('is_used', 'asc') // Available first
            ->orderBy('file_name', 'asc')
            ->get();

          return view('admin.pages.movies.addedit',compact('page_title','movie','language_list','genre_list','actor_list','director_list', 'api_urls_data', 'gd_urls_data'));

    }

    public function delete($movie_id)
    {
    	if(Auth::User()->usertype=="Admin" OR Auth::User()->usertype=="Sub_Admin")
        {

        $recently = RecentlyWatched::where('video_type','Movies')->where('video_id',$movie_id)->delete();

        $movie = Movies::findOrFail($movie_id);
        $movie->delete();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();
        }
        else
        {
            \Session::flash('flash_message', trans('words.access_denied'));

            return redirect('admin/dashboard');


        }
    }



}
