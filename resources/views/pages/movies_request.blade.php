@extends('site_app')

@section('head_title', 'Request Movies | '.getcong('site_name') )

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
            <div class="container-fluid">
              <div class="row justify-content-center">
                <div class="col-12 text-center mb-4">
                  <div class="logo justify-content-center d-flex text-center">
                    @if(getcong('site_logo'))
                      <a href="{{ URL::to('/') }}" title="logo"><img src="{{ URL::asset('/'.getcong('site_logo')) }}" alt="logo" title="logo" class="login-signup-logo"></a>
                    @else
                      <a href="{{ URL::to('/') }}" title="logo"><img src="{{ URL::asset('site_assets/images/logo.png') }}" alt="logo" title="logo" class="login-signup-logo"></a>
                    @endif
                  </div>
                </div>
              </div>

              <div class="row justify-content-center align-items-start">
                <!-- Request Form -->
                <div class="col-lg-4 col-md-6 col-sm-10 mb-4">
                  <div class="login-item-block">
                    <div class="container login-part">
                      <div class="row">
                        <div class="col-12">
                          <h2 class="form-title-item mb-4">Request Movies</h2>
                          {!! Form::open(array('url' => 'movies_request','class'=>'','id'=>'requestform','role'=>'form')) !!}

                          <div class="form-group">
                            <input type="text" name="movie_name" id="movie_name" value="{{old('movie_name')}}" class="form-control" placeholder="Movie Name (Required)" required>
                          </div>

                          <div class="form-group">
                            <input type="text" name="language" id="language" value="{{old('language')}}" class="form-control" placeholder="Language (Optional)">
                          </div>

                          <div class="form-group">
                            <textarea name="message" id="message" class="form-control" placeholder="Message / Additional Info (Optional)" rows="3">{{old('message')}}</textarea>
                          </div>

                          @if(!Auth::check())
                          <div class="form-group">
                            <input type="email" name="email" id="email" value="{{old('email')}}" class="form-control" placeholder="Email (Optional, for updates)">
                          </div>
                          @endif

                          <button class="btn-submit btn-block my-4 mb-4" type="submit">Submit Request</button>
                          {!! Form::close() !!}

                          <p class="text-3 text-center mb-3"><a href="{{ URL::to('/') }}" class="btn-link" title="Back to Home">Back to Home</a></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                @if(isset($requested_movies) && count($requested_movies) > 0)
                <!-- Requested Movies Table -->
                <div class="col-lg-6 col-md-8 col-sm-10">
                  <div class="login-item-block">
                    <div class="container login-part py-4">
                      <div class="row">
                        <div class="col-12">
                          <h4 class="form-title-item mb-3 text-center">Requested Movies List</h4>
                          <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-borderless text-white mb-0">
                              <thead style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                                <tr>
                                  <th class="py-3">Movie Name</th>
                                  <th class="py-3">Language</th>
                                  <th class="py-3">Status</th>
                                </tr>
                              </thead>
                              <tbody>
                                @foreach($requested_movies as $req_movie)
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                  <td class="py-3">{{ $req_movie->movie_name }}</td>
                                  <td class="py-3">{{ $req_movie->language ?? '-' }}</td>
                                  <td class="py-3">
                                    @if($req_movie->status == 'Completed')
                                      <span class="badge badge-success" style="background-color: #28a745;">Completed</span>
                                    @else
                                      <span class="badge badge-warning" style="background-color: #ffc107; color: #000;">Pending</span>
                                    @endif
                                  </td>
                                </tr>
                                @endforeach
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endif
              </div>
            </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- Login Main Wrapper End -->

@endsection
