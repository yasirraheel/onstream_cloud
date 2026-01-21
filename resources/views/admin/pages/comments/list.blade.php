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
                      <span aria-hidden="true">&times;</span></button>
                        {{ Session::get('flash_message') }}
                    </div>
                @endif
                <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>User</th>
                      <th>Content</th>
                      <th>Comment</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                   @foreach($comments_list as $comment)
                    <tr>
                      <td>{{ $comment->user->name ?? 'Unknown' }}</td>
                      <td>
                          @if($comment->commentable_type == 'App\Movies')
                            Movie: {{ $comment->commentable->video_title ?? 'Deleted' }}
                          @elseif($comment->commentable_type == 'App\Series')
                            Series: {{ $comment->commentable->series_name ?? 'Deleted' }}
                          @elseif($comment->commentable_type == 'App\LiveTV')
                            LiveTV: {{ $comment->commentable->channel_name ?? 'Deleted' }}
                          @else
                            {{ $comment->commentable_type }}
                          @endif
                      </td>
                      <td>{{ $comment->comment }}</td>
                      <td>{{ $comment->created_at->format('Y-m-d H:i') }}</td>
                      <td>
                          @if($comment->status == 1)
                            <span class="badge badge-success">Approved</span>
                          @else
                            <span class="badge badge-warning">Pending</span>
                          @endif
                      </td>
                      <td>
                        @if($comment->status == 0)
                            <a href="{{ url('admin/comments/approve/'.$comment->id) }}" class="btn btn-icon waves-effect waves-light btn-success m-b-5 m-r-5" data-toggle="tooltip" title="Approve"> <i class="fa fa-check"></i> </a>
                        @endif
                        <a href="{{ url('admin/comments/delete/'.$comment->id) }}" class="btn btn-icon waves-effect waves-light btn-danger m-b-5" data-toggle="tooltip" title="Remove" onclick="return confirm('Are you sure?')"> <i class="fa fa-remove"></i> </a>
                      </td>
                    </tr>
                   @endforeach
                  </tbody>
                </table>
              </div>
                <nav class="paging_simple_numbers">
                @include('admin.pagination', ['paginator' => $comments_list])
                </nav>

              </div>
            </div>
          </div>
        </div>
      </div>
      @include("admin.copyright")
    </div>

@endsection
