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
                ->where('upcoming', 0);

            $trial_movies = $trialQuery->orderBy('id', 'DESC')->get();
            $trial_ids = $trial_movies->pluck('id')->toArray();

            $movies_list = Movies::whereNotIn('id', $trial_ids)
                ->where('upcoming', 0)
                ->orderBy('id','DESC')
                ->paginate(12);

            $allMovies = Movies::where('upcoming',0)->orderBy('id','DESC')->get();

        }
 $allMovies = Movies::where('upcoming',0)->orderBy('id','DESC')->get();
        return view('admin.pages.movies.list',compact('page_title','movies_list','language_list','genres_list','allMovies', 'trial_movies'));
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
         $movies_list = Movies::where('upcoming',1)->orderBy('id','DESC')->paginate(12);
         $allMovies = Movies::where('upcoming',1)->orderBy('id','DESC')->get();

         return view('admin.pages.movies.list',compact('movies_list','page_title','language_list','genres_list','allMovies'));
    }


    public function addMovie()    {

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

        // Prepare list for dropdown: available first, then used
        // Note: The view logic currently uses $used_urls array to mark items.
        // We will pass the full $api_urls collection to the view and handle it there.

        // We still need 'used_urls' for backward compatibility or we can deprecate it if view is updated.
        // Let's keep it but populated from ApiUrl table for consistency?
        // No, 'used_urls' was used to check against external API. Now we use local table.
        // We can just pass $api_urls.

        return view('admin.pages.movies.addedit',compact('page_title','language_list','genre_list','actor_list','director_list', 'api_urls_data'));
    }

public function addnew(Request $request)
{
    if ($request->video_type == 'Embed') {
        if (stripos($request->video_embed_code, '<iframe') !== false) {
            $video_embed_code = $request->video_embed_code;
        } else {
            $video_url = $request->video_embed_code;
            preg_match('/\/d\/(.*?)\//', $video_url, $matches);
            $file_id = $matches[1] ?? '';

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
            } else {
                $video_embed_code = 'Invalid Google Drive URL provided.';
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
            'video_title'       => 'required',
            'imdb_id'           => [

                // Rule::unique('movie_videos', 'imdb_id')->ignore($inputs['id']),
            ],
        );
    } else {
        // Creating → enforce unique imdb_id
        $rule = [
            'movie_language'    => 'required',
            'genres'            => 'required',
            'video_title'       => 'required',
            'video_image_thumb' => 'required',
            'imdb_id'           => 'unique:movie_videos,imdb_id',
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

    if ($inputs['upcoming'] == 0) {
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

          return view('admin.pages.movies.addedit',compact('page_title','movie','language_list','genre_list','actor_list','director_list', 'api_urls_data'));

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
