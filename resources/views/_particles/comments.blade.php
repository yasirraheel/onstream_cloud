<div class="row">
    <div class="col-md-12">
        <div class="vfx-item-section">
            <h3>Comments</h3>
        </div>
        <div class="comment-section">
            <div id="comments-list">
                @foreach($comments as $comment)
                    @include('_particles.comment_item', ['comment' => $comment])
                @endforeach
            </div>

            <div class="comment-form mt-4">
                @if(Auth::check())
                    <form id="comment-form">
                        @csrf
                        <input type="hidden" name="commentable_id" value="{{ $item_id }}">
                        <input type="hidden" name="commentable_type" value="{{ $item_type }}">
                        <div class="form-group">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Write a comment..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Submit</button>
                    </form>
                @else
                    <div class="login-to-comment">
                        <p>Please <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal">login</a> to comment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
@if(!Auth::check())
<div class="modal fade centered-modal" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark-2 text-light">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login / Signup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                 <p class="text-center">You need to login to post a comment.</p>
                 <div class="text-center mt-3">
                     <a href="{{ URL::to('login') }}" class="btn btn-primary">Login</a>
                     <a href="{{ URL::to('signup') }}" class="btn btn-secondary">Signup</a>
                 </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    .comment-item {
        background: #222;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #333;
    }
    .comment-user {
        font-weight: bold;
        color: #ff0000; /* Adjust to theme */
        margin-bottom: 5px;
        display: block;
    }
    .comment-date {
        font-size: 0.8em;
        color: #888;
        margin-left: 10px;
        font-weight: normal;
    }
    .comment-text {
        color: #ddd;
    }
    .login-to-comment {
        background: #222;
        padding: 15px;
        border-radius: 5px;
        text-align: center;
    }
</style>

<script>
    $(document).ready(function() {
        $('#comment-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: "{{ url('comments/add') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.status == 'success') {
                        if (response.comment_status == 1) {
                            $('#comments-list').prepend(response.html);
                        }
                        form[0].reset();
                        // Use SweetAlert if available or simple alert
                        if(typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            alert(response.message);
                        }
                    } else if (response.status == 'error') {
                         if(response.message == 'Login required') {
                             $('#loginModal').modal('show');
                         } else {
                             alert('Error: ' + JSON.stringify(response.errors));
                         }
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('An error occurred.');
                }
            });
        });
    });
</script>
