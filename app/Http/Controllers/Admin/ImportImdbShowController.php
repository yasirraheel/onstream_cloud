<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Language;
use App\Genres;
use App\ActorDirector;


use App\Http\Requests;
use Illuminate\Http\Request;
use Session;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class ImportImdbShowController extends MainAdminController
{
	public function __construct()
    {
		 $this->middleware('auth');

    }

    public function find_imdb_show()
    {
        $show_id= $_GET['id'];

        $default_language=set_tmdb_language();

        $get_tmdb_id = null;

        $is_tmdb_id = is_numeric($show_id);
        $is_imdb_id = (substr($show_id, 0, 2) === 'tt');

        if (!$is_tmdb_id && !$is_imdb_id) {
            // It's a title, search for it
            $search_curl = curl_init();
            $search_query = urlencode($show_id);

            curl_setopt_array($search_curl, [
                CURLOPT_URL => "https://api.themoviedb.org/3/search/tv?query=$search_query&include_adult=false&language=$default_language&page=1",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer ".getcong('tmdb_api_key'),
                    "accept: application/json"
                ],
            ]);

            $search_response = curl_exec($search_curl);
            curl_close($search_curl);

            $search_result = json_decode($search_response);

            if (isset($search_result->results) && count($search_result->results) > 0) {
                // Return list of candidates for selection
                $response['imdb_status'] = 'selection_required';
                $response['results'] = [];

                foreach ($search_result->results as $result) {
                    $response['results'][] = [
                        'id' => $result->id,
                        'title' => $result->name,
                        'release_date' => isset($result->first_air_date) ? $result->first_air_date : 'N/A',
                        'poster_path' => isset($result->poster_path) ? 'https://image.tmdb.org/t/p/w92' . $result->poster_path : null,
                        'overview' => isset($result->overview) ? Str::limit($result->overview, 100) : ''
                    ];
                }

                echo json_encode($response);
                exit;
            } else {
                 $response['imdb_status'] = 'fail';
                 echo json_encode($response);
                 exit;
            }
        } elseif ($is_tmdb_id) {
            $get_tmdb_id = $show_id;
        } else {
            // Existing logic for IMDB ID
            $curl = curl_init();

            curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.themoviedb.org/3/find/$show_id?external_source=imdb_id&language=$default_language",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer  ".getcong('tmdb_api_key'),
                "accept: application/json"
            ],
            ]);

            $response_obj = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            $result= json_decode($response_obj);

            if(isset($result) && isset($result->tv_results[0]))
            {
                $get_tmdb_id= $result->tv_results[0]->id;
            }
        }

        if($get_tmdb_id)
        {
            $curl1 = curl_init();

            curl_setopt_array($curl1, [
            CURLOPT_URL => "https://api.themoviedb.org/3/tv/$get_tmdb_id?append_to_response=videos&language=$default_language",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer ".getcong('tmdb_api_key'),
                "accept: application/json"
            ],
            ]);

            $response_obj1 = curl_exec($curl1);
            $err1 = curl_error($curl1);

            curl_close($curl1);

            $result1= json_decode($response_obj1);

            $response['imdb_status']    = 'success';
            $response['imdbid']         = $get_tmdb_id;
            $response['tmdb_id']         = $get_tmdb_id;
            $response['imdb_rating']         = round($result1->vote_average,1);
            $response['imdb_votes']         = '';

            $response['title']          = $result1->original_name;

            $response['plot']  = $result1->overview;

            //Get Lang
            $lang_list = isset($result1->spoken_languages[0]->english_name) ? $result1->spoken_languages[0]->english_name : (isset($result1->original_language) ? $result1->original_language : 'English');
            $response['language'] = Language::getLanguageID($lang_list);

            $genre = [];
            if (isset($result1->genres) && is_array($result1->genres)) {
                foreach($result1->genres as $genres)
                {
                    $genre[]= Genres::getGenresID($genres->name);
                }
            }

            $response['genre']=$genre;


            $backdrop_path = isset($result1->backdrop_path) ? 'https://image.tmdb.org/t/p/w780'.$result1->backdrop_path.'?language='.$default_language : '';

            $backdrop_file_name = parse_url($backdrop_path, PHP_URL_PATH);

            $response['poster']          = $backdrop_path;
            $response['poster_name']  =basename($backdrop_file_name);


            //Actor Director Add

            $curl2 = curl_init();

            curl_setopt_array($curl2, [
            CURLOPT_URL => "https://api.themoviedb.org/3/tv/$get_tmdb_id/aggregate_credits?language=$default_language",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer ".getcong('tmdb_api_key'),
                "accept: application/json"
            ],
            ]);

            $response2 = curl_exec($curl2);
            $err2 = curl_error($curl2);

            curl_close($curl2);

            $result2= json_decode($response2);

            $cast_length = isset($result2->cast) ? count($result2->cast) : 0;
            $actors_names = [];

            for($cn=0;$cn < $cast_length && $cn <= 20;$cn++)
            {
                if(isset($result2->cast[$cn]))
                {
                $a_id =   $result2->cast[$cn]->id;
                $a_name = $result2->cast[$cn]->original_name;

                $ad_info = ActorDirector::where('ad_name',$a_name)->where('ad_type','actor')->first();

                if(!$ad_info)
                {
                    //Get Actor Details
                    $curl11 = curl_init();

                    curl_setopt_array($curl11, [
                    CURLOPT_URL => "https://api.themoviedb.org/3/person/$a_id?language=$default_language",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "Authorization: Bearer ".getcong('tmdb_api_key'),
                        "accept: application/json"
                    ],
                    ]);

                    $actor_response = curl_exec($curl11);
                    $err1 = curl_error($curl11);

                    curl_close($curl11);

                    $actor_result= json_decode($actor_response);

                    $ad_bio = isset($actor_result->biography) ? $actor_result->biography : '';
                    $ad_birthdate = isset($actor_result->birthday) ? $actor_result->birthday : '';
                    $ad_place_of_birth = isset($actor_result->place_of_birth) ? $actor_result->place_of_birth : '';

                    //Get Actor Details End

                    $ad_obj = new ActorDirector;

                    $ad_slug = Str::slug($a_name, '-',null);

                    $ad_obj->ad_type = 'actor';
                    $ad_obj->ad_name = addslashes($a_name);
                    $ad_obj->ad_bio = addslashes($ad_bio);
                    $ad_obj->ad_birthdate = $ad_birthdate?strtotime($ad_birthdate):NULL;
                    $ad_obj->ad_place_of_birth = addslashes($ad_place_of_birth);
                    $ad_obj->ad_tmdb_id = $a_id;
                    $ad_obj->ad_slug = $ad_slug;

                    if(isset($result2->cast[$cn]->profile_path) && $result2->cast[$cn]->profile_path!="")
                    {
                        $cast_profile_path = 'https://image.tmdb.org/t/p/w300'.$result2->cast[$cn]->profile_path;

                        $cast_file_name = parse_url($cast_profile_path, PHP_URL_PATH);

                        $cast_image_source           =   $cast_profile_path;
                        $cast_save_to                =   public_path('/upload/images/'.basename($cast_file_name));

                        grab_image($cast_image_source,$cast_save_to);

                        $ad_obj->ad_image = 'upload/images/'.basename($cast_file_name);
                    }


                    $ad_obj->save();

                }

                $a_id=ActorDirector::getActorDirectorID($a_name);

                $actors_names[]="<option value='".$a_id."' selected>$a_name</option>";
                }
            }

            $response['actors']=$actors_names;

            $director_names = [];
            $crew_length = isset($result2->crew) ? count($result2->crew) : 0;

            for($cn=0;$cn < $crew_length;$cn++)
            {
                //echo $result2->crew[$cn]->job;
                if(isset($result2->crew[$cn]->jobs[0]->job) AND $result2->crew[$cn]->jobs[0]->job == "Director")
                {
                    if(isset($result2->crew[$cn]->id))
                    {
                        $d_id =    $result2->crew[$cn]->id;
                        $d_name =  $result2->crew[$cn]->original_name;

                        //Add Director
                        $ad_info = ActorDirector::where('ad_name',addslashes($d_name))->where('ad_type','director')->first();

                        if(!$ad_info)
                        {
                                $curl22 = curl_init();

                                curl_setopt_array($curl22, [
                                CURLOPT_URL => "https://api.themoviedb.org/3/person/$d_id?language=$default_language",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "GET",
                                CURLOPT_HTTPHEADER => [
                                    "Authorization: Bearer ".getcong('tmdb_api_key'),
                                    "accept: application/json"
                                ],
                                ]);

                                $director_response = curl_exec($curl22);
                                $err1 = curl_error($curl22);

                                curl_close($curl22);

                                $director_result= json_decode($director_response);

                                $ad_bio = isset($director_result->biography) ? $director_result->biography : '';
                                $ad_birthdate = isset($director_result->birthday) ? $director_result->birthday : '';
                                $ad_place_of_birth = isset($director_result->place_of_birth) ? $director_result->place_of_birth : '';

                                //Get Actor Details End

                                $ad_obj = new ActorDirector;

                                $ad_slug = Str::slug($d_name, '-',null);

                                $ad_obj->ad_type = 'director';
                                $ad_obj->ad_name = addslashes($d_name);
                                $ad_obj->ad_bio = addslashes($ad_bio);
                                $ad_obj->ad_birthdate = $ad_birthdate?strtotime($ad_birthdate):NULL;
                                $ad_obj->ad_place_of_birth = addslashes($ad_place_of_birth);
                                $ad_obj->ad_tmdb_id = $d_id;
                                $ad_obj->ad_slug = $ad_slug;

                            if(isset($result2->crew[$cn]->profile_path) && $result2->crew[$cn]->profile_path!="")
                            {
                                $crew_profile_path = 'https://image.tmdb.org/t/p/w300'.$result2->crew[$cn]->profile_path.'?language='.$default_language;

                                $crew_file_name = parse_url($crew_profile_path, PHP_URL_PATH);

                                $crew_image_source           =   $crew_profile_path;
                                $crew_save_to                =   public_path('/upload/images/'.basename($crew_file_name));

                                grab_image($crew_image_source,$crew_save_to);

                                $ad_obj->ad_image = 'upload/images/'.basename($crew_file_name);
                            }


                            $ad_obj->save();
                        }


                        $d_id=ActorDirector::getActorDirectorID($d_name);

                        $director_names[]="<option value='".$d_id."' selected>$d_name</option>";
                    }

                }

            }

            $response['director']=$director_names;


        }
        else
        {
            $response['imdb_status']    = 'fail';
        }
        //echo $obj->Title;
         //echo $_GET['id'];

         echo json_encode($response);
         exit;
    }


    public function find_imdb_episode()
    {
        $episode_id= $_GET['id'];

        $default_language=set_tmdb_language();

        $curl = curl_init();

        curl_setopt_array($curl, [
          CURLOPT_URL => "https://api.themoviedb.org/3/find/$episode_id?external_source=imdb_id&language=$default_language",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => [
            "Authorization: Bearer ".getcong('tmdb_api_key'),
            "accept: application/json"
          ],
        ]);

        $response_obj = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $result= json_decode($response_obj);

        //dd($response_obj);exit;

        $response['imdb_status']    = 'success';
        $response['imdbid']         = $episode_id;

        $vote_average = isset($result->tv_episode_results[0]->vote_average) ? $result->tv_episode_results[0]->vote_average : 0;
        $response['imdb_rating']         = round($vote_average,1);
        $response['imdb_votes']         = '';

        $response['title']          = isset($result->tv_episode_results[0]->name) ? $result->tv_episode_results[0]->name : '';
        $response['plot']  = isset($result->tv_episode_results[0]->overview) ? $result->tv_episode_results[0]->overview : '';

        $response['runtime']        = isset($result->tv_episode_results[0]->runtime) ? $result->tv_episode_results[0]->runtime : '';

        $air_date = isset($result->tv_episode_results[0]->air_date) ? $result->tv_episode_results[0]->air_date : null;
        $response['released']       = $air_date ? date('m/d/Y',strtotime($air_date)) : date('m/d/Y');

        $still_path = isset($result->tv_episode_results[0]->still_path) ? $result->tv_episode_results[0]->still_path : null;
        $backdrop_path = $still_path ? 'https://image.tmdb.org/t/p/w780'.$still_path.'?language='.$default_language : '';

        $backdrop_file_name = parse_url($backdrop_path, PHP_URL_PATH);

        $response['poster']          = $backdrop_path;
        $response['poster_name']  =basename($backdrop_file_name);

         echo json_encode($response);
         exit;
    }

}
