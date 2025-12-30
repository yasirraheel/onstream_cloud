@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card-box table-responsive">

                        <div class="row">
                            <div class="col-sm-6">
                                <h4 class="m-t-0 header-title"><b>{{$page_title}}</b></h4>
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

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Movie Name</th>
                                    <th>Language</th>
                                    <th>Message</th>
                                    <th>User / Email</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requests as $request)
                                <tr>
                                    <td>{{ $request->movie_name }}</td>
                                    <td>{{ $request->language }}</td>
                                    <td>{{ $request->message }}</td>
                                    <td>
                                        @if($request->user)
                                            <a href="{{ url('admin/users/history/'.$request->user_id) }}">{{ $request->user->name }}</a>
                                        @else
                                            {{ $request->email ?? 'Guest' }}
                                        @endif
                                    </td>
                                    <td>{{ $request->created_at->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        @if($request->status == 'Pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-success">Completed</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->status == 'Pending')
                                            <a href="{{ url('admin/movie_requests/status/'.$request->id.'/Completed') }}" class="btn btn-icon waves-effect waves-light btn-success" data-toggle="tooltip" title="Mark as Completed"> <i class="fa fa-check"></i> </a>
                                        @else
                                            <a href="{{ url('admin/movie_requests/status/'.$request->id.'/Pending') }}" class="btn btn-icon waves-effect waves-light btn-warning" data-toggle="tooltip" title="Mark as Pending"> <i class="fa fa-undo"></i> </a>
                                        @endif
                                        <a href="{{ url('admin/movie_requests/delete/'.$request->id) }}" class="btn btn-icon waves-effect waves-light btn-danger" onclick="return confirm('Are you sure?')" data-toggle="tooltip" title="{{trans('words.remove')}}"> <i class="fa fa-remove"></i> </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $requests])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

@endsection
