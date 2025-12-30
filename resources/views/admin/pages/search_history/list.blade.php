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
                            <div class="col-sm-6">
                                <a href="{{URL::to('admin/search_history/clear')}}" class="btn btn-danger btn-md waves-effect waves-light m-b-20 pull-right" onclick="return confirm('Are you sure you want to delete all history?')" data-toggle="tooltip" title="Clear All History"><i class="fa fa-trash"></i> Clear All History</a>
                                <a href="{{URL::to('admin/search_history/analytics')}}" class="btn btn-info btn-md waves-effect waves-light m-b-20 pull-right m-r-10" data-toggle="tooltip" title="Analytics"><i class="fa fa-bar-chart-o"></i> Analytics</a>
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
                                    <th>#</th>
                                    <th>Keyword</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($search_history as $i => $history)
                                <tr>
                                    <td>{{ $history->id }}</td>
                                    <td>{{ $history->keyword }}</td>
                                    <td>
                                        @if($history->user)
                                            <a href="{{ url('admin/users/history/'.$history->user_id) }}">{{ $history->user->name }}</a>
                                        @else
                                            Guest
                                        @endif
                                    </td>
                                    <td>{{ $history->ip_address }}</td>
                                    <td>{{ $history->created_at->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ url('admin/search_history/delete/'.$history->id) }}" class="btn btn-icon waves-effect waves-light btn-danger" onclick="return confirm('Are you sure?')" data-toggle="tooltip" title="{{trans('words.remove')}}"> <i class="fa fa-remove"></i> </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <nav class="paging_simple_numbers">
                            @include('admin.pagination', ['paginator' => $search_history])
                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("admin.copyright")
</div>

@endsection
