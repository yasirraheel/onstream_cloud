@extends('site_app')

@section('head_title', trans('words.movies_text').' | '.getcong('site_name') )

@section('head_url', Request::url())

@section('content')

@if(count($slider)!=0)
  @include("pages.movies.slider")
@endif

<!-- Banner -->
@if(get_web_banner('list_top')!="")
<div class="vid-item-ptb banner_ads_item">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				{!!stripslashes(get_web_banner('list_top'))!!}
			</div>
		</div>
	</div>
</div>
@endif

<link rel="stylesheet" href="{{ URL::asset('site_assets/css/nice-select.css') }}">

<!-- Start View All Movies -->
<div class="view-all-video-area view-movie-list-item vfx-item-ptb">
  <div class="container-fluid">
   <div class="filter-list-area">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-title-item">{{trans('words.movies_text')}}</div>
        <div class="custom_select_filter">
          <select class="selectpicker show-menu-arrow" id="filter_by_lang">
            <option value="">{{trans('words.languages')}}</option>
            @foreach(\App\Language::where('status','1')->orderBy('id')->get() as $lang_data)
              <option value="{{ URL::to('movies/?lang_id='.$lang_data->id) }}" @if(isset($_GET['lang_id']) && $_GET['lang_id']==$lang_data->id ) selected @endif>{{stripslashes($lang_data->language_name)}}</option>
            @endforeach

          </select>
        </div>
        <div class="custom_select_filter">
          <select class="selectpicker show-menu-arrow" id="filter_by_genre">
            <option value="">{{trans('words.genres_text')}}</option>
            @foreach(\App\Genres::where('status','1')->orderBy('id')->get() as $genres_data)
            <option value="{{ URL::to('movies/?genre_id='.$genres_data->id) }}" @if(isset($_GET['genre_id']) && $_GET['genre_id']==$genres_data->id ) selected @endif>{{stripslashes($genres_data->genre_name)}}</option>
            @endforeach
          </select>
        </div>

        <div class="custom_select_filter">
          <select class="selectpicker show-menu-arrow" id="filter_list" required>
              <option value="?filter=new" @if(isset($_GET['filter']) && $_GET['filter']=='new' ) selected @endif>{{trans('words.newest')}}</option>
			  <option value="?filter=old" @if(isset($_GET['filter']) && $_GET['filter']=='old' ) selected @endif>{{trans('words.oldest')}}</option>
			  <option value="?filter=alpha" @if(isset($_GET['filter']) && $_GET['filter']=='alpha' ) selected @endif>{{trans('words.a_to_z')}}</option>
			  <option value="?filter=rand" @if(isset($_GET['filter']) && $_GET['filter']=='rand' ) selected @endif>{{trans('words.random')}}</option>
          </select>
        </div>
      </div>
    </div>
   </div>
     <div class="row" id="movies_grid">
      @foreach($movies_list as $movies_data)
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 col-6">
          <div class="single-video">
            @if(Auth::check())
                <a href="{{ URL::to('movies/details/'.($movies_data->video_slug?:'movie').'/'.$movies_data->id) }}" title="{{$movies_data->video_title}}">
            @else
               @if($movies_data->video_access=='Paid')
                <a href="{{ URL::to('movies/details/'.($movies_data->video_slug?:'movie').'/'.$movies_data->id) }}" title="{{$movies_data->video_title}}" data-toggle="modal" data-target="#loginAlertModal" title="{{$movies_data->video_title}}">
               @else
                <a href="{{ URL::to('movies/details/'.($movies_data->video_slug?:'movie').'/'.$movies_data->id) }}" title="{{$movies_data->video_title}}">
               @endif
            @endif
               <div class="video-img">
                @if($movies_data->video_access =="Paid")
                <div class="vid-lab-premium">
                  <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}" alt="premium" title="premium">
                </div>
                @endif

                @if($movies_data->video_type == 'URL' && (empty($movies_data->video_url) || strpos($movies_data->video_url, 'youtube') !== false))
                     <div class="vid-lab-trial" style="position: absolute; top: 10px; right: 10px; z-index: 9;">
                        <span class="badge" style="background-color: #ffcc00; color: #000; padding: 5px 10px; border-radius: 5px;">Trial</span>
                    </div>
                @endif

                <span class="video-item-content">{{Str::limit(stripslashes($movies_data->video_title),20)}}</span>
                <img src="{{URL::to('/'.$movies_data->video_image_thumb)}}" alt="{{stripslashes($movies_data->video_title)}}" title="{{stripslashes($movies_data->video_title)}}">
               </div>
            </a>
          </div>
        </div>
      @endforeach
    </div>

    <div class="col-xs-12 text-center" style="margin-top: 20px; margin-bottom: 20px;">
        <button id="load_more_btn" class="btn btn-success" style="padding: 10px 30px; font-weight: bold;">Load More</button>
        <div id="loading_spinner" style="display:none; color: #fff; font-size: 18px;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
    </div>

    <!-- Hidden pagination to get the next URL -->
    <div id="hidden_pagination" style="display:none;">
      @include('_particles.pagination', ['paginator' => $movies_list])
    </div>

    <script type="text/javascript">
    $(document).ready(function() {
        var nextPageUrl = $('#hidden_pagination .next.page-numbers').attr('href');

        if(!nextPageUrl) {
            $('#load_more_btn').hide();
        }

        $('#load_more_btn').click(function() {
            var btn = $(this);
            var spinner = $('#loading_spinner');

            if(nextPageUrl) {
                btn.hide();
                spinner.show();

                $.ajax({
                    url: nextPageUrl,
                    type: 'GET',
                    success: function(data) {
                        var newContent = $(data).find('#movies_grid').html();
                        var newPagination = $(data).find('#hidden_pagination').html();

                        $('#movies_grid').append(newContent);
                        $('#hidden_pagination').html(newPagination);

                        nextPageUrl = $('#hidden_pagination .next.page-numbers').attr('href');

                        spinner.hide();
                        if(nextPageUrl) {
                            btn.show();
                        } else {
                            btn.hide();
                        }
                    },
                    error: function() {
                        spinner.hide();
                        btn.show();
                        alert('Error loading more movies.');
                    }
                });
            }
        });
    });
    </script>
   </div>
</div>
<!-- End View All Movies -->

<!-- Banner -->
@if(get_web_banner('list_bottom')!="")
<div class="vid-item-ptb banner_ads_item pb-3">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				{!!stripslashes(get_web_banner('list_bottom'))!!}
			</div>
		</div>
	</div>
</div>
@endif

@endsection
