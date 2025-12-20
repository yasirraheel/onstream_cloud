<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Transactions;
use App\SubscriptionPlan;
use App\Coupons;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use URL;
use Session;
use Redirect;
use Input;

use Paystack;

class PaystackController extends Controller
{

    /**
     * Redirect the User to Paystack Payment Page
     * @return Url
     */
    public function redirectToGateway(Request $request)
    {
        // Check for existing pending orders for the current user
        $existing_pending_order = Transactions::where('user_id', Auth::user()->id)
            ->where('payment_status', 0) // 0 = PENDING
            ->first();

        if ($existing_pending_order) {
            \Session::flash('error_flash_message', trans('words.pending_order_exists'));
            return redirect('dashboard');
        }

       $plan_info = SubscriptionPlan::where('id',$request->plan_id)->where('status','1')->first();

        if (!$plan_info) {
            return redirect()->back()->with('error', 'Invalid plan.');
        }

       $plan_price = $request->amount;

       $gateway_info = json_decode($request->gateway_info, true); // Decode JSON into associative array

       // Now you can access the keys from the gateway_info array
       $paystack_secret_key = $gateway_info['paystack_secret_key'];
       $paystack_public_key = $gateway_info['paystack_public_key'];

        // Store pending transaction
        $payment_id = uniqid(); // Generate a unique payment ID

        $payment_trans = new Transactions();
        $payment_trans->user_id = Auth::user()->id;
        $payment_trans->email = Auth::user()->email;
        $payment_trans->plan_id = $request->plan_id;
        $payment_trans->gateway = 'Paystack';
        $payment_trans->payment_amount = $plan_price;
        $payment_trans->payment_id = $payment_id;
        $payment_trans->payment_status = 0; // PENDING
        $payment_trans->date = strtotime(date('m/d/Y H:i:s'));
        $payment_trans->save();

        Session::put('paystack_payment_id', $payment_id);
        Session::put('paystack_plan_id', $request->plan_id);
        Session::put('paystack_amount', $plan_price);

        return view('pages.payment.easy_paisa', compact('plan_info','plan_price','paystack_secret_key','paystack_public_key', 'payment_id'));
    }

    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function PayEasyPaisa(Request $request)
{
    $payment_id = Session::get('paystack_payment_id');
    $plan_id = Session::get('paystack_plan_id');
    $plan_amount = Session::get('paystack_amount');

    // Retrieve the pending transaction
    $payment_trans = Transactions::where('payment_id', $payment_id)
        ->where('user_id', Auth::user()->id)
        ->where('payment_status', 0) // PENDING
        ->firstOrFail();

    $plan_info = SubscriptionPlan::where('id', $plan_id)->where('status', '1')->first();

    if (!$plan_info) {
        // If plan is invalid, mark transaction as failed
        $payment_trans->payment_status = 2; // FAILED
        $payment_trans->save();
        return redirect()->back()->with('error_flash_message', trans('words.invalid_plan'));
    }

    $plan_name = $plan_info->plan_name;
    $plan_days = $plan_info->plan_days;
    $amount = $plan_info->plan_price;

    // Check for coupon discount
    $discount_price_less = $request->discount_price_less ?? 0;
    $coupon_percentage = $request->coupon_percentage;

    // If coupon is applied, calculate discounted price
    if ($coupon_percentage) {
        $final_plan_amount = $amount - $discount_price_less;
    } else {
        $final_plan_amount = $amount;
        $coupon_code = null;  // If no coupon, set to null
        $coupon_percentage = null;
    }

    $currency_code = getcong('currency_code') ? getcong('currency_code') : 'USD';
    $user_id = Auth::user()->id;
    $user = User::findOrFail($user_id);

    // Update user plan information
    $user->plan_id = $plan_id;
    $user->plan_payment_status = false; // Initial status before admin approval
    $user->start_date = strtotime(date('m/d/Y'));
    $user->exp_date = strtotime("+$plan_days days");
    $user->plan_amount = $final_plan_amount;
    $user->save();

    // Update coupon usage count
    $coupon_code = $request->coupon_code;
    if ($coupon_code) {
        Coupons::where('coupon_code', $coupon_code)->increment('coupon_used');
    }

    // Update existing payment transaction
    $payment_trans->payment_status = 0; // Keep as PENDING (manual approval)
    $payment_trans->payment_amount = $final_plan_amount;
    $payment_trans->payment_id = $request->payment_id; // Store the user-provided transaction ID
    $payment_trans->coupon_code = $coupon_code;
    $payment_trans->coupon_percentage = $coupon_percentage;
    $payment_trans->save();

    // Send subscription created email
    $user_full_name = $user->name;
    $data_email = ['name' => $user_full_name];

    try {
        \Mail::send('emails.subscription_created', $data_email, function ($message) use ($user, $user_full_name) {
            $message->to($user->email, $user_full_name)
                ->from(getcong('site_email'), getcong('site_name'))
                ->subject('Subscription Created');

        });
    } catch (\Throwable $e) {
        \Log::info($e->getMessage());
    }

    // Flash session data
    Session::flash('coupon_code', Session::get('coupon_code'));
    Session::flash('coupon_percentage', Session::get('coupon_percentage'));
    Session::flash('plan_id', Session::get('plan_id'));

    \Session::flash('success', trans('words.payment_success'));
    $app_name = env('APP_NAME', 'YourDefaultAppName'); // Fallback in case APP_NAME is not set

    // Prepare SMS message with detailed information as a single string
// Prepare SMS message with detailed information
// Prepare SMS message with detailed information in plain text
// Prepare SMS message in plain text format
$sms_message = "Hi, " . $user->name . "! Thank you for subscribing to " . $app_name . ". Your subscription for " . $plan_name . " (" . $currency_code . " " . $plan_amount . ") has been received and is currently pending approval. The subscription will expire on " . date('m/d/Y', $user->exp_date) . ". You will be notified once your subscription is approved. We appreciate your support! If you have any questions, feel free to contact us.";


// Check message length
// if (strlen($sms_message) > 160) {
//     // If the message is too long, rewrite it to fit within 160 characters
//     $sms_message = "Subscription for " . $plan_name . " (" . $currency_code . " " . $plan_amount . ") received. Approval pending. Expiry: " . date('m/d/Y', $user->exp_date) . ". Notification will be sent.";
// }

// Send SMS
$response = sendSMS($sms_message, $user->phone, '6d722f377025e831');
// dd($response);
// Log the response for debugging
\Log::info('SMS Response:', (array) $response);
// Log the response for debugging
\Log::info('SMS Response:', (array) $response);
    // dd($response);
    // Redirect to dashboard
    return redirect('dashboard')->with('flash_message', 'Your Subscription request submitted successfully! .');
}
public function updateTransactionStatus(Request $request, $id)
{
    $transaction = Transactions::findOrFail($id);
    $user_id = $transaction->user_id;

    $transaction->payment_status = $request->payment_status;
    $update = $transaction->save();
    if (!$update) {
        return redirect()->back()->with('error_flash_message', 'Failed to update payment status.');
    }
    else
    {
        $user = User::findOrFail($user_id);
        if($request->payment_status == '1')
        {
            $user->plan_payment_status = true;
            $user->save();
        }
        else
        {
            $user->plan_payment_status = false;
            $user->save();

        }

    }


    return redirect()->back()->with('flash_message', 'Payment status updated successfully.');
}



    }
