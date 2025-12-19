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

use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    //private $config;

    public function __construct()
    {
        //parent::__construct();

        $client_id=getPaymentGatewayInfo(1,'paypal_client_id');
        $secret=getPaymentGatewayInfo(1,'paypal_secret');
        $mode=getPaymentGatewayInfo(1,'mode');

         $this->config = [
                    'mode'    => $mode,
                    'sandbox' => [
                        'client_id'         => $client_id,
                        'client_secret'     => $secret,
                        'app_id'            => '',
                     ],
                    'live' => [
                        'client_id'         => $client_id,
                        'client_secret'     => $secret,
                        'app_id'            => '',
                    ],

                    'payment_action' => 'Sale',
                    'currency'       => 'USD',
                    'notify_url'     => '',
                    'locale'         => 'en_US',
                    'validate_ssl'   => true,
                ];
    }


     /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function paypal_pay(Request $request)
    {

        $currency_code=getcong('currency_code')?getcong('currency_code'):'USD';

        $plan_id=$request->get('plan_id');
        $plan_name=$request->get('plan_name');

        // Check for existing pending orders for the current user
        $existing_pending_order = Transactions::where('user_id', Auth::user()->id)
            ->where('payment_status', '!=', 'COMPLETED')
            ->first();

        if ($existing_pending_order) {
            \Session::flash('error_flash_message', trans('words.pending_order_exists'));
            return redirect('dashboard');
        }

        $success_url=\URL::to('paypal/success/');
        $fail_url=\URL::to('paypal/fail/');

        $provider = new PayPalClient;
        $provider->setApiCredentials($this->config);
        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => $success_url,
                "cancel_url" => $fail_url,
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => $currency_code,
                        "value" => $plan_amount
                    ],
                    "description" => $plan_name,
                ]
            ]
        ]);


        if (isset($response['id']) && $response['id'] != null) {

            $payment_id= $response['id'];

            $payment_trans = new Transactions;

            $payment_trans->user_id = Auth::user()->id;
            $payment_trans->email = Auth::user()->email;
            $payment_trans->plan_id = $plan_id;
            $payment_trans->gateway = 'Paypal';
            $payment_trans->payment_amount = $plan_amount;
            $payment_trans->payment_id = $payment_id;
            $payment_trans->payment_status = 'PENDING'; // Set status to PENDING
            $payment_trans->date = strtotime(date('m/d/Y H:i:s'));

            $payment_trans->save();

            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            \Session::flash('error_flash_message','Something went wrong.');
                return redirect('dashboard');


        } else {

            \Session::flash('error_flash_message',$response['message'] ?? 'Something went wrong.');
            return redirect('dashboard');

        }
    }

    /**
     * success transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function paypal_success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials($this->config);
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);


        if (isset($response['status']) && $response['status'] == 'COMPLETED') {

            $payment_id= $response['purchase_units'][0]['payments']['captures'][0]['id'];

            $user_id=Auth::user()->id;
            $user_email=Auth::user()->email;
            $user = User::findOrFail($user_id);

            $plan_id = Session::get('plan_id');
            $plan_info = SubscriptionPlan::where('id',$plan_id)->where('status','1')->first();
            $plan_days=$plan_info->plan_days;

            // Find the pending transaction and update it
            $payment_trans = Transactions::where('user_id', $user_id)
                ->where('payment_id', $request['token'])
                ->where('payment_status', 'PENDING')
                ->firstOrFail();

            $payment_trans->payment_status = 'COMPLETED'; // Update status to COMPLETED

            if(Session::get('coupon_percentage'))
            {
                //If coupon used
                $discount_price_less =  $plan_info->plan_price * Session::get('coupon_percentage') / 100;

                $plan_amount=$plan_info->plan_price - $discount_price_less;

                $coupon_code= Session::get('coupon_code');
                $coupon_percentage= Session::get('coupon_percentage');

                //Update Counpon Used
                Coupons::where('coupon_code', $coupon_code)->update([
                    'coupon_used'=> DB::raw('coupon_used+1')
                ]);

            }
            else
            {
                //If no coupon used
                $plan_amount=$plan_info->plan_price;
                $coupon_code= NULL;
                $coupon_percentage= NULL;
            }

            $user->plan_id = $plan_id;
            $user->start_date = strtotime(date('m/d/Y'));
            $user->exp_date = strtotime(date('m/d/Y', strtotime("+$plan_days days")));

            $user->plan_amount = $plan_amount;

            //$user->subscription_status = 0;
            $user->save();


            $payment_trans->payment_amount = $plan_amount;
            Session::flash('plan_id',Session::get('plan_id'));

            //Subscription Create Email
            $user_full_name=$user->name;

            $data_email = array(
                'name' => $user_full_name
                 );


            try{

                \Mail::send('emails.subscription_created', $data_email, function($message) use ($user,$user_full_name){
                    $message->to($user->email, $user_full_name)
                        ->from(getcong('site_email'), getcong('site_name'))
                        ->subject('Subscription Created');
                });

            }catch (\Throwable $e) {

                \Log::info($e->getMessage());
            }


            \Session::flash('success',trans('words.payment_success'));
            return redirect('dashboard');

        } else {

            \Session::flash('error_flash_message',trans('words.payment_failed'));
            return redirect('dashboard');

        }
    }

    /**
     * cancel transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function paypal_fail()
    {
            // Update the latest pending transaction for the user to FAILED
            Transactions::where('user_id', Auth::user()->id)
                ->where('payment_status', 'PENDING')
                ->update(['payment_status' => 'FAILED']);

            \Session::flash('error_flash_message',trans('words.payment_failed'));
            return redirect('dashboard');

    }

}
