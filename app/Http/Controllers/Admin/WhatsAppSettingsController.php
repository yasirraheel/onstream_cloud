<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;

class WhatsAppSettingsController extends Controller
{
    /**
     * Display WhatsApp settings page
     */
    public function index()
    {
        // Check if user is admin
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', 'Access denied');
            return redirect('admin/dashboard');
        }

        $page_title = "WhatsApp Settings";

        // Get current settings from .env
        $settings = [
            'api_key' => env('WA_API_KEY'),
            'api_url' => env('WA_API_URL'),
            'account_name' => env('WA_ACCOUNT_NAME', 'OnStream'),
        ];

        return view("admin.whatsapp.settings", compact('page_title', 'settings'));
    }

    /**
     * Update WhatsApp settings in .env file
     */
    public function update(Request $request)
    {
        // Check if user is admin
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', 'Access denied');
            return redirect('admin/dashboard');
        }

        // Validate request
        $request->validate([
            'api_key' => 'required|string',
            'api_url' => 'required|url',
            'account_name' => 'required|string|max:255',
        ]);

        try {
            // Path to .env file
            $envPath = base_path('.env');

            if (!file_exists($envPath)) {
                Session::flash('error_flash_message', '.env file not found');
                return back();
            }

            // Read .env file
            $envContent = file_get_contents($envPath);

            // Update WA_API_KEY
            if (preg_match('/^WA_API_KEY=(.*)$/m', $envContent)) {
                $envContent = preg_replace(
                    '/^WA_API_KEY=(.*)$/m',
                    'WA_API_KEY=' . $request->input('api_key'),
                    $envContent
                );
            } else {
                $envContent .= "\nWA_API_KEY=" . $request->input('api_key');
            }

            // Update WA_API_URL
            if (preg_match('/^WA_API_URL=(.*)$/m', $envContent)) {
                $envContent = preg_replace(
                    '/^WA_API_URL=(.*)$/m',
                    'WA_API_URL=' . $request->input('api_url'),
                    $envContent
                );
            } else {
                $envContent .= "\nWA_API_URL=" . $request->input('api_url');
            }

            // Update WA_ACCOUNT_NAME
            if (preg_match('/^WA_ACCOUNT_NAME=(.*)$/m', $envContent)) {
                $envContent = preg_replace(
                    '/^WA_ACCOUNT_NAME=(.*)$/m',
                    'WA_ACCOUNT_NAME=' . $request->input('account_name'),
                    $envContent
                );
            } else {
                $envContent .= "\nWA_ACCOUNT_NAME=" . $request->input('account_name');
            }

            // Write back to .env file
            file_put_contents($envPath, $envContent);

            Session::flash('flash_message', 'WhatsApp settings updated successfully! Please clear cache for changes to take effect.');

            return back();

        } catch (\Exception $e) {
            Session::flash('error_flash_message', 'Failed to update settings: ' . $e->getMessage());
            return back();
        }
    }
}
