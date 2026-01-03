<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GdUrl;
use App\Movies;
use App\Episodes;
use Illuminate\Support\Facades\Http;

class GdUrlController extends MainAdminController
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

        $page_title = "GD URLs Module";

        // Fetch URLs sorted: Available first (is_used=0), then Used (is_used=1)
        // Secondary sort by file_name
        $gd_urls = GdUrl::orderBy('is_used', 'asc')
            ->orderBy('file_name', 'asc')
            ->get();

        // Summary Statistics
        $total_urls = $gd_urls->count();
        $used_urls_count = $gd_urls->where('is_used', 1)->count();
        $available_urls_count = $total_urls - $used_urls_count;

        return view('admin.pages.gd_urls.list', compact('page_title', 'gd_urls', 'total_urls', 'used_urls_count', 'available_urls_count'));
    }

    public function fetch_urls()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        // Google Drive folder ID extracted from the URL
        $folder_id = "1J03UKvMPr2EEgAgkfSy9RIHjQblUwG10";
        $api_key = env('GOOGLE_DRIVE_API_KEY', ''); // You'll need to set this in .env

        if (empty($api_key)) {
            \Session::flash('error_flash_message', 'Google Drive API Key is not configured. Please set GOOGLE_DRIVE_API_KEY in your .env file.');
            return redirect()->back();
        }

        try {
            $api_endpoint = "https://www.googleapis.com/drive/v3/files?q='{$folder_id}'+in+parents&key={$api_key}&fields=files(id,name,size,mimeType,webContentLink,webViewLink)";
            
            $response = Http::get($api_endpoint);

            if ($response->successful()) {
                $data = $response->json();
                $files = $data['files'] ?? [];

                if (!empty($files)) {
                    $count_added = 0;
                    $count_updated = 0;

                    // Get all existing Used URLs from our database (Movies, Episodes) to check status
                    $movie_urls = Movies::pluck('video_url')->toArray();
                    $all_used_urls_in_system = $movie_urls;

                    foreach ($files as $file) {
                        $file_id = $file['id'] ?? '';
                        $file_name = $file['name'] ?? '';
                        $file_size = $file['size'] ?? 0;
                        $mime_type = $file['mimeType'] ?? '';
                        
                        // Generate direct download link
                        $url = "https://drive.google.com/uc?export=download&id={$file_id}";
                        
                        // Alternative: use webViewLink or webContentLink if available
                        if (isset($file['webContentLink'])) {
                            $url = $file['webContentLink'];
                        }

                        if (empty($file_id)) continue;

                        // Check if URL is used in system
                        $is_used_system = in_array($url, $all_used_urls_in_system) ? 1 : 0;

                        // Check if file exists in GdUrl table by file_id
                        $existing_entry = GdUrl::where('file_id', $file_id)->first();

                        if ($existing_entry) {
                            // Update existing entry
                            $existing_entry->file_name = $file_name;
                            $existing_entry->url = $url;
                            $existing_entry->file_size = $file_size;
                            $existing_entry->mime_type = $mime_type;
                            $existing_entry->is_used = $is_used_system;
                            $existing_entry->save();
                            $count_updated++;
                        } else {
                            // Create new entry
                            GdUrl::create([
                                'file_name' => $file_name,
                                'url' => $url,
                                'file_id' => $file_id,
                                'file_size' => $file_size,
                                'mime_type' => $mime_type,
                                'is_used' => $is_used_system
                            ]);
                            $count_added++;
                        }
                    }

                    \Session::flash('flash_message', "Google Drive URLs Fetched Successfully! Added: $count_added, Updated: $count_updated");
                } else {
                    \Session::flash('error_flash_message', 'No files found in the Google Drive folder.');
                }
            } else {
                \Session::flash('error_flash_message', 'Failed to connect to Google Drive API. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            \Session::flash('error_flash_message', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
