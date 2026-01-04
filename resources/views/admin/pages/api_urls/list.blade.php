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
                  <div class="col-md-6 text-right">
                    <a href="{{ URL::to('admin/api_urls/delete_all') }}" class="btn btn-danger btn-md waves-effect waves-light m-b-20 m-r-10" onclick="return confirm('{{trans('words.dlt_warning_text')}}')">
                        <i class="fa fa-trash"></i> Delete All
                    </a>
                    <a href="{{ URL::to('admin/api_urls/fetch') }}" class="btn btn-success btn-md waves-effect waves-light m-b-20" onclick="return confirm('This will fetch latest URLs from API. Continue?')">
                        <i class="fa fa-refresh"></i> Fetch/Sync URLs from API
                    </a>
                  </div>
                </div>
                <br>

                <div class="row">
                    <!-- Summary Stats -->
                    <div class="col-xl-4 col-md-6">
                        <div class="card-box widget-user bg-primary text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $total_urls }}</h2>
                                <h5>Total URLs</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <div class="card-box widget-user bg-success text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $available_urls_count }}</h2>
                                <h5>Available URLs</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <div class="card-box widget-user bg-danger text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $used_urls_count }}</h2>
                                <h5>Used URLs</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <strong>Note:</strong> Used URLs are displayed at the bottom of the list.
                        </div>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Movie Name</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($api_urls as $i => $url_data)
                                <tr style="{{ $url_data->is_used ? 'background-color: #f8d7da; color: #721c24;' : '' }}">
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $url_data->movie_name }}</td>
                                    <td>
                                        <input type="text" value="{{ $url_data->url }}" class="form-control" readonly style="background: transparent; border: none; width: 100%; color: inherit;">
                                    </td>
                                    <td>
                                        @if($url_data->is_used)
                                            <span class="badge badge-danger">Used</span>
                                        @else
                                            <span class="badge badge-success">Available</span>
                                        @endif
                                    </td>
                                    <td>{{ $url_data->updated_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ URL::to('admin/api_urls/delete/'.$url_data->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" onclick="return confirm('{{trans('words.dlt_warning_text')}}')" data-toggle="tooltip" title="{{trans('words.remove')}}"> <i class="fa fa-remove"></i> </a>
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

@endsection
