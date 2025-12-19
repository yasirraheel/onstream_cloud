<div class="embed-responsive embed-responsive-16by9 text-center video-player" style="margin: 0 auto;">
    {!! $movies_info->video_url !!}
</div>

<style>
.video-player {
    position: relative;
    width: 70%; /* Default width for most screens */
    max-width: 600px; /* Slightly larger for larger screens */
    margin: 0 auto;
    padding-bottom: 35%; /* Adjust aspect ratio for a cleaner look */
    height: 0;
    overflow: hidden;
}

.video-player iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

@media (max-width: 768px) {
    .video-player {
        width: 100%; /* Normal size for mobile devices */
        max-width: 100%; /* Ensure it doesn’t exceed the screen size */
        padding-bottom: 56.25%; /* Standard 16:9 ratio for mobile screens */
    }
}
</style>
