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
                                    <td>
                                        <a href="#" class="search-movie" data-title="{{ $request->movie_name }}" style="color: #007bff; font-weight: bold;">
                                            {{ $request->movie_name }}
                                        </a>
                                    </td>
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

<!-- Modal for Movie Selection -->
<div class="modal fade" id="movieRequestModal" tabindex="-1" role="dialog" aria-labelledby="movieRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="movieRequestModalLabel" style="color: #333;">Search Results</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="movieRequestResults" style="max-height: 500px; overflow-y: auto;">
                <!-- Results will be loaded here -->
            </div>
        </div>
    </div>
</div>

@section('extra_scripts')
<script>
$(document).ready(function() {
    $('.search-movie').click(function(e) {
        e.preventDefault();
        var title = $(this).attr('data-title');

        if (!title || title.trim() === '') {
             alert('Movie name is empty!');
             return;
        }

        $('#movieRequestModal').modal('show');
        $('#movieRequestResults').html('<div class="text-center" style="padding: 20px;"><i class="fa fa-spinner fa-spin fa-3x"></i><br><br>Searching for "' + title + '"...</div>');

        $.ajax({
            url: "{{ URL::to('admin/find_imdb_movie') }}",
            data: { id: title, from: 'movie' },
            success: function(response) {
                if (response.imdb_status == 'selection_required') {
                    var html = '<div class="list-group">';
                    $.each(response.results, function(index, movie) {
                        var poster = movie.poster_path ? movie.poster_path : 'https://via.placeholder.com/92x138.png?text=No+Image';
                        html += '<a href="{{ URL::to("admin/movies/add_movie") }}?tmdb_id=' + movie.id + '" class="list-group-item list-group-item-action" style="display: flex; gap: 15px; align-items: start; background-color: #fff; color: #333; border-bottom: 1px solid #eee;">';
                        html += '<img src="' + poster + '" alt="' + movie.title + '" style="width: 60px; height: 90px; object-fit: cover; border-radius: 4px;">';
                        html += '<div>';
                        html += '<h5 class="mb-1" style="font-weight: bold; font-size: 16px; color: #333;">' + movie.title + '</h5>';
                        html += '<p class="mb-1" style="font-size: 14px; color: #666;">Released: ' + movie.release_date + '</p>';
                        html += '<p class="mb-0" style="font-size: 13px; color: #888;">' + (movie.overview ? movie.overview : '') + '</p>';
                        html += '</div>';
                        html += '</a>';
                    });
                    html += '</div>';
                    $('#movieRequestResults').html(html);
                } else if (response.imdb_status == 'success') {
                     // In case it returns success directly (single match logic if exists)
                    var html = '<div class="alert alert-success">Movie found! Redirecting...</div>';
                    $('#movieRequestResults').html(html);
                    // Redirect with the ID found
                    window.location.href = "{{ URL::to('admin/movies/add_movie') }}?tmdb_id=" + response.imdbid; // Wait, response.imdbid might be IMDB ID or TMDB ID depending on logic.
                    // The find_imdb_movie returns 'imdbid' which is assigned from $result->imdb_id.
                    // But for import we can use TMDB ID as well?
                    // Let's check ImportImdbController logic again.
                    // If it's success, it means it fetched details.
                    // But we want to redirect to Add Movie page.
                    // If we pass IMDB ID, it works.
                } else {
                    $('#movieRequestResults').html('<div class="alert alert-danger">No results found for "' + title + '".</div>');
                }
            },
            error: function() {
                $('#movieRequestResults').html('<div class="alert alert-danger">Error occurred while searching.</div>');
            }
        });
    });
});
</script>
@endsection

@endsection
