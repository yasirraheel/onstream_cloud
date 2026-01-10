<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\GdUrl;
use App\Movies;
use App\Episodes;
use App\Settings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GdUrlController extends MainAdminController
{
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        check_verify_purchase();
    }

    public function settings()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $page_title = "Google Drive Settings";
        $settings = Settings::findOrFail('1');

        return view('admin.pages.gd_urls.settings', compact('page_title', 'settings'));
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

        $settings->gd_api_key = $inputs['gd_api_key'];
        $settings->gd_folder_ids = $inputs['gd_folder_ids'];

        $settings->save();

        \Session::flash('flash_message', trans('words.successfully_updated'));
        return redirect()->back();
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

        // Calculate total storage in GB
        $total_size_bytes = $gd_urls->sum('file_size');
        $total_size_gb = number_format($total_size_bytes / (1024 * 1024 * 1024), 2);

        // Count distinct folders
        $total_folders = GdUrl::distinct('folder_id')->count('folder_id');

        // Get last fetch time
        $settings = Settings::findOrFail('1');
        $last_fetch = $settings->gd_last_fetch_at;

        return view('admin.pages.gd_urls.list', compact('page_title', 'gd_urls', 'total_urls', 'used_urls_count', 'available_urls_count', 'total_size_gb', 'total_folders', 'last_fetch'));
    }

    public function fetch_urls()
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            \Session::flash('flash_message', trans('words.access_denied'));
            return redirect('dashboard');
        }

        $settings = Settings::findOrFail('1');

        // Google Drive folder IDs from settings (comma-separated)
        $folder_ids_string = $settings->gd_folder_ids ?? env('GOOGLE_DRIVE_FOLDER_IDS', '1J03UKvMPr2EEgAgkfSy9RIHjQblUwG10');
        $folder_ids = array_map('trim', explode(',', $folder_ids_string));
        $api_key = $settings->gd_api_key ?? env('GOOGLE_DRIVE_API_KEY', '');

        // Debug logging
        \Log::info('GD Fetch Debug:', [
            'api_key_from_db' => $settings->gd_api_key,
            'folder_ids_from_db' => $settings->gd_folder_ids,
            'api_key_used' => substr($api_key, 0, 10) . '...',
            'folder_ids_count' => count($folder_ids),
            'folder_ids' => $folder_ids
        ]);

        if (empty($api_key)) {
            \Session::flash('error_flash_message', 'Google Drive API Key is not configured. Please configure it in Google Drive Settings.');
            return redirect()->back();
        }

        if (empty($folder_ids)) {
            \Session::flash('error_flash_message', 'No Google Drive folder IDs configured. Please configure them in Google Drive Settings.');
            return redirect()->back();
        }

        try {
            $count_added = 0;
            $count_updated = 0;
            $total_folders = count($folder_ids);
            $processed_folders = 0;
            $errors = [];

            // Get all existing Used URLs from our database (Movies, Episodes) to check status
            $movie_urls = Movies::pluck('video_url')->toArray();
            $all_used_urls_in_system = $movie_urls;

            foreach ($folder_ids as $folder_id) {
                if (empty($folder_id)) continue;

                $pageToken = null;
                $folder_files_count = 0;

                // Loop through all pages to get ALL files from the folder
                do {
                    $api_endpoint = "https://www.googleapis.com/drive/v3/files?q='{$folder_id}'+in+parents&key={$api_key}&pageSize=1000&fields=nextPageToken,files(id,name,size,mimeType,webContentLink,webViewLink)";

                    if ($pageToken) {
                        $api_endpoint .= "&pageToken={$pageToken}";
                    }

                    $response = Http::timeout(60)->get($api_endpoint);

                    if ($response->successful()) {
                        $data = $response->json();
                        $files = $data['files'] ?? [];
                        $pageToken = $data['nextPageToken'] ?? null;

                        if (empty($files) && $folder_files_count == 0) {
                            $errors[] = "Folder ID: {$folder_id} - No files found or folder is empty.";
                        }

                        foreach ($files as $file) {
                            $file_id = $file['id'] ?? '';
                            $file_name = $file['name'] ?? '';
                            $file_size = $file['size'] ?? 0;
                            $mime_type = $file['mimeType'] ?? '';

                            // Generate Google Drive URL in format that works with embed player
                            $url = "https://drive.google.com/file/d/{$file_id}/view";

                            if (empty($file_id)) continue;

                            // Check if URL is used in system
                            $is_used_system = in_array($url, $all_used_urls_in_system) ? 1 : 0;

                            // Check if file exists in GdUrl table by file_id
                            $existing_entry = GdUrl::where('file_id', $file_id)->first();

                            if ($existing_entry) {
                                // Update existing entry
                                $existing_entry->file_name = $file_name;
                                $existing_entry->url = $url;
                                $existing_entry->folder_id = $folder_id;
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
                                    'folder_id' => $folder_id,
                                    'file_size' => $file_size,
                                    'mime_type' => $mime_type,
                                    'is_used' => $is_used_system
                                ]);
                                $count_added++;
                            }
                            $folder_files_count++;
                        }

                        if ($folder_files_count > 0 && !$pageToken) {
                            $processed_folders++;
                        }
                    } else {
                        $error_data = $response->json();
                        $error_message = $error_data['error']['message'] ?? 'Unknown error';
                        $errors[] = "Folder ID: {$folder_id} - API Error: {$error_message}";
                        break; // Stop pagination on error
                    }
                } while ($pageToken); // Continue while there are more pages
            }

            if ($processed_folders > 0) {
                // Update last fetch timestamp
                $settings->gd_last_fetch_at = now();
                $settings->save();

                $message = "Google Drive URLs Fetched Successfully! Processed {$processed_folders} folder(s). Added: {$count_added}, Updated: {$count_updated}";
                if (!empty($errors)) {
                    $message .= " | Errors: " . implode(' | ', $errors);
                }
                \Session::flash('flash_message', $message);
            } else {
                $error_msg = 'Failed to fetch files from any folder.';
                if (!empty($errors)) {
                    $error_msg .= ' Details: ' . implode(' | ', $errors);
                }
                \Session::flash('error_flash_message', $error_msg);
            }
        } catch (\Exception $e) {
            \Session::flash('error_flash_message', 'Error: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function searchVideo(Request $request)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }

        try {
            $fileName = $request->input('file_name');
            $gdUrlId = $request->input('gd_url_id');

            if (empty($fileName)) {
                return response()->json(['success' => false, 'message' => 'File name is required']);
            }

            // Extract keywords from file name for better search
            // Remove file extension
            $searchTerm = pathinfo($fileName, PATHINFO_FILENAME);

            // Remove common separators and normalize
            $searchTerm = str_replace(['.', '_', '-', '(', ')', '[', ']'], ' ', $searchTerm);

            // Remove common quality indicators and extra info
            $searchTerm = preg_replace('/\b(480p|720p|1080p|2160p|4k|hd|fullhd|bluray|webrip|web-dl|hdtv|xvid|x264|x265|hevc|aac|mp4|mkv|avi)\b/i', '', $searchTerm);

            // Clean up extra spaces
            $searchTerm = trim(preg_replace('/\s+/', ' ', $searchTerm));

            // Search in movie_videos table
            $results = DB::table('movie_videos')
                ->select('id', 'video_title', 'release_date', 'duration', 'video_type', 'video_url')
                ->where(function($query) use ($searchTerm, $fileName) {
                    // Search for exact match first
                    $query->where('video_title', 'LIKE', '%' . $fileName . '%');

                    // Then search for cleaned search term
                    if (!empty($searchTerm)) {
                        $query->orWhere('video_title', 'LIKE', '%' . $searchTerm . '%');
                    }

                    // Search for individual words (for better matching)
                    $words = explode(' ', $searchTerm);
                    foreach ($words as $word) {
                        if (strlen($word) > 3) { // Only search for words longer than 3 characters
                            $query->orWhere('video_title', 'LIKE', '%' . $word . '%');
                        }
                    }
                })
                ->orderBy('id', 'desc')
                ->limit(50) // Limit results to prevent overwhelming output
                ->get();

            return response()->json([
                'success' => true,
                'results' => $results,
                'search_term' => $searchTerm,
                'original_filename' => $fileName
            ]);

        } catch (\Exception $e) {
            \Log::error('GD URL Video Search Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function insertUrlToMovie(Request $request)
    {
        if(Auth::User()->usertype!="Admin" AND Auth::User()->usertype!="Sub_Admin")
        {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }

        try {
            $videoId = $request->input('video_id');
            $gdUrlId = $request->input('gd_url_id');

            if (empty($videoId) || empty($gdUrlId)) {
                return response()->json(['success' => false, 'message' => 'Video ID and GD URL ID are required']);
            }

            // Get the movie
            $movie = Movies::find($videoId);
            if (!$movie) {
                return response()->json(['success' => false, 'message' => 'Movie not found']);
            }

            // Get the GD URL
            $gdUrl = GdUrl::find($gdUrlId);
            if (!$gdUrl) {
                return response()->json(['success' => false, 'message' => 'GD URL not found']);
            }

            $video_url = $gdUrl->url;

            // Log for debugging
            \Log::info('Processing GD URL for insert:', ['url' => $video_url, 'movie_id' => $videoId]);

            // Extract file ID from Google Drive URL (same logic as MoviesController)
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

                // Update the movie
                $movie->video_type = 'Embed';
                $movie->video_url = $video_embed_code;
                $movie->save();

                // Mark GD URL as used
                $gdUrl->is_used = 1;
                $gdUrl->save();

                \Log::info('Movie updated successfully', ['movie_id' => $videoId, 'video_type' => 'Embed']);

                \Session::flash('flash_message', 'GD URL successfully inserted into movie "' . $movie->video_title . '". Video type set to Embed.');

                return response()->json([
                    'success' => true,
                    'message' => 'GD URL inserted successfully',
                    'movie_id' => $videoId,
                    'video_type' => 'Embed'
                ]);

            } else {
                \Log::warning('Failed to extract file ID from URL');
                return response()->json(['success' => false, 'message' => 'Failed to extract file ID from Google Drive URL']);
            }

        } catch (\Exception $e) {
            \Log::error('GD URL Insert Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
