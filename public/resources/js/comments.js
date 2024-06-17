var postId = app.params.id;
var userId = app.user.id ?? null;

$(document).ready(function() {
    const $commentsContainer = $('#comments');
    let currentEditingCommentId = null;

    function initComments() {
        $('.comments-view-less').hide();
        $('.child-comments').hide();
    }

    initComments();

    $commentsContainer.on('click', '.comments-delete', function(e) {
        e.preventDefault();
        const id = $(this).data('comment-id');
        if (confirm('Are you sure you want to delete the comment?')) {
            $.post('/posts/delete_comments', { id: id }, () => fetchComments(postId, userId));
        }
    });

    $commentsContainer.on('click', '.comments-edit', function(e) {
        e.preventDefault();
        const id = $(this).data('comment-id');

        if (currentEditingCommentId !== null) {
            $(`.comment-edit-container[data-comment-id="${currentEditingCommentId}"]`).empty();
        }

        const commentContent = $(this).closest('.parent-comment').find(`.comment-content[data-comment-id="${id}"] p`).text();
        const editTemplate = `
            <div class="comment-user">
                <textarea class="comment-edit-textarea" placeholder="Write Comment">${commentContent}</textarea>
                <button class="comments-update" data-comment-id="${id}"><i class='bx bxs-paper-plane'></i></button>
            </div>
            <br>
            <button class="comments-cancel"><i class='bx bx-x'></i>Cancel...</button>
        `;
        $(this).closest('.parent-comment').find(`.comment-edit-container[data-comment-id="${id}"]`).html(editTemplate);
        currentEditingCommentId = id;
    });

    $commentsContainer.on('click', '.comments-cancel', function(e) {
        e.preventDefault();
        const commentId = $(this).closest('.comment-edit-container').data('comment-id');
        $(`.comment-edit-container[data-comment-id="${commentId}"]`).empty();
        currentEditingCommentId = null;
    });

    $commentsContainer.on('click', '.comments-update', function(e) {
        e.preventDefault();
        const id = $(this).data('comment-id');
        const updatedComment = $(this).closest('.comment').find('.comment-edit-textarea').val();
        $.post('/posts/edit_comments', { id: id, comment: updatedComment }, () => fetchComments(postId, userId));
    });

    $commentsContainer.on('click', '.comments-reply', function(e) {
        e.preventDefault();
        const id = $(this).data('comment-id');

        if (currentEditingCommentId !== null) {
            $(`.comment-edit-container[data-comment-id="${currentEditingCommentId}"]`).empty();
        }

        const replyTemplate = `
            <div class="comment-user">
                <textarea class="comment-create-textarea" placeholder="Write Answer"></textarea>
                <button class="comments-create" data-parent-id="${id}"><i class='bx bxs-paper-plane'></i></button>
            </div>
            <br>
            <button class="comments-cancel"><i class='bx bxs-message-x'></i>Cancel...</button>
        `;

        $(this).closest('.parent-comment').find(`.comment-edit-container[data-comment-id="${id}"]`).html(replyTemplate);
        currentEditingCommentId = id;
    });

    $commentsContainer.on('click', '.comments-create', function(e) {
        e.preventDefault();
        const parentCommentId = $(this).data('parent-id');
        const newComment = $(this).closest('.parent-comment').find('.comment-create-textarea').val();
        if (newComment.trim() !== '') {
            $.post('/posts/comments_son', {
                parentCommentId: parentCommentId,
                comment: newComment,
                postId: postId,
                userId: app.user.id
            }, () => fetchComments(postId, userId));
        }
    });

    function fetchComments(postId) {
        $.ajax({
            url: '/posts/show_comments',
            type: 'POST',
            data: { postId: postId },
            success: function(response) {
                const comments = JSON.parse(response);
                const commentsHTML = generateCommentsHTML(comments);
                $commentsContainer.html(commentsHTML);
                initComments();
                const totalComments = comments.total_comments !== undefined ? comments.total_comments : comments.length;
                $('.count_comments p').text(totalComments + ' Comments');
            },
            error: function() {
                initComments();
                $('.count_comments p').text('0 Comments');
                $commentsContainer.html('<p> Be the first to comment on this post! </p>');
            }
        });
    }

    function generateCommentsHTML(comments) {
        let html = '';
        const parentComments = comments.filter(comment => comment.parent_comment_id === null);
        parentComments.forEach(parentComment => {
            html += generateCommentHTML(parentComment, comments);
        });
        return html;
    }

    function generateCommentHTML(comment, allComments, level = 0) {
        let avatarHtml;
        if (app.user.avatar) {
          avatarHtml = `<img src="/assets/imgs/${app.user.avatar}" class="user-card-img alt="${app.user.username}" style="width:25px; height:25px">>`;
        } else {
          avatarHtml = `<img src="/resources/img/user.png" class="user-card-img alt="User" style="width:25px; height:25px">`;
        }
        let html = `
            <div class="comments-user">
                <div class="parent-comment">
                    <div class="user-info">
                    ${avatarHtml}
                        <p style="padding-left: 5px; ">${comment.username}</p>`;
            if (userId === comment.user_id) {
                html += ` <div class="actions-comments">  
                            <button class="comments-edit" data-comment-id="${comment.id}"><i class='bx bxs-message-alt-edit' ></i></button>
                            <button class="comments-delete" data-comment-id="${comment.id}"><i class='bx bxs-trash' ></i></button>
                          </div>`;
            }
            html+=`
                    </div>
                    <div class="_comment">
                        <div class="comment-content" data-comment-id="${comment.id}">
                            <p class="comment-comment">${comment.comment}</p>
                        </div>
                        <div class="reply-comment">
                            <p class="date-comment">${comment.created_at}</p>
                            <div class="comment-actions">`;
                    if (userId !== null) {
                        html += `<button class="comments-reply" data-comment-id="${comment.id}"><i class='bx bx-reply-all' ></i>Reply...</button>`;
                    }
        html += `           </div>
                        </div>
                        <div class="comment-edit-container" data-comment-id="${comment.id}"></div>
                        <br>`;
                        const childComments = allComments.filter(c => c.parent_comment_id === comment.id);
                        if (childComments.length > 0) {
                            html += `<button class="comments-view-more" data-comment-id="${comment.id}">View Answers...</button>`;
                            html += `<button class="comments-view-less" data-comment-id="${comment.id}">View Less...</button>`;
        html +=`
                    </div>`;
                            html += `<div class="child-comments" data-comment-id="${comment.id}">`;
                            childComments.forEach(childComment => {
                                html += generateCommentHTML(childComment, allComments, level + 1);
                            });
                            html += `</div>`;
                        }
        html +=`           
                </div>`;

        html += `</div>`;
        return html;
    }

    $commentsContainer.on('click', '.comments-view-more', function(e) {
        e.preventDefault();
        const id = $(this).data('comment-id');
        $(`.child-comments[data-comment-id="${id}"]`).show();
        $(this).hide();
        $(`.comments-view-less[data-comment-id="${id}"]`).show();
    });

    $commentsContainer.on('click', '.comments-view-less', function(e) {
        e.preventDefault();
        const id = $(this).data('comment-id');
        $(`.child-comments[data-comment-id="${id}"]`).hide();
        $(this).hide();
        $(`.comments-view-more[data-comment-id="${id}"]`).show();
    });

    fetchComments(postId);
});
