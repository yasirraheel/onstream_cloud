@extends('site_app')

@section('head_title', 'Request Movies | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

<!-- Start Breadcrumb -->
<div class="breadcrumb-section bg-xs" style="background-image: url({{ URL::asset('site_assets/images/breadcrum-bg.jpg') }})">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-12">
          <h2>Request Movies</h2>
          <nav id="breadcrumbs">
            <ul>
              <li><a href="{{ URL::to('/') }}" title="{{trans('words.home')}}">{{trans('words.home')}}</a></li>
              <li>Request Movies</li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </div>
<!-- End Breadcrumb -->

<div class="vfx-item-ptb vfx-item-info">
  <div class="container-fluid">
    <div class="row justify-content-center align-items-start">

      <!-- Announcements Section -->
      @if(count($announcements) > 0)
      <div class="col-12 mb-4">
        @foreach($announcements as $announcement)
        <div class="alert" style="background-color: rgba(255,193,7,0.1); border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin-bottom: 15px;">
          <h5 style="color: #ffc107; margin-bottom: 10px;">
            <i class="fa fa-bullhorn"></i> {{ $announcement->title }}
          </h5>
          @if(!empty($announcement->image))
            <div class="mb-2">
              <img src="{{ URL::asset('/'.$announcement->image) }}" alt="Announcement" style="max-height:120px;border-radius:8px;">
            </div>
          @endif
          <p style="margin: 0; color: #fff;">{!! $announcement->message !!}</p>
          @if(!empty($announcement->cta_text) && !empty($announcement->cta_url))
            <div class="mt-3">
              <a href="{{ $announcement->cta_url }}" target="{{ $announcement->cta_target ?? '_self' }}" class="btn btn-warning btn-sm" style="color:#000; font-weight:700;">
                {{ $announcement->cta_text }}
              </a>
            </div>
          @endif
        </div>
        @endforeach
      </div>
      @endif

      <!-- Request Form -->
      <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
        <div class="login-item-block">
          <div class="login-part">
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

          </div>
        </div>
      </div>

      @if(isset($requested_movies) && count($requested_movies) > 0)
      <!-- Requested Movies Table -->
      <div class="col-lg-6 col-md-8 col-sm-12">
        <div class="login-item-block">
          <div class="login-part py-4">
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
      @endif

    </div>
  </div>
</div>

@endsection
