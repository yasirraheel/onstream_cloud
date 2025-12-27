<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AdManagementController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        check_verify_purchase();
    }

    public function ads_list()
    {
        // Check authorization
        if(Auth::User()->usertype != "Admin" AND Auth::User()->usertype != "Sub_Admin") {
            Session::flash('flash_message', trans('words.access_denied'));
            return redirect('admin/dashboard');
        }

        $page_title = "Ad Management";

        // Fetch products from external API
        try {
            $response = Http::timeout(30)->get('https://topdealsplus.com/api/listings');

            if ($response->successful()) {
                $api_data = $response->json();
                $products = $api_data['data'] ?? [];
            } else {
                $products = [];
                Session::flash('error_flash_message', 'Failed to fetch products from API');
            }
        } catch (\Exception $e) {
            $products = [];
            Session::flash('error_flash_message', 'Error connecting to API: ' . $e->getMessage());
        }

        return view('admin.pages.ads.list', compact('page_title', 'products'));
    }
}
