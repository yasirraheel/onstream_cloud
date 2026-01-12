<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Auth;
use Session;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Display the WhatsApp message form
     */
    public function index()
    {
        // Check if user is admin
        if (Auth::User()->usertype != "Admin") {
            Session::flash('flash_message', 'Access denied');
            return redirect('admin/dashboard');
        }

        $page_title = "WhatsApp - Send Message";

        // Check if API is configured
        $isConfigured = $this->whatsappService->isConfigured();

        return view("admin.whatsapp.send_message", compact('page_title', 'isConfigured'));
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(Request $request)
    {
        // Check if user is admin
        if (Auth::User()->usertype != "Admin") {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Validate request
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        // Check if API is configured
        if (!$this->whatsappService->isConfigured()) {
            return back()->with('error_flash_message', 'WhatsApp API is not configured. Please check WA_API_KEY in .env file');
        }

        // Get form data
        $number = $request->input('number');
        $message = $request->input('message');
        $accountName = $request->input('account_name');
        $sessionId = $request->input('session_id');

        // Send message
        $result = $this->whatsappService->sendMessage($number, $message, $accountName, $sessionId);

        if ($result['success']) {
            Session::flash('flash_message', 'Message sent successfully!');
        } else {
            Session::flash('error_flash_message', $result['message']);
        }

        return back();
    }

    /**
     * Send message via AJAX
     */
    public function sendMessageAjax(Request $request)
    {
        // Check if user is admin
        if (Auth::User()->usertype != "Admin") {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        // Validate request
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string|max:1000',
        ]);

        // Check if API is configured
        if (!$this->whatsappService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'WhatsApp API is not configured. Please check WA_API_KEY in .env file'
            ]);
        }

        // Get form data
        $number = $request->input('number');
        $message = $request->input('message');
        $accountName = $request->input('account_name');
        $sessionId = $request->input('session_id');

        // Send message
        $result = $this->whatsappService->sendMessage($number, $message, $accountName, $sessionId);

        return response()->json($result);
    }
}
