@extends("admin.admin_app")

@section("content")

<div class="content-page">
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card-box table-responsive" style="background-color: #1a2234; color: #fff;">

            <div class="row">
              <div class="col-sm-6">
                  <h4 class="header-title m-t-0">{{ $page_title }}</h4>
              </div>
              <div class="col-sm-6">
                  <div class="pull-right">
                    <a href="{{ URL::to('admin/announcements/add') }}" class="btn btn-success btn-md waves-effect waves-light m-b-20"><i class="fa fa-plus"></i> Add Announcement</a>
                  </div>
              </div>
            </div>

            @if(Session::has('flash_message'))
              <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                {{ Session::get('flash_message') }}
              </div>
            @endif

              <table class="table table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Show as Popup</th>
                    <th>Views</th>
                    <th>Created Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($announcements as $announcement)
                  <tr>
                    <td>{{ $announcement->title }}</td>
                    <td>{{ Str::limit(strip_tags($announcement->message), 100) }}</td>
                    <td>
                      @if($announcement->is_active)
                        <span class="badge badge-success">Active</span>
                      @else
                        <span class="badge badge-danger">Inactive</span>
                      @endif
                    </td>
                    <td>
                      @if($announcement->show_as_popup)
                        <span class="badge badge-info">Yes</span>
                      @else
                        <span class="badge badge-secondary">No</span>
                      @endif
                    </td>
                    <td>{{ number_format($announcement->view_count) }}</td>
                    <td>{{ date('M d, Y', strtotime($announcement->created_at)) }}</td>
                    <td>
                      <a href="{{ url('admin/announcements/edit/'.$announcement->id) }}" class="btn btn-icon waves-effect waves-light btn-primary m-b-5 m-r-5"> <i class="fa fa-edit"></i> </a>
                      <a href="{{ url('admin/announcements/delete/'.$announcement->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" onclick="return confirm('Are you sure?')"> <i class="fa fa-trash"></i> </a>
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

@endsection
