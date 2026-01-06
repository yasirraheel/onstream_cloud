@extends('site_app')

@section('head_title', getcong('site_name'))

@section('head_url', Request::url())

@section('content')

    @include('pages.home.slider')

    @if(isset($announcements) && count($announcements) > 0)
      @foreach($announcements as $announcement)
        @if($announcement->show_as_popup == 1)
        <div class="modal fade announcement-modal" id="homeAnnouncementModal{{ $announcement->id }}" tabindex="-1" role="dialog" aria-labelledby="homeAnnouncementModalLabel{{ $announcement->id }}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="homeAnnouncementModalLabel{{ $announcement->id }}">
                  <i class="fa fa-bullhorn"></i> {{ $announcement->title }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                @if(!empty($announcement->image))
                  <div class="mb-2 text-center">
                    <img src="{{ URL::asset('/'.$announcement->image) }}" alt="Announcement" class="img-fluid">
                  </div>
                @endif
                <p>{!! $announcement->message !!}</p>
                @if(!empty($announcement->cta_text) && !empty($announcement->cta_url))
                  <div class="mt-2 text-center">
                    <a href="{{ $announcement->cta_url }}" target="{{ $announcement->cta_target ?? '_self' }}" class="btn btn-warning">
                      {{ $announcement->cta_text }}
                    </a>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endif
      @endforeach
    @endif

    <!-- Banner -->
    @if (get_web_banner('home_top') != '')
        <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        {!! stripslashes(get_web_banner('home_top')) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if (Auth::check() && $recently_watched->count() > 0)
        <!-- Start Recently Watched Video Section -->
        <div class="video-shows-section vfx-item-ptb">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="vfx-item-section">
                            <h3>{{ trans('words.recently_watched') }}</h3>
                        </div>
                        <div class="recently-watched-video-carousel owl-carousel">
                            @foreach ($recently_watched as $i => $watched_videos)
                                <div class="single-video">
                                    @if ($watched_videos->video_type == 'Movies')
                                        <a href="{{ URL::to('movies/details/' . (recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_slug ?: 'movie') . '/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->id) }}"
                                            title="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}">
                                            <div class="video-img">

                                                <span
                                                    class="video-item-content">{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}</span>
                                                <img src="{{ URL::to('/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_image) }}"
                                                    alt="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}"
                                                    title="Movies-{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}">
                                            </div>
                                        </a>
                                    @endif



                                    @if ($watched_videos->video_type == 'Episodes')
                                        <?php $episode_series_id = \App\Episodes::getEpisodesInfo($watched_videos->video_id, 'episode_series_id'); ?>

                                        <div class="single-video">
                                            <a href="{{ URL::to('shows/' . (\App\Series::getSeriesInfo($episode_series_id, 'series_slug') ?: 'show') . '/' . (recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_slug ?: 'episode') . '/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->id) }}"
                                                title="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}">
                                                <div class="video-img">

                                                    <span
                                                        class="video-item-content">{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}</span>
                                                    <img src="{{ URL::to('/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_image) }}"
                                                        alt="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}"
                                                        title="Episodes-{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endif


                                    @if ($watched_videos->video_type == 'Sports')
                                        <div class="single-video">
                                            <a href="{{ URL::to('sports/details/' . (recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_slug ?: 'sport') . '/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->id) }}"
                                                title="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}">
                                                <div class="video-img">

                                                    <span
                                                        class="video-item-content">{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}</span>
                                                    <img src="{{ URL::to('/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_image) }}"
                                                        alt="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}"
                                                        title="Sports-{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->video_title }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                    @if ($watched_videos->video_type == 'LiveTV')
                                        <div class="single-video">
                                            <a href="{{ URL::to('livetv/details/' . (recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->channel_slug ?: 'channel') . '/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->id) }}"
                                                title="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->channel_name }}">
                                                <div class="video-img">

                                                    <span
                                                        class="video-item-content">{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->channel_name }}</span>
                                                    <img src="{{ URL::to('/' . recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->channel_thumb) }}"
                                                        alt="{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->channel_name }}"
                                                        title="LiveTV-{{ recently_watched_info($watched_videos->video_type, $watched_videos->video_id)->channel_name }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endif

                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Recently Watched Video Section -->
    @endif

    <!-- Start Ads Section -->
    @if (count($ads_products) > 0)
        <style>
            .deals-section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .deals-section-title {
                margin: 0;
            }

            .view-all-offers-btn {
                background: linear-gradient(135deg, #ff8508, #fd0575);
                color: #fff;
                padding: 10px 20px;
                border-radius: 25px;
                font-size: 13px;
                font-weight: 700;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 4px 15px rgba(255, 133, 8, 0.4);
                transition: all 0.3s ease;
                white-space: nowrap;
            }

            .view-all-offers-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255, 133, 8, 0.6);
                text-decoration: none;
                color: #fff;
            }

            @media (max-width: 768px) {
                .deals-section-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }

                .deals-section-title {
                    font-size: 16px !important;
                }

                .deals-section-title .sponsored-label {
                    display: block;
                    margin-top: 5px;
                    margin-left: 0 !important;
                }

                .view-all-offers-btn {
                    padding: 8px 16px;
                    font-size: 12px;
                    width: 100%;
                    justify-content: center;
                }
            }

            @media (max-width: 480px) {
                .deals-section-title {
                    font-size: 14px !important;
                }

                .deals-section-title i {
                    font-size: 14px;
                }

                .view-all-offers-btn {
                    font-size: 11px;
                    padding: 7px 14px;
                }
            }

            .sponsored-deal-card {
                background: linear-gradient(135deg, #1e272e 0%, #2c3e50 100%);
                border: 1px solid #34495e;
                border-radius: 12px;
                padding: 15px;
                display: flex;
                gap: 15px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                cursor: pointer;
                height: 160px;
            }

            .sponsored-deal-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
                transition: left 0.5s;
            }

            .sponsored-deal-card:hover::before {
                left: 100%;
            }

            .sponsored-deal-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(255, 133, 8, 0.3);
                border-color: #ff8508;
            }

            .deal-icon {
                width: 100px;
                height: 130px;
                border-radius: 10px;
                object-fit: cover;
                border: 2px solid #445566;
                flex-shrink: 0;
                transition: transform 0.3s ease;
            }

            .sponsored-deal-card:hover .deal-icon {
                transform: scale(1.05) rotate(2deg);
            }

            .deal-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                min-width: 0;
            }

            .deal-header {
                display: flex;
                align-items: start;
                gap: 10px;
            }

            .deal-title {
                font-size: 14px;
                font-weight: 700;
                color: #fff;
                line-height: 1.3;
                margin: 0 0 8px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                flex: 1;
            }

            .deal-hot-badge {
                background: linear-gradient(135deg, #e74c3c, #c0392b);
                color: #fff;
                font-size: 10px;
                font-weight: 700;
                padding: 4px 8px;
                border-radius: 5px;
                white-space: nowrap;
                animation: pulse 2s infinite;
                box-shadow: 0 0 15px rgba(231, 76, 60, 0.5);
            }

            .deal-flash-badge {
                background: linear-gradient(135deg, #f39c12, #e67e22);
                animation: flash 1.5s infinite;
                box-shadow: 0 0 15px rgba(243, 156, 18, 0.5);
            }

            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }

            @keyframes flash {
                0%, 50%, 100% { opacity: 1; }
                25%, 75% { opacity: 0.7; }
            }

            .deal-price-section {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 8px;
            }

            .deal-price {
                font-size: 24px;
                font-weight: 900;
                color: #1abc9c;
                text-shadow: 0 2px 10px rgba(26, 188, 156, 0.3);
            }

            .deal-discount {
                background: #e74c3c;
                color: #fff;
                font-size: 11px;
                font-weight: 700;
                padding: 3px 6px;
                border-radius: 4px;
            }

            .deal-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-bottom: 8px;
            }

            .deal-badge {
                background: rgba(52, 152, 219, 0.2);
                border: 1px solid rgba(52, 152, 219, 0.4);
                color: #5dade2;
                font-size: 10px;
                padding: 3px 8px;
                border-radius: 5px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .deal-badge.green {
                background: rgba(46, 204, 113, 0.2);
                border-color: rgba(46, 204, 113, 0.4);
                color: #58d68d;
            }

            .deal-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .deal-seller {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                color: #bdc3c7;
            }

            .deal-seller img {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                border: 1px solid #445566;
            }

            .deal-rating {
                display: flex;
                align-items: center;
                gap: 5px;
                background: rgba(52, 152, 219, 0.15);
                padding: 4px 10px;
                border-radius: 15px;
                font-size: 12px;
                font-weight: 700;
                color: #3498db;
            }

            .deal-cta {
                position: absolute;
                bottom: 12px;
                right: 12px;
                background: linear-gradient(135deg, #ff8508, #fd0575);
                color: #fff;
                padding: 8px 16px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 4px 15px rgba(255, 133, 8, 0.4);
                transition: all 0.3s ease;
            }

            .sponsored-deal-card:hover .deal-cta {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(255, 133, 8, 0.6);
            }

            .deal-verified {
                color: #1abc9c;
                margin-left: 3px;
            }
        </style>

        <div class="video-shows-section vfx-item-ptb">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="vfx-item-section deals-section-header">
                            <h3 class="deals-section-title">
                                <i class="fa fa-bullhorn" style="margin-right: 8px; color: #ff8508; animation: pulse 2s infinite;"></i>
                                Exclusive Deals - Limited Time Offers!
                                <span class="sponsored-label" style="font-size: 12px; color: #999; font-weight: 400; margin-left: 10px;">Sponsored</span>
                            </h3>
                            <a href="{{ URL::to('offers') }}" class="view-all-offers-btn">
                                <i class="fa fa-th"></i> View All Offers
                            </a>
                        </div>
                        <div class="video-carousel owl-carousel">
                            @foreach ($ads_products as $product)
                                <div class="single-video">
                                    <a href="javascript:void(0);"
                                       onclick="trackAdClick({{ $product['id'] }}, '{{ $product['url'] ?? '#' }}')"
                                       style="text-decoration: none; display: block;">
                                        <div class="sponsored-deal-card">
                                            <img src="{{ $product['thumbnail_url'] ?? '' }}"
                                                 alt="{{ $product['title'] ?? 'Product' }}"
                                                 class="deal-icon"
                                                 onerror="this.src='{{ URL::asset('site_assets/images/video-placeholder.jpg') }}'">

                                            <div class="deal-content">
                                                <div>
                                                    <div class="deal-header">
                                                        <h4 class="deal-title">{{ $product['title'] ?? 'No Title' }}</h4>
                                                        @if ($product['badges']['is_trending'] ?? false)
                                                            <span class="deal-hot-badge">
                                                                <i class="fa fa-fire"></i> HOT
                                                            </span>
                                                        @elseif ($product['badges']['is_flash'] ?? false)
                                                            <span class="deal-hot-badge deal-flash-badge">
                                                                <i class="fa fa-bolt"></i> FLASH
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="deal-price-section">
                                                        <span class="deal-price">
                                                            {{ $product['currency_symbol'] ?? '$' }}{{ number_format($product['final_price'] ?? 0, 2) }}
                                                        </span>
                                                        @if(isset($product['discount']) && $product['discount'] > 0)
                                                            <span class="deal-discount">-{{ $product['discount'] }}%</span>
                                                        @endif
                                                    </div>

                                                    <div class="deal-meta">
                                                        @if(isset($product['badges']['duration']))
                                                            <span class="deal-badge">
                                                                <i class="fa fa-clock-o"></i> {{ $product['badges']['duration'] }}
                                                            </span>
                                                        @endif
                                                        @if(isset($product['badges']['delivery_speed']))
                                                            <span class="deal-badge green">
                                                                <i class="fa fa-truck"></i> {{ $product['badges']['delivery_speed'] }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="deal-footer">
                                                    <div class="deal-seller">
                                                        <img src="{{ $product['seller']['avatar'] ?? '' }}"
                                                             alt="{{ $product['seller']['username'] ?? 'Seller' }}"
                                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\'%3E%3Ccircle fill=\'%2334495e\' cx=\'10\' cy=\'10\' r=\'10\'/%3E%3C/svg%3E'">
                                                        <span>{{ $product['seller']['username'] ?? 'Seller' }}</span>
                                                        @if($product['seller']['is_verified'] ?? false)
                                                            <i class="fa fa-check-circle deal-verified"></i>
                                                        @endif
                                                    </div>

                                                    @if(isset($product['rating']['percentage']) && $product['rating']['percentage'] > 0)
                                                        <div class="deal-rating">
                                                            <i class="fa fa-thumbs-up"></i>
                                                            {{ number_format($product['rating']['percentage'], 0) }}%
                                                            <span style="color: #7f8c8d;">({{ $product['rating']['count'] ?? 0 }})</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <span class="deal-cta">
                                                Get Deal <i class="fa fa-arrow-right"></i>
                                            </span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@section('scripts')
<script type="text/javascript">
(function() {
    'use strict';
    var dbg = function() {
        try {
            console.log.apply(console, ['[AnnouncementDBG]'].concat([].slice.call(arguments)));
        } catch(e) {}
    };
    @if(isset($announcements))
      var rawAnnouncements = {!! $announcements->values()->toJson(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!};
      var homeAnnouncements = {!! $announcements->where('show_as_popup', 1)->values()->toJson(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) !!};
    @else
      var rawAnnouncements = [];
      var homeAnnouncements = [];
    @endif
    var announcementStyles = document.createElement('style');
    announcementStyles.innerHTML = ".announcement-modal,.announcement-modal-home{z-index:100050}.announcement-modal-home+.modal-backdrop{z-index:100040}";
    document.head.appendChild(announcementStyles);
    @if(isset($announcements) && count($announcements) > 0)
      dbg('Script loaded. Announcements count:', {{ count($announcements) }});
      dbg('jQuery version:', (window.jQuery && jQuery.fn && jQuery.fn.jquery) || 'missing');
      dbg('Bootstrap modal available:', typeof (jQuery && jQuery.fn && jQuery.fn.modal));
      dbg('DOM modal elements found:', $('.announcement-modal').length);
      dbg('Offer overlay present:', $('.offer-popup-overlay').length, 'visible:', $('.offer-popup-overlay.show').length);
      @foreach($announcements as $announcement)
        @if($announcement->show_as_popup == 1)
          (function() {
              var announcementId = {{ $announcement->id }};
              var modalId = 'homeAnnouncementModal' + announcementId;
              $(document).ready(function() {
                  setTimeout(function() {
                      var $modal = $('#' + modalId);
                      dbg('Check modal exists for id:', modalId, 'len:', $modal.length);
                      dbg('Modal initial classes:', $modal.attr('class'));
                      dbg('Modal initial display:', $modal.css('display'));
                      dbg('Bootstrap modal fn:', typeof $modal.modal);
                      if($modal.length) {
                          $modal.addClass('announcement-modal-home').appendTo('body');
                          dbg('Appended modal to body. Classes now:', $modal.attr('class'));
                          dbg('Computed z-index:', window.getComputedStyle($modal[0]).zIndex);
                          dbg('Attempt show via Bootstrap:', typeof $modal.modal === 'function');
                          if (typeof $modal.modal === 'function') {
                              $modal.modal('show');
                              $modal.on('shown.bs.modal', function(){ dbg('Bootstrap shown event fired:', modalId); });
                              $modal.on('hidden.bs.modal', function(){ dbg('Bootstrap hidden event fired:', modalId); });
                          } else {
                              // Fallback if Bootstrap modal plugin is not available/conflicts
                              $modal.addClass('show').css('display', 'block').attr('aria-modal', 'true').removeAttr('aria-hidden');
                              // Create backdrop
                              var $backdrop = $('<div class="modal-backdrop fade show"></div>');
                              $backdrop.insertAfter($modal);
                              dbg('Fallback show applied. Backdrop inserted:', $('.modal-backdrop').length);
                          }
                          // Track view count when shown
                          $.ajax({
                              url: '{{ url("announcement/track-view") }}',
                              type: 'POST',
                              data: {
                                  _token: '{{ csrf_token() }}',
                                  announcement_id: announcementId
                              }
                          }).done(function(){ dbg('View track success:', announcementId); }).fail(function(jq){ dbg('View track failed:', announcementId, jq.status); });
                          $modal.find('.close, [data-dismiss=\"modal\"]').on('click', function() {
                              if (typeof $modal.modal === 'function') {
                                  $modal.modal('hide');
                              } else {
                                  $modal.removeClass('show').css('display', 'none').attr('aria-hidden', 'true').removeAttr('aria-modal');
                                  $('.modal-backdrop').remove();
                                  dbg('Fallback hide: removed backdrop, modal hidden');
                              }
                          });
                      } else {
                          dbg('Modal not found:', modalId);
                      }
                  }, 800);
              });
          })();
        @endif
      @endforeach
    @endif
    // Fallback: if no announcement modal becomes visible, force-show the first one
    $(function(){
      setTimeout(function(){
        var anyVisible = $('.announcement-modal-home.show').length > 0;
        if (!anyVisible) {
          $('.announcement-modal-home').each(function() {
            if ($(this).is(':visible') && $(this).css('display') === 'block') {
              anyVisible = true;
              return false;
            }
          });
        }
        dbg('Visibility check after initial attempts. anyVisible:', anyVisible);
        if (!anyVisible) {
          var $first = $('.announcement-modal').first();
          dbg('Fallback will use first modal. Exists:', $first.length, 'id:', $first.attr('id'));
          if ($first.length) {
            var announcementId = $first.attr('id').replace('homeAnnouncementModal','');
            var $modal = $first.addClass('announcement-modal-home').appendTo('body');
            dbg('Fallback append to body. Classes:', $modal.attr('class'));
            if (typeof $modal.modal === 'function') {
              $modal.modal('show');
              $modal.on('shown.bs.modal', function(){ dbg('Bootstrap shown event fired (fallback):', $first.attr('id')); });
            } else {
              $modal.addClass('show').css('display', 'block').attr('aria-modal', 'true').removeAttr('aria-hidden');
              var $backdrop = $('<div class="modal-backdrop fade show"></div>').css('z-index','100040');
              $('body').addClass('modal-open');
              $backdrop.insertAfter($modal);
              dbg('Fallback show applied. Backdrop count:', $('.modal-backdrop').length);
            }
            $.ajax({
              url: '{{ url("announcement/track-view") }}',
              type: 'POST',
              data: { _token: '{{ csrf_token() }}', announcement_id: announcementId }
            }).done(function(){ dbg('View track success (fallback):', announcementId); }).fail(function(jq){ dbg('View track failed (fallback):', announcementId, jq.status); });
            $modal.find('.close, [data-dismiss="modal"]').on('click', function() {
              if (typeof $modal.modal === 'function') {
                $modal.modal('hide');
              } else {
                $modal.removeClass('show').css('display', 'none').attr('aria-hidden', 'true').removeAttr('aria-modal');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                dbg('Fallback close click: modal hidden, backdrop removed');
              }
            });
          } else if (homeAnnouncements && homeAnnouncements.length > 0) {
            dbg('No static modal found. Building dynamic modal from data. Count:', homeAnnouncements.length);
            var a = homeAnnouncements[0];
            var modalIdDyn = 'homeAnnouncementModal' + a.id;
            var imgHtml = a.image ? '<div class=\"mb-2 text-center\"><img src=\"{{ URL::asset('/') }}'+a.image+'\" alt=\"Announcement\" class=\"img-fluid\"></div>' : '';
            var ctaHtml = (a.cta_text && a.cta_url) ? '<div class=\"mt-2 text-center\"><a href=\"'+a.cta_url+'\" target=\"'+(a.cta_target || '_self')+'\" class=\"btn btn-warning\">'+a.cta_text+'</a></div>' : '';
            var modalHtml = '' +
              '<div class=\"modal fade announcement-modal\" id=\"'+modalIdDyn+'\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"'+modalIdDyn+'Label\" aria-hidden=\"true\">' +
              '  <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">' +
              '    <div class=\"modal-content\">' +
              '      <div class=\"modal-header\">' +
              '        <h5 class=\"modal-title\" id=\"'+modalIdDyn+'Label\"><i class=\"fa fa-bullhorn\"></i> '+a.title+'</h5>' +
              '        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>' +
              '      </div>' +
              '      <div class=\"modal-body\">' + imgHtml + '<p>'+a.message+'</p>' + ctaHtml + '</div>' +
              '    </div>' +
              '  </div>' +
              '</div>';
            $('body').append(modalHtml);
            var $modalDyn = $('#'+modalIdDyn).addClass('announcement-modal-home');
            dbg('Dynamic modal appended. ID:', modalIdDyn, 'classes:', $modalDyn.attr('class'));
            if (typeof $modalDyn.modal === 'function') {
              $modalDyn.modal('show');
              $modalDyn.on('shown.bs.modal', function(){ dbg('Bootstrap shown event fired (dynamic):', modalIdDyn); });
            } else {
              $modalDyn.addClass('show').css('display', 'block').attr('aria-modal', 'true').removeAttr('aria-hidden');
              var $backdropDyn = $('<div class=\"modal-backdrop fade show\"></div>').css('z-index','100040');
              $('body').addClass('modal-open');
              $backdropDyn.insertAfter($modalDyn);
              dbg('Dynamic fallback show applied. Backdrop count:', $('.modal-backdrop').length);
            }
            $.ajax({
              url: '{{ url("announcement/track-view") }}',
              type: 'POST',
              data: { _token: '{{ csrf_token() }}', announcement_id: a.id }
            }).done(function(){ dbg('View track success (dynamic):', a.id); }).fail(function(jq){ dbg('View track failed (dynamic):', a.id, jq.status); });
            $modalDyn.find('.close, [data-dismiss=\"modal\"]').on('click', function() {
              if (typeof $modalDyn.modal === 'function') {
                $modalDyn.modal('hide');
              } else {
                $modalDyn.removeClass('show').css('display', 'none').attr('aria-hidden', 'true').removeAttr('aria-modal');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                dbg('Dynamic close click: modal hidden, backdrop removed');
              }
            });
          } else if (rawAnnouncements && rawAnnouncements.length > 0) {
            dbg('No popup announcements configured. Showing latest active announcement as popup (fallback). Count:', rawAnnouncements.length);
            var a2 = rawAnnouncements[0];
            var modalIdDyn2 = 'homeAnnouncementModal' + a2.id;
            var imgHtml2 = a2.image ? '<div class=\"mb-2 text-center\"><img src=\"{{ URL::asset('/') }}'+a2.image+'\" alt=\"Announcement\" class=\"img-fluid\"></div>' : '';
            var ctaHtml2 = (a2.cta_text && a2.cta_url) ? '<div class=\"mt-2 text-center\"><a href=\"'+a2.cta_url+'\" target=\"'+(a2.cta_target || '_self')+'\" class=\"btn btn-warning\">'+a2.cta_text+'</a></div>' : '';
            var modalHtml2 = '' +
              '<div class=\"modal fade announcement-modal\" id=\"'+modalIdDyn2+'\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"'+modalIdDyn2+'Label\" aria-hidden=\"true\">' +
              '  <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">' +
              '    <div class=\"modal-content\">' +
              '      <div class=\"modal-header\">' +
              '        <h5 class=\"modal-title\" id=\"'+modalIdDyn2+'Label\"><i class=\"fa fa-bullhorn\"></i> '+a2.title+'</h5>' +
              '        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>' +
              '      </div>' +
              '      <div class=\"modal-body\">' + imgHtml2 + '<p>'+a2.message+'</p>' + ctaHtml2 + '</div>' +
              '    </div>' +
              '  </div>' +
              '</div>';
            $('body').append(modalHtml2);
            var $modalDyn2 = $('#'+modalIdDyn2).addClass('announcement-modal-home');
            dbg('Dynamic fallback (non-popup) modal appended. ID:', modalIdDyn2, 'classes:', $modalDyn2.attr('class'));
            if (typeof $modalDyn2.modal === 'function') {
              $modalDyn2.modal('show');
              $modalDyn2.on('shown.bs.modal', function(){ dbg('Bootstrap shown event fired (dynamic non-popup):', modalIdDyn2); });
            } else {
              $modalDyn2.addClass('show').css('display', 'block').attr('aria-modal', 'true').removeAttr('aria-hidden');
              var $backdropDyn2 = $('<div class=\"modal-backdrop fade show\"></div>').css('z-index','100040');
              $('body').addClass('modal-open');
              $backdropDyn2.insertAfter($modalDyn2);
              dbg('Dynamic non-popup fallback show applied. Backdrop count:', $('.modal-backdrop').length);
            }
            $.ajax({
              url: '{{ url("announcement/track-view") }}',
              type: 'POST',
              data: { _token: '{{ csrf_token() }}', announcement_id: a2.id }
            }).done(function(){ dbg('View track success (dynamic non-popup):', a2.id); }).fail(function(jq){ dbg('View track failed (dynamic non-popup):', a2.id, jq.status); });
            $modalDyn2.find('.close, [data-dismiss=\"modal\"]').on('click', function() {
              if (typeof $modalDyn2.modal === 'function') {
                $modalDyn2.modal('hide');
              } else {
                $modalDyn2.removeClass('show').css('display', 'none').attr('aria-hidden', 'true').removeAttr('aria-modal');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                dbg('Dynamic non-popup close click: modal hidden, backdrop removed');
              }
            });
          }
        }
      }, 1200);
    });
})();
</script>
@endsection
    <!-- End Ads Section -->

    @if (getcong('menu_movies'))
        <!-- Start Upcoming Section -->
        @if ($upcoming_movies->count() > 0)

            <!-- Start Movies Video Carousel -->
            <div class="video-carousel-area vfx-item-ptb">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="vfx-item-section">
                                <h3>{{ trans('words.upcoming_movies') }}</h3>
                            </div>
                            <div class="video-carousel owl-carousel">

                                @foreach ($upcoming_movies as $movies_data)
                                    <div class="single-video">
                                        <a href="{{ URL::to('movies/details/' . ($movies_data->video_slug ? $movies_data->video_slug : 'movie') . '/' . $movies_data->id) }}"
                                            title="{{ $movies_data->video_title }}">
                                            <div class="video-img">
                                                @if ($movies_data->video_access == 'Paid')
                                                    <div class="vid-lab-premium">
                                                        <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                            alt="ic-premium" title="Movies">
                                                    </div>
                                                @endif
                                                <span
                                                    class="video-item-content">{{ stripslashes($movies_data->video_title) }}</span>
                                                <img src="{{ URL::to('/' . $movies_data->video_image_thumb) }}"
                                                    alt="{{ $movies_data->video_title }}"
                                                    title="Movies-{{ $movies_data->video_title }}">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- End Latest Movies Video Carousel -->
        @endif

        <!-- End Upcoming Section -->
    @endif

    @if (getcong('menu_movies'))
        <!-- Start Latest Movies Video Section -->
        <div class="view-all-video-area view-movie-list-item vfx-item-ptb">
            <div class="container-fluid">
                <div class="vfx-item-section">
                    <h3>{{ trans('words.latest_movies') }}</h3>
                </div>
                <div class="row" id="movies-container">
                    @include('pages.includes.movies_list')
                </div>

                <!-- Loader -->
                <div id="loader" class="text-center" style="display: none; padding: 20px;">
                    <div class="spinner-border text-light" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="wave-loader">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                </div>

                <div class="text-center" style="margin-bottom: 20px;">
                    <button id="load-more-btn" class="btn btn-primary"
                        style="display: none; background: linear-gradient(135deg, #ff8508, #fd0575); border: none; padding: 10px 30px; border-radius: 25px; font-weight: bold; box-shadow: 0 4px 15px rgba(255, 133, 8, 0.4);">Load
                        More</button>
                </div>

                <div class="col-xs-12" style="display: none;">
                    @include('_particles.pagination', ['paginator' => $movies_list])
                </div>
            </div>
        </div>
        <!-- End Latest Movies Video Section -->
    @endif

<style>
    .today-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: red;
        color: white;
        padding: 5px 10px;
        font-size: 12px;
        border-radius: 5px;
    }

    /* Wave Loader */
    .wave-loader {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 40px;
    }
    .wave-loader span {
        display: inline-block;
        width: 6px;
        height: 100%;
        background: linear-gradient(45deg, #ff0015, #ff8c00);
        animation: wave 1s infinite ease-in-out;
        border-radius: 10px;
    }
    .wave-loader span:nth-child(1) { animation-delay: 0s; }
    .wave-loader span:nth-child(2) { animation-delay: 0.1s; }
    .wave-loader span:nth-child(3) { animation-delay: 0.2s; }
    .wave-loader span:nth-child(4) { animation-delay: 0.3s; }
    .wave-loader span:nth-child(5) { animation-delay: 0.4s; }

    @keyframes wave {
        0%, 100% { transform: scaleY(0.4); }
        50% { transform: scaleY(1); }
    }
</style>



    @foreach ($home_sections as $sections_data)

        {{-- @if (getcong('menu_movies'))
            @if ($sections_data->post_type == 'Movie')

                <!-- Start Movies Video Carousel -->
                <div class="video-carousel-area vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>
                                <div class="video-carousel owl-carousel">

                                    @foreach (explode(',', $sections_data->movie_ids) as $movie_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('movies/details/' . (App\Movies::getMoviesInfo($movie_data, 'video_slug') ?: 'movie') . '/' . App\Movies::getMoviesInfo($movie_data, 'id')) }}"
                                                title="{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}">
                                                <div class="video-img">
                                                    @if (App\Movies::getMoviesInfo($movie_data, 'video_access') == 'Paid')
                                                        <div class="vid-lab-premium">
                                                            <img src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Movies-ic-premium">
                                                        </div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ stripslashes(App\Movies::getMoviesInfo($movie_data, 'video_title')) }}</span>
                                                    <img src="{{ URL::to('/' . App\Movies::getMoviesInfo($movie_data, 'video_image_thumb')) }}"
                                                        alt="{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}"
                                                        title="Movies-{{ App\Movies::getMoviesInfo($movie_data, 'video_title') }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Movies Video Carousel -->
            @endif
        @endif --}}

        @if (getcong('menu_shows'))
            @if ($sections_data->post_type == 'Shows')
                <!-- Start Latest Shows Video Section -->
                <div class="video-shows-section vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>
                                <div class="video-shows-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->show_ids) as $show_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('shows/details/' . (App\Series::getSeriesInfo($show_data, 'series_slug') ?: 'show') . '/' . $show_data) }}"
                                                title="{{ App\Series::getSeriesInfo($show_data, 'series_name') }}">
                                                <div class="video-img">
                                                    @if (App\Series::getSeriesInfo($show_data, 'series_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Shows-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ stripslashes(App\Series::getSeriesInfo($show_data, 'series_name')) }}</span>
                                                    <img src="{{ URL::to('/' . App\Series::getSeriesInfo($show_data, 'series_poster')) }}"
                                                        alt="{{ App\Series::getSeriesInfo($show_data, 'series_name') }}"
                                                        title="Shows-{{ App\Series::getSeriesInfo($show_data, 'series_name') }}">
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Latest Shows Video Section -->
            @endif
        @endif


        @if (getcong('menu_sports'))
            @if ($sections_data->post_type == 'Sports')
                <!-- Start Sports Video Section -->
                <div class="video-shows-section sport-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->sport_ids) as $sport_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('sports/details/' . (App\Sports::getSportsInfo($sport_data, 'video_slug') ?: 'sport') . '/' . $sport_data) }}"
                                                title="{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}">
                                                <div class="video-img">
                                                    @if (App\Sports::getSportsInfo($sport_data, 'video_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="Sports-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}</span>
                                                    <img src="{{ URL::to('/' . App\Sports::getSportsInfo($sport_data, 'video_image')) }}"
                                                        alt="{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}"
                                                        title="Sports-{{ App\Sports::getSportsInfo($sport_data, 'video_title') }}" />
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Sports Section -->
            @endif
        @endif


        @if (getcong('menu_livetv'))
            @if ($sections_data->post_type == 'LiveTV')
                <!-- Start Live TV Video Section -->
                <div class="video-shows-section live-tv-video-block vfx-item-ptb">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="vfx-item-section">
                                    <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                        title="{{ $sections_data->section_name }}">
                                        <h3>{{ $sections_data->section_name }}</h3>
                                    </a>
                                    <span class="view-more">
                                        <a href="{{ URL::to('collections/' . $sections_data->section_slug . '/' . $sections_data->id) }}"
                                            title="view-more">{{ trans('words.view_all') }}</a>
                                    </span>
                                </div>

                                <div class="tv-season-video-carousel owl-carousel">
                                    @foreach (explode(',', $sections_data->tv_ids) as $tv_data)
                                        <div class="single-video">
                                            <a href="{{ URL::to('livetv/details/' . (App\LiveTV::getLiveTvInfo($tv_data, 'channel_slug') ?: 'channel') . '/' . $tv_data) }}"
                                                title="{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}">
                                                <div class="video-img">
                                                    @if (App\LiveTV::getLiveTvInfo($tv_data, 'channel_access') == 'Paid')
                                                        <div class="vid-lab-premium"><img
                                                                src="{{ URL::asset('site_assets/images/ic-premium.png') }}"
                                                                alt="ic-premium" title="LiveTV-ic-premium"></div>
                                                    @endif
                                                    <span
                                                        class="video-item-content">{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}</span>
                                                    <img src="{{ URL::to('/' . App\LiveTV::getLiveTvInfo($tv_data, 'channel_thumb')) }}"
                                                        alt="{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}"
                                                        title="LiveTV-{{ App\LiveTV::getLiveTvInfo($tv_data, 'channel_name') }}" />
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Live TV Section -->
            @endif
        @endif

    @endforeach

    <!-- Banner -->
    @if (get_web_banner('home_bottom') != '')
        <div class="vid-item-ptb banner_ads_item pb-1" style="padding: 15px 0;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        {!! stripslashes(get_web_banner('home_bottom')) !!}
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Offer Popup Modal -->
    @if (count($ads_products) > 0)
        <style>
            .offer-popup-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.85);
                z-index: 99999;
                display: none;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.3s ease;
            }

            .offer-popup-overlay.show {
                display: flex;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            @keyframes slideUp {
                from {
                    transform: translateY(50px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .offer-popup {
                background: linear-gradient(135deg, #1e272e 0%, #2c3e50 100%);
                border: 2px solid #ff8508;
                border-radius: 20px;
                max-width: 550px;
                width: 90%;
                padding: 30px;
                position: relative;
                box-shadow: 0 20px 60px rgba(255, 133, 8, 0.4);
                animation: slideUp 0.4s ease;
            }

            .popup-close {
                position: absolute;
                top: 15px;
                right: 15px;
                background: rgba(231, 76, 60, 0.2);
                border: 1px solid #e74c3c;
                color: #e74c3c;
                width: 35px;
                height: 35px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 18px;
            }

            .popup-close:hover {
                background: #e74c3c;
                color: #fff;
                transform: rotate(90deg);
            }

            .popup-header {
                text-align: center;
                margin-bottom: 25px;
            }

            .popup-header h2 {
                font-size: 24px;
                font-weight: 900;
                color: #fff;
                margin: 0 0 10px 0;
                text-transform: uppercase;
            }

            .popup-header p {
                font-size: 14px;
                color: #bdc3c7;
                margin: 0;
            }

            .popup-deal {
                background: rgba(0, 0, 0, 0.3);
                border: 1px solid #34495e;
                border-radius: 15px;
                padding: 20px;
                display: flex;
                gap: 18px;
                margin-bottom: 25px;
            }

            .popup-deal-icon {
                width: 100px;
                height: 120px;
                border-radius: 10px;
                object-fit: cover;
                border: 2px solid #445566;
                flex-shrink: 0;
            }

            .popup-deal-content {
                flex: 1;
                min-width: 0;
            }

            .popup-deal-title {
                font-size: 15px;
                font-weight: 700;
                color: #fff;
                line-height: 1.4;
                margin: 0 0 10px 0;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .popup-deal-price {
                font-size: 26px;
                font-weight: 900;
                color: #1abc9c;
                margin-bottom: 10px;
                text-shadow: 0 2px 10px rgba(26, 188, 156, 0.4);
            }

            .popup-deal-badges {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
                margin-bottom: 10px;
            }

            .popup-badge {
                background: rgba(52, 152, 219, 0.2);
                border: 1px solid rgba(52, 152, 219, 0.4);
                color: #5dade2;
                font-size: 10px;
                padding: 3px 8px;
                border-radius: 5px;
                font-weight: 600;
            }

            .popup-badge.green {
                background: rgba(46, 204, 113, 0.2);
                border-color: rgba(46, 204, 113, 0.4);
                color: #58d68d;
            }

            .popup-deal-rating {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: rgba(52, 152, 219, 0.15);
                padding: 4px 10px;
                border-radius: 15px;
                font-size: 12px;
                font-weight: 700;
                color: #3498db;
            }

            .popup-actions {
                display: flex;
                gap: 12px;
                margin-top: 20px;
            }

            .popup-btn {
                flex: 1;
                padding: 14px 20px;
                border-radius: 30px;
                font-size: 14px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .popup-btn-primary {
                background: linear-gradient(135deg, #ff8508, #fd0575);
                color: #fff;
                border: none;
                box-shadow: 0 5px 20px rgba(255, 133, 8, 0.4);
            }

            .popup-btn-primary:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(255, 133, 8, 0.6);
                color: #fff;
                text-decoration: none;
            }

            .popup-btn-secondary {
                background: transparent;
                color: #bdc3c7;
                border: 2px solid #34495e;
            }

            .popup-btn-secondary:hover {
                border-color: #3498db;
                color: #3498db;
                text-decoration: none;
            }

            .popup-view-all {
                display: block;
                text-align: center;
                color: #3498db;
                font-size: 13px;
                font-weight: 600;
                margin-top: 15px;
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .popup-view-all:hover {
                color: #5dade2;
                text-decoration: none;
            }

            @media (max-width: 768px) {
                .offer-popup {
                    max-width: 95%;
                    padding: 15px;
                    margin: 10px;
                }

                .popup-header h2 {
                    font-size: 18px;
                }

                .popup-header p {
                    font-size: 12px;
                }

                .popup-deal {
                    padding: 12px;
                    gap: 12px;
                }

                .popup-deal-icon {
                    width: 70px;
                    height: 85px;
                }

                .popup-deal-title {
                    font-size: 13px;
                    -webkit-line-clamp: 2;
                }

                .popup-deal-price {
                    font-size: 20px;
                }

                .popup-deal-badges {
                    gap: 4px;
                }

                .popup-badge {
                    font-size: 9px;
                    padding: 2px 6px;
                }

                .popup-deal-rating {
                    font-size: 10px;
                    padding: 3px 8px;
                }

                .popup-actions {
                    gap: 8px;
                }

                .popup-btn {
                    padding: 10px 15px;
                    font-size: 12px;
                }

                .popup-view-all {
                    font-size: 11px;
                    margin-top: 10px;
                }

                .popup-close {
                    width: 30px;
                    height: 30px;
                    font-size: 14px;
                }
            }
        </style>

        <div class="offer-popup-overlay" id="offerPopup">
            <div class="offer-popup">
                <div class="popup-close" onclick="closeOfferPopup()">
                    <i class="fa fa-times"></i>
                </div>

                <div class="popup-header">
                    <h2>
                        <i class="fa fa-fire" style="color: #e74c3c; animation: pulse 2s infinite;"></i>
                        Exclusive Deal!
                    </h2>
                    <p>Limited time offer - Don't miss out!</p>
                </div>

                <div class="popup-deal" id="popupDealContent">
                    <!-- Dynamic content will be loaded here -->
                </div>

                <div class="popup-actions">
                    <button class="popup-btn popup-btn-secondary" onclick="closeOfferPopup()">
                        <i class="fa fa-clock-o"></i> Later
                    </button>
                    <a href="#" class="popup-btn popup-btn-primary" id="popupGetOfferBtn">
                        <i class="fa fa-shopping-cart"></i> Get Offer
                    </a>
                </div>

                <a href="{{ URL::to('offers') }}" class="popup-view-all">
                    <i class="fa fa-th"></i> View All Offers
                </a>
            </div>
        </div>

        <script>
            // Offers data from server
            const offersData = @json($ads_products);

            function showOfferPopup() {
                if (offersData.length === 0) return;

                // Get a random offer
                const randomOffer = offersData[Math.floor(Math.random() * offersData.length)];

                // Build popup content
                let badges = '';
                if (randomOffer.badges?.duration) {
                    badges += `<span class="popup-badge"><i class="fa fa-clock-o"></i> ${randomOffer.badges.duration}</span>`;
                }
                if (randomOffer.badges?.delivery_speed) {
                    badges += `<span class="popup-badge green"><i class="fa fa-truck"></i> ${randomOffer.badges.delivery_speed}</span>`;
                }

                let hotBadge = '';
                if (randomOffer.badges?.is_trending) {
                    hotBadge = '<span style="position: absolute; top: 10px; left: 10px; background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff; font-size: 10px; font-weight: 700; padding: 4px 8px; border-radius: 5px; animation: pulse 2s infinite;"><i class="fa fa-fire"></i> HOT</span>';
                } else if (randomOffer.badges?.is_flash) {
                    hotBadge = '<span style="position: absolute; top: 10px; left: 10px; background: linear-gradient(135deg, #f39c12, #e67e22); color: #fff; font-size: 10px; font-weight: 700; padding: 4px 8px; border-radius: 5px; animation: flash 1.5s infinite;"><i class="fa fa-bolt"></i> FLASH</span>';
                }

                const popupContent = `
                    <div style="position: relative;">
                        ${hotBadge}
                        <img src="${randomOffer.thumbnail_url || ''}"
                             alt="${randomOffer.title || 'Product'}"
                             class="popup-deal-icon"
                             onerror="this.src='{{ URL::asset('site_assets/images/video-placeholder.jpg') }}'">
                    </div>
                    <div class="popup-deal-content">
                        <h3 class="popup-deal-title">${randomOffer.title || 'No Title'}</h3>
                        <div class="popup-deal-price">
                            ${randomOffer.currency_symbol || '$'}${parseFloat(randomOffer.final_price || 0).toFixed(2)}
                        </div>
                        <div class="popup-deal-badges">
                            ${badges}
                        </div>
                        ${randomOffer.rating?.percentage > 0 ? `
                        <div class="popup-deal-rating">
                            <i class="fa fa-thumbs-up"></i>
                            ${Math.round(randomOffer.rating.percentage)}%
                            <span style="color: #7f8c8d; font-weight: 400;">(${randomOffer.rating.count || 0})</span>
                        </div>
                        ` : ''}
                    </div>
                `;

                document.getElementById('popupDealContent').innerHTML = popupContent;
                document.getElementById('popupGetOfferBtn').onclick = function() {
                    trackAdClick(randomOffer.id, randomOffer.url || '#');
                    closeOfferPopup();
                };

                document.getElementById('offerPopup').classList.add('show');
            }

            function closeOfferPopup() {
                document.getElementById('offerPopup').classList.remove('show');
                // Save last shown time
                localStorage.setItem('lastOfferPopupTime', Date.now());
            }

            // Check and show popup based on time
            function checkAndShowOfferPopup() {
                const lastShown = localStorage.getItem('lastOfferPopupTime');
                const currentTime = Date.now();
                const minInterval = 20 * 60 * 1000; // 20 minutes in milliseconds
                const maxInterval = 30 * 60 * 1000; // 30 minutes in milliseconds

                // Random interval between 20-30 minutes
                const randomInterval = Math.floor(Math.random() * (maxInterval - minInterval + 1)) + minInterval;

                if (!lastShown || (currentTime - parseInt(lastShown)) > randomInterval) {
                    // Avoid conflict: wait until announcement modal is closed
                    function isAnnouncementOpen() {
                        var open = $('.announcement-modal-home.show').length > 0;
                        if (!open) {
                            $('.announcement-modal-home').each(function() {
                                if ($(this).is(':visible') && $(this).css('display') === 'block') {
                                    open = true;
                                    return false;
                                }
                            });
                        }
                        return open;
                    }
                    setTimeout(function scheduleOffer() {
                        if (isAnnouncementOpen()) {
                            setTimeout(scheduleOffer, 2000);
                        } else {
                            showOfferPopup();
                        }
                    }, 5000);
                }
            }

            // Run on page load
            checkAndShowOfferPopup();

            // Close popup when clicking overlay
            document.getElementById('offerPopup').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeOfferPopup();
                }
            });
        </script>
    @endif


@endsection

@section('scripts')
<script type="text/javascript">
    var page = 1;
    var lastPage = {{ $movies_list->lastPage() }};
    var loading = false;

    // Initial button state
    if (page < lastPage) {
        $('#load-more-btn').show();
    }

    $('#load-more-btn').click(function() {
        if (page < lastPage && !loading) {
            loading = true;
            page++;
            loadMoreData(page);
        }
    });

    function loadMoreData(page){
        $('#load-more-btn').hide();
        $('#loader').show();
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                beforeSend: function()
                {
                    // $('#loader').show();
                }
            })
            .done(function(data)
            {
                $('#loader').hide();
                if(data.html == " " || data.html == ""){
                    // $('.ajax-load').html("No more records found");
                    return;
                }

                $("#movies-container").append(data);
                loading = false;

                if (page < lastPage) {
                    $('#load-more-btn').show();
                } else {
                    $('#load-more-btn').hide();
                }
            })
            .fail(function(jqXHR, ajaxOptions, thrownError)
            {
                  // alert('server not responding...');
                  $('#loader').hide();
                  loading = false;
                  $('#load-more-btn').show(); // Allow retry
            });
    }
</script>
@endsection
