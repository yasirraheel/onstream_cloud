@extends("admin.admin_app")

@section("content")

<div class="content-page">
      <div class="content">
        <div class="container-fluid">
          
          <div class="row">
            <div class="col-12">
              <div class="card-box table-responsive">

                <div class="row">
                  <div class="col-md-6">
                    <h4 class="m-t-0 header-title">{{ $page_title }}</h4>
                  </div>
                </div>
                <br>

                <div class="row">
                    
                    <!-- Total Views -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-custom" data-plugin="counterup">{{ $total_views }}</h2>
                                <h5 style="color: #f9f9f9;">Total Views</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Views -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-pink" data-plugin="counterup">N/A</h2>
                                <h5 style="color: #f9f9f9;">Today's Views *</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Yesterday's Views -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-warning" data-plugin="counterup">N/A</h2>
                                <h5 style="color: #f9f9f9;">Yesterday's Views *</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Last 30 Days Views -->
                    <div class="col-xl-3 col-md-6">
                        <div class="card-box widget-user">
                            <div class="text-center">
                                <h2 class="text-success" data-plugin="counterup">N/A</h2>
                                <h5 style="color: #f9f9f9;">Last 30 Days Views *</h5>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <p class="text-muted">* Time-based analytics (Today, Yesterday, Last 30 Days) require timestamp data which is currently not available in the history.</p>
                    </div>
                </div>

                <div class="row">
                    
                    <!-- Top 10 Videos -->
                    <div class="col-lg-6">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30">Top 10 Most Viewed Videos</h4>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Video Title</th>
                                        <th>Type</th>
                                        <th>Views</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($top_videos as $key => $video)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $video->title }}</td>
                                        <td>{{ $video->video_type }}</td>
                                        <td>{{ $video->total }}</td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Top 10 Countries -->
                    <div class="col-lg-6">
                        <div class="card-box">
                            <h4 class="header-title m-t-0 m-b-30">Top 10 Countries</h4>

                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Country</th>
                                        <th>Views/Searches</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($top_countries as $key => $country)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $country->country }}</td>
                                        <td>{{ $country->total }}</td>
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
      </div>
</div>

@endsection
