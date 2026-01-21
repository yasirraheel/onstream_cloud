<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Comment;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        
        // If the setting column doesn't exist yet (migration skipped), default to approved (1) or pending (0). 
        // Safer to check if property exists or handle exception, but usually Eloquent returns null for non-existent columns if not strict.
        // Actually, if I added the column, it should be there.
        
        $comment = new Comment();
        $comment->user_id = Auth::id();
        $comment->commentable_id = $request->commentable_id;
        $comment->commentable_type = $request->commentable_type;
        $comment->comment = $request->comment;
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
