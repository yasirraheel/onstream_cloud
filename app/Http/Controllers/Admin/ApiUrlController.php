<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\ApiUrl;
use App\Movies;
use App\Episodes;
use App\Settings;
use Illuminate\Support\Facades\Http;

class ApiUrlController extends MainAdminController
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

        $page_title = "API URLs Module";

        // Fetch URLs sorted: Available first (is_used=0), then Used (is_used=1)
        // Secondary sort by movie_name
        $api_urls = ApiUrl::orderBy('is_used', 'asc')
            ->orderBy('movie_name', 'asc')
            ->get(); // Using get() for small dataset, paginate() if large

        // Summary Statistics
        $total_urls = $api_urls->count();
        $used_urls_count = $api_urls->where('is_used', 1)->count();
        $available_urls_count = $total_urls - $used_urls_count;

        return view('admin.pages.api_urls.list', compact('page_title', 'api_urls', 'total_urls', 'used_urls_count', 'available_urls_count'));
    }

    public function fetch_urls()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $settings = Settings::findOrFail('1');

        $api_key = $settings->api_url_api_key;
        $base_url = $settings->api_url_base_url;

        if (empty($api_key)) {
            \Session::flash('error_flash_message', 'API Key is not configured. Please configure it in API URL Settings.');
            return redirect()->back();
        }

        if (empty($base_url)) {
            \Session::flash('error_flash_message', 'Base URL is not configured. Please configure it in API URL Settings.');
            return redirect()->back();
        }

        // Ensure base_url ends with / or not? The previous code was https://cineworm.twoflip.com/api/files?api_key=
        // Let's assume the user enters the full endpoint or just base.
        // The user said "option to add base url and api".
        // The previous code was: "https://cineworm.twoflip.com/api/files?api_key=" . $api_key . "&limit=all";
        // So I'll construct it: $base_url . "?api_key=" . $api_key . "&limit=all";
        // I should make sure the user enters the URL up to '.../api/files'.

        // Clean base url to remove trailing slash or parameters
        // Actually, let's assume the user puts the full path to the endpoint without query params.

        $api_endpoint = $base_url . "?api_key=" . $api_key . "&limit=all";

        try {
            $response = Http::get($api_endpoint);

            if ($response->successful()) {
                $data = $response->json();

                // Handle both paginated and non-paginated responses
                // If data is directly in 'data', use it. If it's inside 'data.data' (Laravel pagination), use that.
                $items = [];
                if (isset($data['status']) && $data['status'] == 'success') {
                    if (isset($data['data']['data']) && is_array($data['data']['data'])) {
                        // Paginated response structure
                        $items = $data['data']['data'];
                    } elseif (isset($data['data']) && is_array($data['data'])) {
                        // Standard array response
                        $items = $data['data'];
                    }
                }

                if (!empty($items)) {

                    $count_added = 0;
                    $count_updated = 0;

                    // Get all existing Used URLs from our database (Movies, Episodes) to check status
                    // Note: This check might be heavy if tables are huge, but necessary for accuracy
                    $movie_urls = Movies::pluck('video_url')->toArray();
                    // $episode_urls = Episodes::pluck('video_url')->toArray(); // If needed

                    // Combine used URLs (simplified check against Movies for now as primary use case)
                    $all_used_urls_in_system = $movie_urls;

                    foreach ($items as $item) {
                        $url = $item['direct_link'] ?? '';
                        $name = $item['name'] ?? '';

                        if (empty($url)) continue;

                        // Check if URL is used in system
                        $is_used_system = in_array($url, $all_used_urls_in_system) ? 1 : 0;

                        // Check if URL exists in ApiUrl table
                        $existing_entry = ApiUrl::where('url', $url)->first();

                        if ($existing_entry) {
                            // Update status if changed or name
                            $existing_entry->movie_name = $name;
                            // Only update status to used if system says it's used,
                            // or keep existing used status if we trust our local table more?
                            // Let's trust the system check.
                            $existing_entry->is_used = $is_used_system;
                            $existing_entry->save();
                            $count_updated++;
                        } else {
                            // Create new
                            ApiUrl::create([
                                'movie_name' => $name,
                                'url' => $url,
                                'is_used' => $is_used_system
                            ]);
                            $count_added++;
                        }
                    }

                    \Session::flash('flash_message', "URLs Fetched Successfully! Added: $count_added, Updated: $count_updated");

                    // Update last fetch timestamp
                    $settings->api_url_last_fetch_at = now();
                    $settings->save();

                } else {
                    \Session::flash('error_flash_message', 'API returned no data or invalid structure.');
                }
            } else {
                \Session::flash('error_flash_message', 'Failed to connect to API.');
            }
        } catch (\Exception $e) {
            \Session::flash('error_flash_message', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function delete($id)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $url_obj = ApiUrl::findOrFail($id);
        $url_obj->delete();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();
    }

    public function delete_all()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        ApiUrl::truncate();

        \Session::flash('flash_message', trans('words.deleted'));

        return redirect()->back();
    }

    public function settings()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "API URL Settings";
        $settings = Settings::findOrFail('1');

        return view('admin.pages.api_urls.settings', compact('page_title', 'settings'));
    }

    public function update_settings(Request $request)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $settings = Settings::findOrFail('1');
        $inputs = $request->all();

        $settings->api_url_base_url = $inputs['api_url_base_url'];
        $settings->api_url_api_key = $inputs['api_url_api_key'];

        $settings->save();

        \Session::flash('flash_message', trans('words.successfully_updated'));
        return redirect()->back();
    }
}
