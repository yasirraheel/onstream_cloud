@extends("admin.admin_app")

@section("content")

<div class="content-page">
    <div class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <h4 class="m-t-0 header-title"><b>{{$page_title}}</b></h4>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <!-- Top Stats -->
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30">Top 10 Searched Keywords</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Keyword</th>
                                        <th>Search Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top_keywords as $keyword)
                                    <tr>
                                        <td>{{$keyword->keyword}}</td>
                                        <td><span class="badge badge-success">{{$keyword->total}}</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-6">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30">Searches by Country</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>Search Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($top_countries as $country)
                                    <tr>
                                        <td>
                                            @if($country->country_code)
                                                <img src="https://flagcdn.com/24x18/{{strtolower($country->country_code)}}.png" alt="{{$country->country_code}}">
                                            @endif
                                            {{$country->country}}
                                        </td>
                                        <td><span class="badge badge-primary">{{$country->total}}</span></td>
                                    </tr>
                                    @endforeach
                                    @if(count($top_countries) == 0)
                                    <tr>
                                        <td colspan="2" class="text-center">No location data available yet.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Trends -->
            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30">Daily Search Volume (Last 30 Days)</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Total Searches</th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daily_searches as $daily)
                                    <tr>
                                        <td>{{$daily->date}}</td>
                                        <td>{{$daily->total}}</td>
                                        <td>
                                            <div class="progress mb-0" style="height: 10px;">
                                                <div class="progress-bar bg-custom" role="progressbar" style="width: {{ min(100, ($daily->total * 5)) }}%;" aria-valuenow="{{$daily->total}}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if(count($daily_searches) == 0)
                                    <tr>
                                        <td colspan="3" class="text-center">No data available for the last 30 days.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-30">Last 10 Searches</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Keyword</th>
                                        <th>User</th>
                                        <th>Country</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_searches as $search)
                                    <tr>
                                        <td>{{$search->keyword}}</td>
                                        <td>
                                            @if($search->user)
                                                <a href="{{ url('admin/users/history/'.$search->user_id) }}" target="_blank">{{$search->user->name}}</a>
                                            @else
                                                Guest
                                            @endif
                                        </td>
                                        <td>{{$search->country ?? '-'}}</td>
                                        <td>{{$search->created_at->diffForHumans()}}</td>
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
    @include("admin.copyright")
</div>

@endsection
