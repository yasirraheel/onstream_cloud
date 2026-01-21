<div class="comment-item">
    <div class="comment-user">
        <span class="user-name">{{ $comment->user->name ?? 'User' }}</span>
        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
    </div>
    <div class="comment-text">
        {!! nl2br(e($comment->comment)) !!}
    </div>
</div>
