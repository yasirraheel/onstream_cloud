@extends('site_app')

@section('head_title', trans('Buy a Plan') . ' | ' . getcong('site_name'))

@section('head_url', Request::url())

@section('content')


    <!-- Start Breadcrumb -->
    <div class="breadcrumb-section bg-xs"
        style="background-image: url('{{ URL::asset('site_assets/images/breadcrum-bg.jpg') }}')">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <h2>{{ trans('Buy a Plan') }}</h2>
                    <nav id="breadcrumbs">
                        <ul>
                            <li><a href="{{ URL::to('/') }}" title="{{ trans('words.home') }}">{{ trans('words.home') }}</a>
                            </li>
                            <li><a href="{{ URL::to('/dashboard') }}"
                                    title="{{ trans('words.dashboard_text') }}">{{ trans('words.dashboard_text') }}</a></li>
                            <li>{{ trans('Buy a Plan') }}</li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- End Breadcrumb -->

    <!-- Start Edit Profile -->
    <div class="edit-profile-area vfx-item-ptb vfx-item-info">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
                <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12 text-center">
                    <form action="{{ URL::to('payment/PayEasyPaisa') }}" method="POST">
                        @csrf <!-- CSRF Token for security -->
                        <div class="edit-profile-form">
                            <div class="membership-plan-list">
                                <h3>{{ $plan_info->plan_name }}</h3>

                                <!-- Price and Currency Display -->
                                <h1>
                                    <span>{{ html_entity_decode(getCurrencySymbols(getcong('currency_code'))) }}</span>
                                    @if(Session::get('coupon_percentage'))
                                        <?php
                                            $discount_price_less = $plan_info->plan_price * Session::get('coupon_percentage') / 100;
                                            $final_plan_price = $plan_info->plan_price - $discount_price_less;
                                            echo number_format($final_plan_price, 2);
                                        ?>
                                    @else
                                    @php
                                        $discount_price_less = 0;
                                    @endphp
                                        <?php echo number_format($plan_info->plan_price, 2); ?>
                                    @endif
                                </h1>

                                <!-- Duration and Device Limit -->
                                <h4>{{ App\SubscriptionPlan::getPlanDuration($plan_info->id) }}</h4>
                                <h4>{{ trans('words.plan_device_limit') }} - {{ $plan_info->plan_device_limit }}</h4>

                                <!-- Hidden Input Fields -->
                                <input type="hidden" name="plan_name" value="{{ $plan_info->plan_name }}">
                                <input type="hidden" name="coupon_percentage" value="{{ Session::get('coupon_percentage') }}">
                                <input type="hidden" name="coupon_code" value="{{ Session::get('coupon_code') }}">
                                <input type="hidden" name="discount_price_less" value="{{ $discount_price_less }}">
                                <input type="hidden" name="plan_price" value="{{ $plan_info->plan_price }}">
                                <input type="hidden" name="plan_id" value="{{ $plan_info->id }}">
                                <input type="hidden" name="plan_device_limit" value="{{ $plan_info->plan_device_limit }}">
                                <input type="hidden" name="payment_id" value="{{ $payment_id }}">

                                <!-- Payment Instructions -->
                                {{-- <h4>{{ $paystack_secret_key }}</h4> --}}
                                <div class="edit-profile-form">
                                    <h5><strong>Payment Instructions:</strong></h5>
                                    <p>{{ $paystack_secret_key}}</p>
                                    <p class="alert-dark"><strong>EasyPaisa Account Number:</strong> {{ $paystack_public_key}}</p>

                                </div>
                                {{-- <p>Please transfer the payment to the EasyPaisa account provided below. After completing the payment, enter the Transaction ID in the box below and submit. Kindly wait for admin approval. Additionally, make sure to send us a message on WhatsApp for confirmation.</p> --}}

                                <!-- Transaction ID Input -->
                                <div class="form-group mb-3">
                                    <label>{{ trans('Transaction ID') }}</label>
                                    <input type="text" name="payment_id" class="form-control" required>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="vfx-item-btn-danger text-uppercase mb-30">{{ trans('words.submit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- End Edit Profile -->

    <script type="text/javascript">
        @if (Session::has('flash_message'))

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
                /*didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer)
                  toast.addEventListener('mouseleave', Swal.resumeTimer)
                }*/
            })

            Toast.fire({
                icon: 'success',
                title: '{{ Session::get('flash_message') }}'
            })
        @endif


        @if (count($errors) > 0)

            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: '<p>@foreach ($errors->all() as $error) {{ $error }}<br/> @endforeach</p>',
                showConfirmButton: true,
                confirmButtonColor: '#10c469',
                background: "#1a2234",
                color: "#fff"
            })
        @endif
    </script>

@endsection
