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
                    <div class="col-xl-4 col-md-6">
                        <div class="card-box widget-user bg-primary text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $total_urls }}</h2>
                                <h5>Total Files</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <div class="card-box widget-user bg-success text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $available_urls_count }}</h2>
                                <h5>Available Files</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6">
                        <div class="card-box widget-user bg-danger text-white">
                            <div class="text-center">
                                <h2 class="text-white" data-plugin="counterup">{{ $used_urls_count }}</h2>
                                <h5>Used Files</h5>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gd_urls as $i => $url_data)
                                <tr style="{{ $url_data->is_used ? 'background-color: #f8d7da; color: #721c24;' : '' }}">
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $url_data->file_name }}</td>
                                    <td><span class="badge badge-info">{{ $url_data->folder_id ?? 'N/A' }}</span></td>
                                    <td>
                                        <input type="text" value="{{ $url_data->url }}" class="form-control" readonly style="background: transparent; border: none; width: 100%; color: inherit;">
                                    </td>
                                    <td>{{ $url_data->file_size ? number_format($url_data->file_size / 1048576, 2) . ' MB' : 'N/A' }}</td>
                                    <td>{{ $url_data->mime_type ?? 'N/A' }}</td>
                                    <td>
                                        @if($url_data->is_used)
                                            <span class="badge badge-danger">Used</span>
                                        @else
                                            <span class="badge badge-success">Available</span>
                                        @endif
                                    </td>
                                    <td>{{ $url_data->updated_at->format('Y-m-d H:i') }}</td>
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
