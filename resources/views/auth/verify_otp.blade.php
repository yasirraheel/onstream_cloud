@extends('site_app')

@section('head_title', 'Verify OTP | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

<!-- Login Main Wrapper Start -->
<div id="main-wrapper">
  <div class="container-fluid px-0 m-0 h-100 mx-auto">
    <div class="row g-0 min-vh-100 overflow-hidden"> 
      <!-- Welcome Text -->
      <div class="col-md-12">
        <div class="hero-wrap d-flex align-items-center h-100">
          <div class="hero-mask"></div>
          <div class="hero-bg hero-bg-scroll" style="background-image:url('{{ URL::asset('site_assets/images/login-signup-bg-img.jpg') }}');"></div>
          <div class="hero-content mx-auto w-100 h-100 d-flex flex-column justify-content-center">
            <div class="row">
              <div class="col-12 col-lg-5 col-xl-5 mx-auto">
                <div class="logo mt-40 mb-20 mb-md-0 justify-content-center d-flex text-center">
               
                  @if(getcong('site_logo'))                 
                    <a href="{{ URL::to('/') }}" title="logo"><img src="{{ URL::asset('/'.getcong('site_logo')) }}" alt="logo" title="logo" class="login-signup-logo"></a>
                  @else
                    <a href="{{ URL::to('/') }}" title="logo"><img src="{{ URL::asset('site_assets/images/logo.png') }}" alt="logo" title="logo" class="login-signup-logo"></a>                          
                  @endif

                </div>
              </div>
            </div>
            <!-- OTP Form -->
        <div class="col-lg-4 col-md-6 col-sm-6 mx-auto d-flex align-items-center login-item-block">
        <div class="container login-part">
          <div class="row">
          <div class="col-12 col-lg-12 col-xl-12 mx-auto">
            <h2 class="form-title-item mb-4">Verify OTP</h2>
             
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(Session::has('flash_message'))
            <div class="alert alert-success">
                {{ Session::get('flash_message') }}
            </div>
            @endif

             {!! Form::open(array('route' => 'verify.otp.submit','class'=>'','id'=>'otpform','role'=>'form')) !!}  

            <div class="form-group">
              <label class="text-white mb-2">Enter the OTP sent to your WhatsApp</label>
              <input type="text" name="otp" id="otp" value="" class="form-control" placeholder="Enter 4-digit OTP" maxlength="4" required>
            </div>
            
            <button class="btn-submit btn-block my-4 mb-4" type="submit">Verify</button>
            {!! Form::close() !!}
            
            <div class="text-center">
                {!! Form::open(array('route' => 'verify.otp.resend','class'=>'','id'=>'resendform','role'=>'form')) !!}
                <p class="text-3 text-center mb-3 text-white">
                    Didn't receive OTP? 
                    <button type="submit" class="btn-link" style="background:none;border:none;padding:0;color:#e40914;">Resend OTP</button>
                </p>
                {!! Form::close() !!}
            </div>

          </div>
          </div>
        </div>
        </div>
        <!-- OTP Form End --> 
          </div>
        </div>
      </div>
      <!-- Welcome Text End -->       
    </div>
  </div>
</div>
<!-- End Login Main Wrapper --> 

@endsection
