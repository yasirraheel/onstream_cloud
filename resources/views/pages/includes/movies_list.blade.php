@foreach ($movies_list as $movies_data)
    <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12 col-6">
        <div class="single-video">
            @if (Auth::check())
                <a href="{{ URL::to('movies/details/' . ($movies_data->video_slug ? $movies_data->video_slug : 'movie') . '/' . $movies_data->id) }}"
                    title="{{ $movies_data->video_title }}">
            @else
                @if ($movies_data->video_access == 'Paid')
                    <a href="{{ URL::to('movies/details/' . ($movies_data->video_slug ? $movies_data->video_slug : 'movie') . '/' . $movies_data->id) }}"
                        title="{{ $movies_data->video_title }}" data-toggle="modal" data-target="#loginAlertModal">
                @else
                    <a href="{{ URL::to('movies/details/' . ($movies_data->video_slug ? $movies_data->video_slug : 'movie') . '/' . $movies_data->id) }}"
                        title="{{ $movies_data->video_title }}">
                @endif
            @endif

            <div class="video-img">
                @if ($movies_data->video_access == 'Paid')
                    <div class="vid-lab-premium">
                        <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}" alt="premium" title="premium">
                    </div>
                @endif

                @if($movies_data->video_type == 'URL' && (empty($movies_data->video_url) || strpos($movies_data->video_url, 'youtube') !== false))
                     <div class="vid-lab-trial" style="position: absolute; top: 10px; right: 10px; z-index: 9;">
                        <span class="badge" style="background-color: #ffcc00; color: #000; padding: 5px 10px; border-radius: 5px;">Trial</span>
                    </div>
                @endif

                {{-- Show "Today" badge if movie was created today --}}
                @php
                $label = '';
                if($movies_data->created_at){
                    $label = \Carbon\Carbon::parse($movies_data->created_at)->format('M d, Y');
                } elseif($movies_data->updated_at){
                    $label = \Carbon\Carbon::parse($movies_data->updated_at)->format('M d, Y');
                }
                @endphp
                @if($label)
                <span class="badge badge-danger today-badge">{{ $label }}</span>
                @endif
                <span class="video-item-content">
                    {{ Str::limit(stripslashes($movies_data->video_title), 20) }}
                </span>

                <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                    alt="{{ stripslashes($movies_data->video_title) }}"
                    title="{{ stripslashes($movies_data->video_title) }}">
            </div>
            </a>
        </div>
    </div>
@endforeach
