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
    .comment-section {
        margin-top: 20px;
    }
    .comment-item {
        background: #1a1a1a;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #333;
    }
    .comment-user {
        font-weight: 600;
        color: #fff;
        margin-bottom: 5px;
        display: block;
        font-size: 16px;
    }
    .comment-date {
        font-size: 0.85em;
        color: #aaa;
        margin-left: 10px;
        font-weight: normal;
    }
    .comment-text {
        color: #ccc;
        font-size: 14px;
        line-height: 1.5;
    }
    .login-to-comment {
        background: #1a1a1a;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #333;
        color: #ccc;
    }
    .login-to-comment a {
        color: #ff0000; /* Theme color */
        text-decoration: none;
        font-weight: bold;
    }
    .comment-form textarea {
        background-color: #1a1a1a;
        border: 1px solid #333;
        color: #fff;
    }
    .comment-form textarea:focus {
        background-color: #222;
        border-color: #555;
        color: #fff;
        box-shadow: none;
    }
    .comment-form .btn-primary {
        background-color: #ff0000;
        border-color: #ff0000;
        padding: 8px 20px;
        font-weight: 600;
    }
    .comment-form .btn-primary:hover {
        background-color: #cc0000;
        border-color: #cc0000;
    }
</style>

<script>
    $(document).ready(function() {
        $('#comment-form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();

            // Basic validation
            var commentText = form.find('textarea[name="comment"]').val();
            if (!commentText.trim()) {
                if(typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please enter a comment',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    alert('Please enter a comment');
                }
                return;
            }

            $.ajax({
                url: "{{ url('comments/add') }}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
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
                             var errorMsg = response.message || 'An error occurred';
                             if(response.errors) {
                                 errorMsg = Object.values(response.errors).join('\n');
                             }
                             if(typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errorMsg
                                });
                             } else {
                                 alert(errorMsg);
                             }
                         }
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    var msg = 'An error occurred.';
                    if(xhr.status === 419) {
                        msg = 'Session expired. Please refresh the page.';
                    }
                    if(typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    } else {
                        alert(msg);
                    }
                }
            });
        });
    });
</script>
