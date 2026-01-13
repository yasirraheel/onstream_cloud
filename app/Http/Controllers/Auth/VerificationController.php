<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use Carbon\Carbon;

class VerificationController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->middleware('auth');
        $this->whatsappService = $whatsappService;
    }

    public function showVerifyForm()
    {
        if (Auth::user()->mobile_verified_at) {
            return redirect('dashboard');
        }

        return view('auth.verify_otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:4',
        ]);

        $user = Auth::user();

        if ($request->otp == $user->otp) {
            $user->mobile_verified_at = Carbon::now();
            $user->otp = null; // Clear OTP after successful verification
            $user->save();

            Session::flash('flash_message', 'Mobile number verified successfully!');
            return redirect('dashboard');
        }

        return back()->withErrors(['otp' => 'Invalid OTP. Please try again.']);
    }

    public function resend()
    {
        $user = Auth::user();

        if ($user->mobile_verified_at) {
            return redirect('dashboard');
        }

        $otp = rand(1000, 9999);
        $user->otp = $otp;
        $user->save();

        $whatsappService = new WhatsAppService();
        $site_name = getcong('site_name');
        $message = "Hello! Your OTP for verification on " . $site_name . " is: " . $otp . ". Please do not share this code with anyone.";
        $result = $whatsappService->sendMessage($user->mobile, $message, 'Onstream');

        if ($result['success']) {
            $user->otp = $otp;
            $user->save();
            Session::flash('flash_message', 'A new OTP has been sent to your WhatsApp number.');
            return back();
        } else {
            return back()->withErrors(['otp' => 'Failed to send OTP: ' . $result['message']]);
        }
    }
}
