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

      <!-- Announcements Section (Non-Popup Only) -->
      @if(isset($announcements) && count($announcements) > 0)
      <div class="col-12 mb-4">
        @foreach($announcements as $announcement)
          @if($announcement->show_as_popup == 0)
          <div class="alert announcement-alert" style="background: linear-gradient(135deg, rgba(255,133,8,0.15) 0%, rgba(253,5,117,0.15) 100%); border: 2px solid #ff8508; border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(255,133,8,0.2);">
            <h5 style="color: #ff8508; margin-bottom: 12px; font-weight: 800;">
              <i class="fa fa-bullhorn" style="animation: bellRing 2s ease-in-out infinite;"></i> {{ $announcement->title }}
            </h5>
            @if(!empty($announcement->image))
              <div class="mb-3">
                <img src="{{ URL::asset('/'.$announcement->image) }}" alt="Announcement" style="max-height:150px;border-radius:10px;box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
              </div>
            @endif
            <p style="margin: 0 0 15px 0; color: #e0e0e0; font-size: 15px; line-height: 1.6;">{!! $announcement->message !!}</p>
            @if(!empty($announcement->cta_text) && !empty($announcement->cta_url))
              <div class="mt-3">
                <a href="javascript:void(0);"
                   onclick="trackRequestPageCTAClick({{ $announcement->id }}, '{{ $announcement->cta_url }}', '{{ $announcement->cta_target ?? '_self' }}')"
                   class="btn btn-warning btn-sm"
                   style="background: linear-gradient(135deg, #ff8508 0%, #fd0575 100%); border: none; color:#fff; font-weight:700; padding: 10px 25px; border-radius: 25px; box-shadow: 0 4px 15px rgba(255,133,8,0.4); transition: all 0.3s ease;">
                  {{ $announcement->cta_text }} <i class="fa fa-arrow-right" style="margin-left: 5px;"></i>
                </a>
              </div>
            @endif
          </div>
          @endif
        @endforeach
      </div>

      <style>
        @keyframes bellRing {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-10deg); }
            20%, 40% { transform: rotate(10deg); }
            50% { transform: rotate(0deg); }
        }

        .announcement-alert a.btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,133,8,0.6);
        }
      </style>
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

@section('scripts')
<script type="text/javascript">
function trackRequestPageCTAClick(announcementId, url, target) {
    $.ajax({
        url: '{{ url("announcement/track-cta-click") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            announcement_id: announcementId
        }
    }).done(function(){
        if(url) {
            if(target === '_blank') {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        }
    }).fail(function(){
        if(url) {
            if(target === '_blank') {
                window.open(url, '_blank');
            } else {
                window.location.href = url;
            }
        }
    });
}
</script>
@endsection
