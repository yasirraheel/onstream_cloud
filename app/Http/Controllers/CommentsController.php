<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Comment;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Device\AbstractDeviceParser;

class CommentsController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
             return response()->json(['status' => 'error', 'message' => 'Login required']);
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()]);
        }

        $settings = Settings::findOrFail(1);
        $status = isset($settings->comments_approval) && $settings->comments_approval == 1 ? 1 : 0;

        // Detect Country
        $country_code = 'Unknown';

        // Try getting country code using helper function if available
        if (function_exists('get_user_country_name')) {
             $detected_country = get_user_country_name();
             if($detected_country) {
                 $country_code = $detected_country;
             }
        } else {
             // Fallback to manual detection if helper not exists
            $client_ip = get_user_ip();
            try {
                 $geoplugin_url = "http://www.geoplugin.net/json.gp?ip=".$client_ip;
                 $geoplugin_info = json_decode(@file_get_contents($geoplugin_url));
                 if($geoplugin_info && isset($geoplugin_info->geoplugin_countryName)) {
                     $country_code = $geoplugin_info->geoplugin_countryName;
                 }
            } catch (\Exception $e) {
                // Fallback or ignore
            }
        }

        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->commentable_id = $request->commentable_id;
        $comment->commentable_type = $request->commentable_type;
        $comment->comment = $request->comment;
        $comment->country = $country_code;
        $comment->status = $status;
        $comment->save();

        $message = $status == 1 ? 'Comment added successfully.' : 'Comment submitted for approval.';

        $html = '';
        if ($status == 1) {
            $html = view('_particles.comment_item', compact('comment'))->render();
        }

        return response()->json(['status' => 'success', 'message' => $message, 'html' => $html, 'comment_status' => $status]);
    }
}
