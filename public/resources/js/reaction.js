var postId = app.params.id;
var userId = app.user.id ?? null;

$(document).ready(function() {
    $(".all-reaction").hide();
    $(document).mouseup(function(e) {
        if ($(e.target).closest(".all-reaction").length === 0) {
            $(".all-reaction").hide();
        }
    });

    $(".react-con").click(function() {
        var id = this.id;
        var postId = id;
        var currentReaction = $("#" + postId).find("img").attr("src");
        if (currentReaction) {
            removeReaction(postId);
            return;
        }
        $("#react_" + id).show("slow");
        $(".reaction").off().click(function() {
            var reactId = this.id;
            var splitId = reactId.split("_");
            var reactType = splitId[0];
            var postId = splitId[1];
            insertReaction(reactType, postId);
        });
    });

    function insertReaction(reactType, postId) {
        $.ajax({
            type: "POST",
            url: "/posts/insert_reactions",
            data: {
                reactType: reactType,
                postId: postId,
                userId: app.user.id
            },
            success: function(data) {
                updateReactionCount(postId, data.total_reactions);
                var reactImg = "<img src='/resources/img/" + reactType + ".png' class='reaction-selected' >";
                $("#" + postId).html(reactImg);
                setReactionBackground(reactType, postId);
            }
        });
        $(".all-reaction").hide();
    }

    function removeReaction(postId) {
        $.ajax({
            type: "POST",
            url: "/posts/delete_reactions",
            data: {
                postId: postId,
                userId: app.user.id
            },
            success: function(data) {
                updateReactionCount(postId, data.total_reactions);
                $("#" + postId).html("<p class='like-action'><i class='bx bxs-like' onclick='checkSession()'></i></p>");
                $("#" + postId).css("background", "");
            }
        });
    }

    function updateReactionCount(postId, count) {
        var reactionCountElement = document.getElementById("reactions-count-" + postId);
        if (reactionCountElement) {
            reactionCountElement.innerHTML = ` <img src="/resources/img/like.png" alt="like">  ${count} reactions`;
        }
    }
    

    function setReactionBackground(reactType, postId) {
        if (reactType === "thumb") {
            $("#" + postId).css("background", "#e8e8ff");
        } else if (reactType === "love") {
            $("#" + postId).css("background", "#ffdddd");
        } else if (reactType === "haha") {
            $("#" + postId).css("background", "#fff7d8");
        } else if (reactType === "wow") {
            $("#" + postId).css("background", "#fff7d8");
        } else if (reactType === "sad") {
            $("#" + postId).css("background", "#fff7d8");
            $("#" + postId).css("padding", "2px");
        } else if (reactType === "angry") {
            $("#" + postId).css("background", "#ffdddd");
        }
    }

    $(".remove-reaction").click(function() {
        var postId = $(this).data("post-id");
        removeReaction(postId);
    });
});
