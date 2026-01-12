<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Slider;
use App\Series;
use App\Movies;
use App\HomeSections;
use App\Sports;
use App\Pages;
use App\RecentlyWatched;
use App\LiveTV;
use App\UsersDeviceHistory;
use App\AdClick;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

use Session;

use ProtoneMedia\LaravelFFMpeg;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Support\ServiceProvider;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use FFMpeg\Filters\AdvancedMedia\ComplexFilters;
use FFMpeg\Filters\Video\WatermarkFilter;

require(base_path() . '/public/device-detector/vendor/autoload.php');
use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

AbstractDeviceParser::setVersionTruncation(AbstractDeviceParser::VERSION_TRUNCATION_NONE);

use App\SearchHistory;
use App\MovieRequest;
use App\Announcement;
use App\Services\WhatsAppService;

class IndexController extends Controller
{


    public function index(Request $request)
    {

        if(!$this->alreadyInstalled())
        {
            return redirect('public/install');
        }

    	$slider= Slider::where('status',1)->whereRaw("find_in_set('Home',slider_display_on)")->orderby('id','DESC')->get();

        if(Auth::check())
        {
            $current_user_id=Auth::User()->id;

            if(getcong('menu_movies')==0 AND getcong('menu_shows')==0)
            {
                $recently_watched = RecentlyWatched::where('user_id',$current_user_id)->where('video_type','!=','Movies')->where('video_type','!=','Episodes')->orderby('id','DESC')->get();
            }
            else if(getcong('menu_sports')==0 AND getcong('menu_livetv')==0)
            {
                $recently_watched = RecentlyWatched::where('user_id',$current_user_id)->where('video_type','!=','Sports')->where('video_type','!=','LiveTV')->orderby('id','DESC')->get();
            }
            else if(getcong('menu_livetv')==0)
            {
                $recently_watched = RecentlyWatched::where('user_id',$current_user_id)->where('video_type','!=','LiveTV')->orderby('id','DESC')->get();
            }
            else if(getcong('menu_sports')==0)
            {
                $recently_watched = RecentlyWatched::where('user_id',$current_user_id)->where('video_type','!=','Sports')->orderby('id','DESC')->get();
            }
            else if(getcong('menu_movies')==0)
            {
                $recently_watched = RecentlyWatched::where('user_id',$current_user_id)->where('video_type','!=','Movies')->orderby('id','DESC')->get();
            }
            else if(getcong('menu_shows')==0)
            {
                $recently_watched = RecentlyWatched::where('user_id',$current_user_id)->where('video_type','!=','Episodes')->orderby('id','DESC')->get();
            }
            else
            {
                $recently_watched = RecentlyWatched::where('user_id',$current_user_id)->orderby('id','DESC')->get();
            }

        }
        else
        {
            $recently_watched = array();
        }

        $upcoming_movies = Movies::where('upcoming',1)->orderby('id','DESC')->get();
        $upcoming_series = Series::where('upcoming',1)->orderby('id','DESC')->get();

        // Fetch ads from external API
        $ads_products = [];
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get('https://topdealsplus.com/api/listings');
            if ($response->successful()) {
                $api_data = $response->json();
                $products_data = $api_data['data'] ?? [];

                // Shuffle for random order
                shuffle($products_data);

                $ads_products = array_slice($products_data, 0, 12); // Limit to 12 products
            }
        } catch (\Exception $e) {
            $ads_products = [];
        }

        //dd($upcoming_movies);exit;
        $movies_list = Movies::where('status',1)
            ->where('upcoming',0)
            ->orderByRaw("CASE WHEN video_type = 'URL' AND (video_url IS NULL OR video_url = '' OR video_url LIKE '%youtube%') THEN 1 ELSE 0 END DESC")
            ->orderBy('id','DESC')
            ->paginate(30);

        if ($request->ajax()) {
            return view('pages.includes.movies_list', compact('movies_list'));
        }

        $home_sections = HomeSections::where('status',1)->orderby('id')->get();
        $announcements = Announcement::where('is_active', 1)->orderBy('id', 'DESC')->get();

        return view('pages.index',compact('slider','recently_watched','upcoming_movies','upcoming_series','home_sections','movies_list','ads_products','announcements'));

    }

    public function home_collections($slug, $id)
    {
        $home_section = HomeSections::where('id',$id)->where('status',1)->first();

        if(!$home_section) {
            abort(404);
        }

        //echo $home_section->post_type;exit;

        if($home_section->post_type=="Movie")
        {
            return view('pages.home.movies',compact('home_section'));
        }
        else if($home_section->post_type=="Shows")
        {
            return view('pages.home.shows',compact('home_section'));
        }
        else if($home_section->post_type=="LiveTV")
        {
            return view('pages.home.livetv',compact('home_section'));
        }
        else if($home_section->post_type=="Sports")
        {
            return view('pages.home.sports',compact('home_section'));
        }
        else
        {
            return view('pages.home_section',compact('home_section'));
        }

    }




    public function alreadyInstalled()
    {

        return file_exists(base_path('/public/.lic'));
    }

    private function get_location_info($ip)
    {
        if ($ip == '127.0.0.1' || $ip == '::1') {
            return ['country' => 'Localhost', 'country_code' => 'LO'];
        }

        // Check for private IP ranges
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return ['country' => 'Private Network', 'country_code' => 'PN'];
        }

        try {
            // Using ip-api.com (Free, no key required for basic usage)
            // Timeout set to 2 seconds to avoid blocking
            $response = \Illuminate\Support\Facades\Http::timeout(2)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return [
                        'country' => $data['country'],
                        'country_code' => $data['countryCode']
                    ];
                }
            }
        } catch (\Exception $e) {
            // Silently fail if API is down or times out
        }

        return ['country' => null, 'country_code' => null];
    }

    public function search_elastic()
    {
        $keyword = $_GET['s'];

        // Save search history with smart update
        if (!empty($keyword) && strlen($keyword) > 2) {
            $user_id = Auth::check() ? Auth::id() : null;
            $ip_address = request()->ip();

            // Check for recent search from same user/ip (last 60 seconds)
            $last_search = SearchHistory::where('ip_address', $ip_address)
                ->where('user_id', $user_id)
                ->where('created_at', '>=', now()->subSeconds(60))
                ->orderBy('id', 'DESC')
                ->first();

            if ($last_search) {
                // If new keyword contains old keyword (typing forward) or old contains new (backspacing)
                if (strpos($keyword, $last_search->keyword) !== false || strpos($last_search->keyword, $keyword) !== false) {
                    $last_search->keyword = $keyword;
                    $last_search->updated_at = now();
                    $last_search->save();
                } else {
                    $location = $this->get_location_info($ip_address);
                    SearchHistory::create([
                        'keyword' => $keyword,
                        'user_id' => $user_id,
                        'ip_address' => $ip_address,
                        'country' => $location['country'],
                        'country_code' => $location['country_code']
                    ]);
                }
            } else {
                $location = $this->get_location_info($ip_address);
                SearchHistory::create([
                    'keyword' => $keyword,
                    'user_id' => $user_id,
                    'ip_address' => $ip_address,
                    'country' => $location['country'],
                    'country_code' => $location['country_code']
                ]);
            }
        }

        if(getcong('menu_movies'))
        {
            $s_movies_list = Movies::where('status',1)->where("video_title", "LIKE","%$keyword%")->orderBy('video_title')->get();

        }
        else
        {
            $s_movies_list =array();
        }

        if(getcong('menu_shows'))
        {
            $s_series_list = Series::where('status',1)->where("series_name", "LIKE","%$keyword%")->orderBy('series_name')->get();
        }
        else
        {
            $s_series_list=array();
        }

        if(getcong('menu_sports'))
        {
            $s_sports_list = Sports::where('status',1)->where("video_title", "LIKE","%$keyword%")->orderBy('video_title')->get();
        }
        else
        {
            $s_sports_list=array();
        }

        if(getcong('menu_livetv'))
        {
            $live_tv_list = LiveTV::where('status',1)->where("channel_name", "LIKE","%$keyword%")->orderBy('channel_name')->get();
        }
        else
        {
            $live_tv_list=array();
        }


        return view('_particles.search_elastic',compact('s_movies_list','s_series_list','s_sports_list','live_tv_list'));

    }

    public function search()
    {
        $keyword = $_GET['s'];

        // Save search history with smart update
        if (!empty($keyword) && strlen($keyword) > 2) {
            $user_id = Auth::check() ? Auth::id() : null;
            $ip_address = request()->ip();

            // Check for recent search from same user/ip (last 60 seconds)
            $last_search = SearchHistory::where('ip_address', $ip_address)
                ->where('user_id', $user_id)
                ->where('created_at', '>=', now()->subSeconds(60))
                ->orderBy('id', 'DESC')
                ->first();

            if ($last_search) {
                // If new keyword contains old keyword (typing forward) or old contains new (backspacing)
                if (strpos($keyword, $last_search->keyword) !== false || strpos($last_search->keyword, $keyword) !== false) {
                    $last_search->keyword = $keyword;
                    $last_search->updated_at = now();
                    $last_search->save();
                } else {
                    $location = $this->get_location_info($ip_address);
                    SearchHistory::create([
                        'keyword' => $keyword,
                        'user_id' => $user_id,
                        'ip_address' => $ip_address,
                        'country' => $location['country'],
                        'country_code' => $location['country_code']
                    ]);
                }
            } else {
                $location = $this->get_location_info($ip_address);
                SearchHistory::create([
                    'keyword' => $keyword,
                    'user_id' => $user_id,
                    'ip_address' => $ip_address,
                    'country' => $location['country'],
                    'country_code' => $location['country_code']
                ]);
            }
        }

        $movies_list = Movies::where('status',1)->where('upcoming',0)->where("video_title", "LIKE","%$keyword%")->orderBy('video_title')->get();

        $series_list = Series::where('status',1)->where('upcoming',0)->where("series_name", "LIKE","%$keyword%")->orderBy('series_name')->get();

        $sports_video_list = Sports::where('status',1)->where("video_title", "LIKE","%$keyword%")->orderBy('video_title')->get();

        $live_tv_list = LiveTV::where('status',1)->where("channel_name", "LIKE","%$keyword%")->orderBy('channel_name')->get();

        return view('pages.search',compact('movies_list','series_list','sports_video_list','live_tv_list'));
    }

    public function sitemap()
    {
        return response()->view('pages.sitemap')->header('Content-Type', 'text/xml');
    }

    public function sitemap_misc()
    {
        $pages_list = Pages::where('status',1)->orderBy('id')->get();

        return response()->view('pages.sitemap_misc',compact('pages_list'))->header('Content-Type', 'text/xml');
    }


    public function sitemap_movies()
    {
        $movies_list = Movies::where('status',1)->orderBy('id','DESC')->get();

        return response()->view('pages.sitemap_movies',compact('movies_list'))->header('Content-Type', 'text/xml');
    }

    public function sitemap_show()
    {
        $series_list = Series::where('status',1)->orderBy('id','DESC')->get();

        return response()->view('pages.sitemap_show',compact('series_list'))->header('Content-Type', 'text/xml');
    }

    public function sitemap_sports()
    {
        $sports_video_list = Sports::where('status',1)->orderBy('id','DESC')->get();

        return response()->view('pages.sitemap_sports',compact('sports_video_list'))->header('Content-Type', 'text/xml');
    }

    public function sitemap_livetv()
    {
        $live_list = LiveTV::where('status',1)->orderBy('id','DESC')->get();

        return response()->view('pages.sitemap_livetv',compact('live_list'))->header('Content-Type', 'text/xml');
    }

    public function login()
    {
        if (Auth::check()) {

            return redirect('dashboard');
        }

        return view('pages.user.login');
    }

    public function postLogin(Request $request)
    {


        $data =  \Request::except(array('_token'));
        $inputs = $request->all();

        if(getcong('recaptcha_on_login'))
        {
            $rule=array(
                'email' => 'required|email',
                'password' => 'required',
                'g-recaptcha-response' => 'required'
                 );
        }
        else
        {
            $rule=array(
                'email' => 'required|email',
                'password' => 'required'
                 );
        }

         $validator = \Validator::make($data,$rule);

        if ($validator->fails())
        {
                Session::flash('login_flash_error', 'required');
                return redirect()->back()->withInput()->withErrors($validator->messages());
         }

         //check reCaptcha
          if(getcong('recaptcha_on_login'))
          {

                $recaptcha_response= $inputs['g-recaptcha-response'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'secret' => getcong('recaptcha_secret_key'),
                    'response' => $recaptcha_response,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]);

                $resp = json_decode(curl_exec($ch));
                curl_close($ch);

                //dd($resp);exit;

                if ($resp->success!=true) {

                    \Session::flash('error_flash_message', 'Captcha timeout or duplicate');
                    return \Redirect::back();
                }
          }

            $credentials = $request->only('email', 'password');

            $remember_me = $request->has('remember') ? true : false;

            if (Auth::attempt($credentials, $remember_me)) {

                if(Auth::user()->status=='0' AND Auth::user()->deleted_at!=NULL){
                    \Auth::logout();

                    Session::flash('login_flash_error', 'required');
                    return redirect('/login')->withInput()->withErrors(trans('words.account_delete_msg'));
                }

                if(Auth::user()->status=='0'){
                    \Auth::logout();
                    Session::flash('login_flash_error', 'required');
                    return redirect('/login')->withInput()->withErrors(trans('words.account_banned'));
                 }

                return $this->handleUserWasAuthenticated($request);
            }

            Session::flash('login_flash_error', 'required');
            return redirect('/login')->withInput()->withErrors(trans('words.email_password_invalid'));


    }

     /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  bool  $throttles
     * @return \Illuminate\Http\Response
     */
    protected function handleUserWasAuthenticated(Request $request)
    {

        if (method_exists($this, 'authenticated')) {
            return $this->authenticated($request, Auth::user());
        }

        /*$previous_session = Auth::User()->session_id;
        if ($previous_session) {
            Session::getHandler()->destroy($previous_session);
        }

        Auth::user()->session_id = Session::getId();
        Auth::user()->save();
        */

        if(Auth::user()->usertype=='Admin' OR Auth::user()->usertype=='Sub_Admin')
        {
            return redirect('admin/dashboard');
        }
        else
        {

            $user_id=Auth::user()->id;
            /***Save Device***/
            $userAgent = $_SERVER['HTTP_USER_AGENT']; // change this to the useragent you want to parse

            $dd = new DeviceDetector($userAgent);

            $dd->parse();

            if ($dd->isBot()) {
              // handle bots,spiders,crawlers,...
              $botInfo = $dd->getBot();
            } else {
              $clientInfo = $dd->getClient(); // holds information about browser, feed reader, media player, ...
              $osInfo = $dd->getOs();
              $device = $dd->getDeviceName();
              $brand = $dd->getBrandName();
              $model = $dd->getModel();


            if($brand)
              {
                $user_device_name= $brand.' '.$model.' '.$osInfo['platform'].' '.$device;
              }
              else
              {
                $user_device_name= $osInfo['name'].$osInfo['version'].' '.$osInfo['platform'].' '.$device;
              }

                //Save History
                $user_device_obj = new UsersDeviceHistory;

                $user_device_obj->user_id = $user_id;
                $user_device_obj->user_device_name=$user_device_name;
                $user_device_obj->user_session_name=Session::getId();
                $user_device_obj->save();

            }

            /***Save Device End***/

            // OTP Verification Check
            if(!Auth::user()->mobile_verified_at) {
                $user = Auth::user();
                $otp = rand(1000, 9999);
                $user->otp = $otp;
                $user->save();

                try {
                    $whatsappService = new WhatsAppService();
                    $site_name = getcong('site_name');
                    $message = "Hello! Your OTP for verification on " . $site_name . " is: " . $otp . ". Please do not share this code with anyone.";
                    $whatsappService->sendMessage($user->mobile, $message, 'Onstream');
                } catch (\Exception $e) {
                     \Log::error('Login OTP failed: ' . $e->getMessage());
                }

                return redirect()->route('verify.otp');
            }

            return redirect('dashboard');
        }

    }


    public function signup()
    {
        return view('pages.user.signup');
    }

    public function postSignup(Request $request)
    {


        $data =  \Request::except(array('_token'));

        $inputs = $request->all();

        if(getcong('recaptcha_on_signup'))
        {
            $rule=array(
                'name' => 'required',
                'email' => 'required|email|max:200|unique:users',
                'mobile' => 'required',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required',
                'g-recaptcha-response' => 'required'
                 );

        }
        else
        {
            $rule=array(
                'name' => 'required',
                'email' => 'required|email|max:200|unique:users',
                'mobile' => 'required|numeric|unique:users,mobile',
                'password' => 'required|confirmed|min:8',
                'password_confirmation' => 'required'
                 );
        }


         $validator = \Validator::make($data,$rule);

        if ($validator->fails())
        {
                return redirect()->back()->withErrors($validator->messages())->withInput();
        }

        //check reCaptcha
        if(getcong('recaptcha_on_signup'))
          {

                $recaptcha_response= $inputs['g-recaptcha-response'];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'secret' => getcong('recaptcha_secret_key'),
                    'response' => $recaptcha_response,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]);

                $resp = json_decode(curl_exec($ch));
                curl_close($ch);

                //dd($resp);exit;

                if ($resp->success!=true) {

                    \Session::flash('error_flash_message', 'Captcha timeout or duplicate');
                    return \Redirect::back();
                }
          }

        $user = new User;

        //$confirmation_code = str_random(30);


        $user->usertype = 'User';
        $user->name = $inputs['name'];
        $user->email = $inputs['email'];
        $user->mobile = $inputs['mobile'];
        $user->password= bcrypt($inputs['password']);
        $user->whatsapp_consent = isset($inputs['whatsapp_consent']) ? 1 : 0;
        $user->save();

        //Welcome Email

        try{
            $user_name=$inputs['name'];
            $user_email=$inputs['email'];

            $data_email = array(
                'name' => $user_name,
                'email' => $user_email
                );

            \Mail::send('emails.welcome', $data_email, function($message) use ($user_name,$user_email){
                $message->to($user_email, $user_name)
                ->from(getcong('site_email'), getcong('site_name'))
                ->subject('Welcome to '.getcong('site_name'));
            });
        }catch (\Throwable $e) {

            \Log::info($e->getMessage());
        }


        // Generate and Send OTP
        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();

        try {
            $whatsappService = new WhatsAppService();
            $site_name = getcong('site_name');
            $message = "Hello! Your OTP for verification on " . $site_name . " is: " . $otp . ". Please do not share this code with anyone.";
            $whatsappService->sendMessage($user->mobile, $message, 'Onstream');
        } catch (\Exception $e) {
             \Log::error('Signup OTP failed: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('verify.otp');


    }


    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user_id=Auth::user()->id;

        //Delete session file
        $user_session_name=Session::getId();
        \Session::getHandler()->destroy($user_session_name);

        $user_device_obj = UsersDeviceHistory::where('user_id',$user_id)->where('user_session_name',$user_session_name);
        $user_device_obj->delete();

        Auth::logout();

        return redirect('/');
    }

    public function logout_user_remotely($user_session_name)
    {
        $user_id=Auth::user()->id;

        $user_obj = User::findOrFail($user_id);

        //Push Notification on Mobile
       $content = array("en" => "Logout device remotely");

        $fields = array(
                'app_id' => getcong_app('onesignal_app_id'),
                'included_segments' => array('all'),
                'data' => array("foo" => "bar", "logout_remote"=>"1"),
                'filters' => array(array('field' => 'tag', 'key' => 'user_session', 'relation' => '=', 'value' => $user_session_name)),
                'headings'=> array("en" => getcong_app('app_name')),
                'contents' => $content
            );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8','Authorization: Basic '.getcong_app('onesignal_rest_key')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $notify_res = curl_exec($ch);

        curl_close($ch);

        //dd($notify_res);
        //exit;


        //Delete session file
        \Session::getHandler()->destroy($user_session_name);

        $user_device_obj = UsersDeviceHistory::where('user_id',$user_id)->where('user_session_name',$user_session_name);
        $user_device_obj->delete();


        \Session::flash('success', 'Logout device remotely successfully...');

        return redirect('/dashboard');
    }

    public function check_user_remotely_logout_or_not($user_session_name)
    {


        $user_device_obj = UsersDeviceHistory::where('user_session_name',$user_session_name)->first();
        //dd($user_device_obj);
        //exit;

        if($user_device_obj)
        {
           echo "true";
        }
        else
        {
          echo "false";
        }
    }

    public function track_ad_click(Request $request)
    {
        $productId = $request->input('product_id');

        if ($productId) {
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            AdClick::incrementClick($productId, $ipAddress, $userAgent);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    public function offers()
    {
        if(!$this->alreadyInstalled())
        {
            return redirect('public/install');
        }

        $page_title = "Exclusive Deals & Offers";

        // Fetch all products from external API
        $ads_products = [];
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(30)->get('https://topdealsplus.com/api/listings');
            if ($response->successful()) {
                $api_data = $response->json();
                $products_data = $api_data['data'] ?? [];

                // Shuffle for random order
                shuffle($products_data);

                // Add click count to each product
                foreach ($products_data as &$product) {
                    $product['click_count'] = AdClick::getClickCount($product['id']);
                }

                $ads_products = $products_data;
            }
        } catch (\Exception $e) {
            $ads_products = [];
        }

        return view('pages.offers', compact('page_title', 'ads_products'));
    }

    public function movies_request()
    {
        $requested_movies = MovieRequest::select('movie_name', 'language', 'status')
            ->orderBy('id', 'DESC')
            ->get();

        // Fetch active announcements
        $announcements = Announcement::where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return view('pages.movies_request', compact('requested_movies', 'announcements'));
    }

    public function post_movies_request(Request $request)
    {
        $data =  \Request::except(array('_token'));

        $rule = array(
            'movie_name' => 'required',
            'email' => 'nullable|email',
        );

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator->messages());
        }

        $inputs = $request->all();

        $request_obj = new MovieRequest;
        $request_obj->movie_name = $inputs['movie_name'];
        $request_obj->language = isset($inputs['language']) ? $inputs['language'] : null;
        $request_obj->message = isset($inputs['message']) ? $inputs['message'] : null;
        $request_obj->email = isset($inputs['email']) ? $inputs['email'] : null;

        // Handle payment proof upload
        if ($request->hasFile('payment_proof')) {
            $payment_proof = $request->file('payment_proof');
            $filename = 'payment_proof_' . time() . '_' . uniqid() . '.' . $payment_proof->getClientOriginalExtension();
            $payment_proof->move(public_path('upload/payment_proofs'), $filename);
            $request_obj->payment_proof = $filename;
        }

        if(Auth::check())
        {
            $request_obj->user_id = Auth::User()->id;
            if(empty($request_obj->email))
            {
                $request_obj->email = Auth::User()->email;
            }
        }

        $request_obj->save();

        Session::flash('flash_message', 'Your movie request has been submitted successfully!');

        return redirect()->back();
    }

    public function track_announcement_view(Request $request)
    {
        $announcement_id = $request->input('announcement_id');

        if($announcement_id) {
            $announcement = Announcement::find($announcement_id);
            if($announcement) {
                $announcement->incrementViewCount();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false]);
    }

    public function track_announcement_cta_click(Request $request)
    {
        $announcement_id = $request->input('announcement_id');

        if($announcement_id) {
            $announcement = Announcement::find($announcement_id);
            if($announcement) {
                $announcement->incrementCTAClickCount();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false]);
    }

}
