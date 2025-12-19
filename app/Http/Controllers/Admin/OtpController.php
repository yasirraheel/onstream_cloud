<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class OtpController extends Controller
{
    public function index()
    {
        $page_title = "OTP";
        return view("admin.otp.otp", compact('page_title'));
    }

    // This method is for sending OTP
    // public function sendOtp(Request $request)
    // {
    //     $request->validate([
    //         'phone' => 'required|numeric|digits:10'
    //     ]);

    //     $otp = rand(1000, 9999);
    //     $message = "Your OTP is: " . $otp;
    //     $phone = $request->phone;
    //     $this->sendSms($phone, $message);

    //     return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
    // }

    // This method is for sending SMS request to the Android app
   public function sendSmsRequest(Request $request)
{
    try {
        $client = new Client();
        $url = 'https://onstream.shahabtech.net/send-sms'; // Live server URL
        

        // Sending GET request with phone number and message as query parameters
        $response = $client->request('GET', $url, [
            'query' => [
                'phone_number' => $request->mob_no, // Use dynamic phone number from form
                'message' => "Hello Sir from server", // Use dynamic message from form
            ]
        ]);

        // For debugging, log the response body
        $responseBody = $response->getBody()->getContents();
        \Log::info("Response: " . $responseBody);

        // Check if the SMS was sent successfully
        if ($response->getStatusCode() == 200) {
            return response()->json(['success' => true, 'message' => 'SMS Sent Successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to send SMS']);
        }
    } catch (\Exception $e) {
        // Return error if the request fails
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

}

