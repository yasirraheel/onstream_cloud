<?php
    $video_url = $movies_info->video_url;
    $extension = pathinfo(parse_url($video_url, PHP_URL_PATH), PATHINFO_EXTENSION);
?>

@if(strtolower($extension) == 'mkv')
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />

    <!-- Responsive container 16:9 -->
    <div style="width: 100%; position: relative; padding-top: 56.25%; background: #000;">
        <video id="my-video" class="video-js vjs-big-play-centered vjs-fill" controls preload="auto"
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
            poster="{{URL::to('/'.$movies_info->video_image)}}" data-setup='{"fluid": true}'>
            <!-- Try multiple types to maximize compatibility -->
            <source src="{{$video_url}}" type="video/mp4" />
            <source src="{{$video_url}}" type="video/webm" />
            <source src="{{$video_url}}" type="video/x-matroska" />
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a web browser that supports HTML5 video.
            </p>
        </video>
    </div>

    <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
@else
    <div id="viavi_player" style="margin:auto;"></div>

    <!-- Setup EVP -->
    <script type="text/javascript">
        FWDEVPUtils.onReady(function(){

            FWDEVPlayer.videoStartBehaviour = "pause";

            new FWDEVPlayer({
                //main settings
                instanceName:"player1",
                parentId:"viavi_player",
                mainFolderPath:"{{URL::asset('/site_assets/player/content/')}}",
                initializeOnlyWhenVisible:"no",
                skinPath:"{{ get_player_cong('player_style') }}",
                displayType:"responsive",
                autoScale:"yes",
                fillEntireVideoScreen:"no",
                playsinline:"yes",
                useWithoutVideoScreen:"no",
                openDownloadLinkOnMobile:"no",
                googleAnalyticsMeasurementId:"",
                useVectorIcons:"{{get_player_cong('player_vector_icons')}}",
                useResumeOnPlay:"yes",
                goFullScreenOnButtonPlay:"no",
                useHEXColorsForSkin:"no",
                normalHEXButtonsColor:"#FF0000",
                privateVideoPassword:"428c841430ea18a70f7b06525d4b748a",
                startAtVideoSource:0,
                startAtTime:"",
                stopAtTime:"",
                videoSource:[
                    {source:"encrypt:{{base64_encode($movies_info->video_url)}}", label:"default"},


                    @if($movies_info->video_quality)
                    @if($movies_info->video_url_480)
                    {source:"encrypt:{{base64_encode($movies_info->video_url_480)}}", label:"480"},
                    @endif

                    @if($movies_info->video_url_720)
                    {source:"encrypt:{{base64_encode($movies_info->video_url_720)}}", label:"720"},
                    @endif

                    @if($movies_info->video_url_1080)
                    {source:"encrypt:{{base64_encode($movies_info->video_url_1080)}}", label:"1080"}
                    @endif
                    @endif
                ],
                posterPath:"{{URL::to('/'.$movies_info->video_image)}}",
                showErrorInfo:"yes",
                fillEntireScreenWithPoster:"no",
                disableDoubleClickFullscreen:"no",
                useChromeless:"no",
                showPreloader:"yes",
                preloaderColors:["#999999", "#FFFFFF"],
                addKeyboardSupport:"yes",
                autoPlay:"{{get_player_cong('autoplay')}}",
                autoPlayText:"Click to Unmute",
                loop:"yes",
                scrubAtTimeAtFirstPlay:"00:00:00",
                maxWidth:1325,
                maxHeight:535,
                volume:.8,
                greenScreenTolerance:200,
                backgroundColor:"#000000",
                posterBackgroundColor:"#000000",
                //lightbox settings
                closeLightBoxWhenPlayComplete:"no",
                lightBoxBackgroundOpacity:.6,
                lightBoxBackgroundColor:"#000000",
                //logo settings
                logoSource:"{{ get_player_cong('player_logo')? URL::asset('/'.get_player_cong('player_logo')) : URL::asset('/'.getcong('site_logo')) }}",
                showLogo:"{{get_player_cong('player_watermark')}}",
                hideLogoWithController:"yes",
                logoPosition:"{{get_player_cong('player_logo_position')}}",
                logoLink:"{{get_player_cong('player_url')}}",
                logoMargins:5,
                //controller settings
                showController:"yes",
                showDefaultControllerForVimeo:"yes",
                showScrubberWhenControllerIsHidden:"yes",
                showControllerWhenVideoIsStopped:"yes",
                showVolumeScrubber:"yes",
                showVolumeButton:"yes",
                showTime:"yes",
                showAudioTracksButton:"yes",
                showRewindButton:"{{get_player_cong('rewind_forward')}}",
                showQualityButton:"yes",
                showShareButton:"no",
                showEmbedButton:"no",
                showDownloadButton:"no",
                showMainScrubberToolTipLabel:"yes",
                showChromecastButton:"yes",
                how360DegreeVideoVrButton:"no",
                showFullScreenButton:"yes",
                repeatBackground:"no",
                controllerHeight:43,
                controllerHideDelay:3,
                startSpaceBetweenButtons:11,
                spaceBetweenButtons:11,
                mainScrubberOffestTop:15,
                scrubbersOffsetWidth:2,
                timeOffsetLeftWidth:1,
                timeOffsetRightWidth:2,
                volumeScrubberWidth:80,
                volumeScrubberOffsetRightWidth:0,
                timeColor:"#bdbdbd",
                showYoutubeRelAndInfo:"no",
                youtubeQualityButtonNormalColor:"#888888",
                youtubeQualityButtonSelectedColor:"#FFFFFF",
                scrubbersToolTipLabelBackgroundColor:"#FFFFFF",
                scrubbersToolTipLabelFontColor:"#5a5a5a",
                //subtitle settings
                showSubtitleButton:"yes",
                startAtSubtitle:0,
                subtitlesSource:[
                @if($movies_info->subtitle_on_off)
                    @if($movies_info->subtitle_url1)
                    {subtitlePath:"{{$movies_info->subtitle_url1}}", subtitleLabel:"{{$movies_info->subtitle_language1}}", isDefault:"no"},
                    @endif
                    @if($movies_info->subtitle_url2)
                    {subtitlePath:"{{$movies_info->subtitle_url2}}", subtitleLabel:"{{$movies_info->subtitle_language2}}", isDefault:"no"},
                    @endif
                    @if($movies_info->subtitle_url3)
                    {subtitlePath:"{{$movies_info->subtitle_url3}}", subtitleLabel:"{{$movies_info->subtitle_language3}}", isDefault:"no"},
                    @endif
                @endif
                ]
            });
        });
    </script>
@endif
