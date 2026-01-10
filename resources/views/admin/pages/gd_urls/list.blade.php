@extends("admin.admin_app")

@section("content")

<div class="content-page">
      <div class="content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                @if(Session::has('flash_message'))
                  <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    {{ Session::get('flash_message') }}
                  </div>
                @endif

                @if(Session::has('error_flash_message'))
                  <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                    {{ Session::get('error_flash_message') }}
                  </div>
                @endif

                <div class="row">
                  <div class="col-md-6">
                    <h4 class="m-t-0 header-title">{{ $page_title }}</h4>
                    @if($last_fetch)
                      <p class="text-muted"><i class="fa fa-clock-o"></i> Last Sync: {{ \Carbon\Carbon::parse($last_fetch)->diffForHumans() }} ({{ \Carbon\Carbon::parse($last_fetch)->format('M d, Y h:i A') }})</p>
                    @else
                      <p class="text-muted"><i class="fa fa-clock-o"></i> Last Sync: Never</p>
                    @endif
                  </div>
                  <div class="col-md-6 text-right">
                    <a href="{{ URL::to('admin/gd_urls/fetch') }}" class="btn btn-success btn-md waves-effect waves-light m-b-20" onclick="return confirm('This will fetch latest files from Google Drive. Continue?')">
                        <i class="fa fa-refresh"></i> Fetch/Sync Files from Google Drive
                    </a>
                  </div>
                </div>
                <br>

                <div class="row">
                    <!-- Summary Stats -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user bg-primary text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $total_urls }}</h2>
                                <h5>Total Files</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user bg-success text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $available_urls_count }}</h2>
                                <h5>Available Files</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user bg-danger text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $used_urls_count }}</h2>
                                <h5>Used Files</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user bg-info text-white">
                            <div class="text-center">
                                <h2 class="text-white">{{ $total_size_gb }} GB</h2>
                                <h5>Total Storage</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12 col-md-12">
                        <div class="card-box widget-user bg-purple text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $total_folders }}</h2>
                                <h5>Total Folders Synced</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <strong>Note:</strong> Used files are displayed at the bottom of the list.
                        </div>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>File Name</th>
                                    <th>Folder ID</th>
                                    <th>URL</th>
                                    <th>File Size</th>
                                    <th>MIME Type</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gd_urls as $i => $url_data)
                                <tr style="{{ $url_data->is_used ? 'background-color: #5a2a2a; color: #ffcccc;' : '' }}" id="row-{{ $url_data->id }}">
                                    <td style="{{ $url_data->is_used ? 'color: #ffcccc;' : '' }}">{{ $i+1 }}</td>
                                    <td style="{{ $url_data->is_used ? 'color: #ffcccc;' : '' }}">{{ $url_data->file_name }}</td>
                                    <td><span class="badge badge-info">{{ $url_data->folder_id ?? 'N/A' }}</span></td>
                                    <td>
                                        <input type="text" value="{{ $url_data->url }}" class="form-control" readonly style="background: transparent; border: none; width: 100%; color: {{ $url_data->is_used ? '#ffcccc' : 'inherit' }};">
                                    </td>
                                    <td style="{{ $url_data->is_used ? 'color: #ffcccc;' : '' }}">{{ $url_data->file_size ? number_format($url_data->file_size / 1048576, 2) . ' MB' : 'N/A' }}</td>
                                    <td style="{{ $url_data->is_used ? 'color: #ffcccc;' : '' }}">{{ $url_data->mime_type ?? 'N/A' }}</td>
                                    <td>
                                        @if($url_data->is_used)
                                            <span class="badge badge-danger">Used</span>
                                        @else
                                            <span class="badge badge-success">Available</span>
                                        @endif
                                    </td>
                                    <td style="{{ $url_data->is_used ? 'color: #ffcccc;' : '' }}">{{ $url_data->updated_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary search-video-btn" data-id="{{ $url_data->id }}" data-filename="{{ $url_data->file_name }}">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </td>
                                </tr>
                                <tr id="results-{{ $url_data->id }}" style="display: none;">
                                    <td colspan="9">
                                        <div class="search-results-container" style="padding: 15px; background: #2c3e50; color: #ffffff;">
                                            <h5 style="color: #ffffff;">Search Results:</h5>
                                            <div id="results-content-{{ $url_data->id }}">
                                                <!-- Results will be loaded here -->
                                            </div>
                                        </div>
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
      </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.search-video-btn').on('click', function() {
        var btn = $(this);
        var gdUrlId = btn.data('id');
        var fileName = btn.data('filename');

        // Show loading state
        btn.prop('disabled', true);
        btn.html('<i class="fa fa-spinner fa-spin"></i> Searching...');

        // Show results row
        $('#results-' + gdUrlId).show();
        $('#results-content-' + gdUrlId).html('<p style="color: #b0bec5;"><i class="fa fa-spinner fa-spin"></i> Searching for matching videos...</p>');

        // Make AJAX request
        $.ajax({
            url: '{{ url("admin/gd_urls/search-video") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                file_name: fileName,
                gd_url_id: gdUrlId
            },
            success: function(response) {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-search"></i> Search');

                if (response.success) {
                    if (response.results.length > 0) {
                        var html = '<div class="table-responsive"><table class="table table-sm" style="background-color: #34495e; color: #ffffff; margin-bottom: 0;">';
                        html += '<thead style="background-color: #2c3e50; color: #ffffff;"><tr>';
                        html += '<th style="color: #ffffff; border-color: #445566;">#</th>';
                        html += '<th style="color: #ffffff; border-color: #445566;">Video Title</th>';
                        html += '<th style="color: #ffffff; border-color: #445566;">Release Date</th>';
                        html += '<th style="color: #ffffff; border-color: #445566;">Duration</th>';
                        html += '<th style="color: #ffffff; border-color: #445566;">Type</th>';
                        html += '<th style="color: #ffffff; border-color: #445566;">Action</th>';
                        html += '</tr></thead>';
                        html += '<tbody>';

                        $.each(response.results, function(index, video) {
                            var releaseDate = video.release_date ? new Date(video.release_date * 1000).toLocaleDateString() : 'N/A';
                            var rowBg = (index % 2 === 0) ? '#34495e' : '#3d566e';
                            html += '<tr style="background-color: ' + rowBg + ';">';
                            html += '<td style="color: #ffffff; border-color: #445566;">' + (index + 1) + '</td>';
                            html += '<td style="color: #ffffff; border-color: #445566;">' + video.video_title + '</td>';
                            html += '<td style="color: #ffffff; border-color: #445566;">' + releaseDate + '</td>';
                            html += '<td style="color: #ffffff; border-color: #445566;">' + (video.duration || 'N/A') + '</td>';
                            html += '<td style="border-color: #445566;"><span class="badge badge-info">' + (video.video_type || 'N/A') + '</span></td>';
                            html += '<td style="border-color: #445566;">';
                            html += '<a href="{{ url("admin/movies/edit") }}/' + video.id + '" class="btn btn-xs btn-info" target="_blank" style="margin-right: 5px;"><i class="fa fa-edit"></i> View/Edit</a>';
                            html += '<button class="btn btn-xs btn-success insert-url-btn" data-video-id="' + video.id + '" data-gd-url-id="' + gdUrlId + '"><i class="fa fa-check"></i> Insert URL</button>';
                            html += '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                        html += '<p style="color: #4caf50; margin-top: 10px;"><strong>' + response.results.length + ' matching video(s) found</strong></p>';

                        $('#results-content-' + gdUrlId).html(html);
                    } else {
                        $('#results-content-' + gdUrlId).html('<p style="color: #ffc107;"><i class="fa fa-exclamation-circle"></i> No matching videos found for "' + fileName + '"</p>');
                    }
                } else {
                    $('#results-content-' + gdUrlId).html('<p style="color: #f44336;"><i class="fa fa-times-circle"></i> ' + response.message + '</p>');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-search"></i> Search');
                $('#results-content-' + gdUrlId).html('<p style="color: #f44336;"><i class="fa fa-times-circle"></i> An error occurred while searching. Please try again.</p>');
            }
        });
    });

    // Handle Insert URL button click (using event delegation for dynamically created buttons)
    $(document).on('click', '.insert-url-btn', function() {
        var btn = $(this);
        var videoId = btn.data('video-id');
        var gdUrlId = btn.data('gd-url-id');

        if (!confirm('Are you sure you want to insert this GD URL into the movie? This will update the video type to Embed and replace the existing video URL.')) {
            return;
        }

        // Show loading state
        btn.prop('disabled', true);
        btn.html('<i class="fa fa-spinner fa-spin"></i> Inserting...');

        // Make AJAX request
        $.ajax({
            url: '{{ url("admin/gd_urls/insert-url") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                video_id: videoId,
                gd_url_id: gdUrlId
            },
            success: function(response) {
                if (response.success) {
                    // Reload page to show flash message and update status
                    location.reload();
                } else {
                    btn.prop('disabled', false);
                    btn.html('<i class="fa fa-check"></i> Insert URL');
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-check"></i> Insert URL');
                alert('An error occurred while inserting the URL. Please try again.');
            }
        });
    });
});
</script>

@endsection
