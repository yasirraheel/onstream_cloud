<div class="row">
    <div class="col-md-12">
        <div class="vfx-item-section">
            <h3>Comments</h3>
        </div>

        <!-- Highlight Badge -->
        <div id="comment-badge" class="comment-badge" style="display: none;">
            <div class="comment-badge-content">
                <i class="fa fa-commenting-o"></i>
                <span>Join the conversation! You can now post comments here.</span>
                <button class="btn-close-badge" onclick="closeCommentBadge()">
                     <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="comment-badge-footer">
                <a href="javascript:void(0)" onclick="dontShowAgain()">Don't show again</a>
            </div>
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

    /* Comment Badge Styles */
    .comment-badge {
        background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
        color: #fff;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3);
        position: relative;
        animation: fadeIn 0.5s ease-in-out;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .comment-badge-content {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 16px;
        font-weight: 600;
    }

    .comment-badge-content i {
        font-size: 24px;
    }

    .btn-close-badge {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        margin-left: auto;
        font-size: 18px;
        padding: 0;
        transition: color 0.3s;
    }

    .btn-close-badge:hover {
        color: #fff;
    }

    .comment-badge-footer {
        margin-top: 8px;
        text-align: right;
        font-size: 12px;
    }

    .comment-badge-footer a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: underline;
        transition: color 0.3s;
    }

    .comment-badge-footer a:hover {
        color: #fff;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

@push('scripts')
<script>
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }

    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function closeCommentBadge() {
        $('#comment-badge').fadeOut();
    }

    function dontShowAgain() {
        setCookie("hide_comment_badge", "true", 365); // Set for 1 year
        closeCommentBadge();
        if(typeof Swal !== 'undefined') {
             const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: false,
            })
            Toast.fire({
                icon: 'info',
                title: 'Badge will not be shown again.'
            })
        }
    }

    $(document).ready(function() {
        // Check if cookie exists
        var hideBadge = getCookie("hide_comment_badge");
        if (!hideBadge) {
            $('#comment-badge').show();
        }

        console.log("Comments script loaded");

        // Unbind any previous handlers to prevent duplicates if loaded multiple times via AJAX (though unlikely here)
        $(document).off('submit', '#comment-form');

        $(document).on('submit', '#comment-form', function(e) {
            e.preventDefault();
            console.log("Form submit intercepted");

            var form = $(this);
            var formData = form.serialize();
            var submitBtn = form.find('button[type="submit"]');

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

            submitBtn.prop('disabled', true);

            $.ajax({
                url: "{{ url('comments/add') }}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    submitBtn.prop('disabled', false);

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
                    submitBtn.prop('disabled', false);
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
@endpush
