<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\GdUrl;
use App\Movies;
use App\Settings;
use Illuminate\Support\Facades\Http;

class FetchGdUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gd:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and sync Google Drive URLs from configured folders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting Google Drive URL fetch...');
        
        $settings = Settings::findOrFail('1');
        
        $folder_ids_string = $settings->gd_folder_ids ?? env('GOOGLE_DRIVE_FOLDER_IDS', '');
        $folder_ids = array_map('trim', explode(',', $folder_ids_string));
        $api_key = $settings->gd_api_key ?? env('GOOGLE_DRIVE_API_KEY', '');

        if (empty($api_key)) {
            $this->error('Google Drive API Key is not configured.');
            \Log::error('GD Fetch Cron: API Key not configured');
            return 1;
        }

        if (empty($folder_ids)) {
            $this->error('No Google Drive folder IDs configured.');
            \Log::error('GD Fetch Cron: No folder IDs configured');
            return 1;
        }

        try {
            $count_added = 0;
            $count_updated = 0;
            $processed_folders = 0;
            $errors = [];

            // Get all existing Used URLs from our database (Movies, Episodes) to check status
            $movie_urls = Movies::pluck('video_url')->toArray();
            $all_used_urls_in_system = $movie_urls;

            foreach ($folder_ids as $folder_id) {
                if (empty($folder_id)) continue;

                $pageToken = null;
                $folder_files_count = 0;

                $this->info("Processing folder: {$folder_id}");

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
                            $this->warn("Folder {$folder_id}: No files found");
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
                        $this->error("Folder {$folder_id}: {$error_message}");
                        break; // Stop pagination on error
                    }
                } while ($pageToken); // Continue while there are more pages
            }

            $message = "GD URLs Fetched! Processed {$processed_folders} folder(s). Added: {$count_added}, Updated: {$count_updated}";
            if (!empty($errors)) {
                $message .= " | Errors: " . implode(' | ', $errors);
            }
            
            // Update last fetch timestamp
            $settings->gd_last_fetch_at = now();
            $settings->save();
            
            $this->info($message);
            \Log::info('GD Fetch Cron: ' . $message);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            \Log::error('GD Fetch Cron Error: ' . $e->getMessage());
            return 1;
        }
    }
}
