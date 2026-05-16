var Aria = (window.Aria = window.Aria || {});

function bindEmotion() {
  var container = $(".OwO").eq(0);
  if (!container.length || container.attr("data-aria-owo-bound") === "true") {
    return;
  }

  container.attr("data-aria-owo-bound", "true");
  new OwO({
    logo: '<i class="iconfont icon-aria-emotion"></i>表情',
    container: container.get(0),
    target: document.getElementsByClassName("textarea")[0],
    api: THEME_CONFIG.OWO_JSON,
    position: "down",
    width: "100%",
    maxHeight: "250px",
  });
}

function bindAjaxAvatar() {
  var mailInput = $("input#mail");
  if (!$("#comment-avatar").length || !mailInput.length) {
    return;
  }

  function updateAvatar() {
    var email = mailInput.val();

    if (email === "") {
      return;
    }

    $.ajax({
      type: "GET",
      data: {
        action: "ajax_avatar_get",
        form: THEME_CONFIG.SITE_URL,
        email: email,
      },
      success: function (avatarUrl) {
        $("#comment-avatar").attr("src", avatarUrl);
      },
    });
  }

  if (mailInput.val() !== "") {
    updateAvatar();
  }

  mailInput.off("blur.ariaAvatar").on("blur.ariaAvatar", updateAvatar);
}

function bindAjaxComment() {
  var currentReplyId = "";

  function createResponseRoot(response) {
    var parsedNodes = $.parseHTML(response, document, !0) || [];
    return $("<div></div>").append(parsedNodes);
  }

  function bindReplyEvents() {
    $(".comment-reply a")
      .off("click.ariaReply")
      .on("click.ariaReply", function () {
        currentReplyId = $(this).parent().parent().parent().parent().attr("id");
      });

    $(".cancel-comment-reply a")
      .off("click.ariaReplyCancel")
      .on("click.ariaReplyCancel", function () {
        currentReplyId = "";
      });
  }

  bindReplyEvents();

  $("#comment-form")
    .off("submit.ariaAjaxComment")
    .on("submit.ariaAjaxComment", function () {
      var submitButton = $(".submit").eq(0);
      var form = $("#comment-form");
      var currentCommentId = "";
      var notifier = new Notyf({ delay: 3e3 });
      var formData = $(this).serializeArray();

      function finishSubmit(success) {
        submitButton.attr("disabled", !1).css("cursor", "pointer");
        form.css({ opacity: "1" });
        $("textarea", form).css({ background: "initial" });
        $("input,textarea", form).attr("disabled", !1);

        if (success) {
          $("#textarea").val("");
          currentReplyId = "";
        }

        bindReplyEvents();
      }

      submitButton.attr("disabled", !0).css("cursor", "not-allowed");
      form.css({ opacity: ".5" });
      $("textarea", form).css({
        background:
          'url("' +
          THEME_CONFIG.THEME_URL +
          '/assets/img/loading.svg") center center no-repeat',
      });
      $("input,textarea", form).attr("disabled", !0);

      $.ajax({
        type: $(this).attr("method"),
        url: $(this).attr("action"),
        data: formData,
        success: function (response) {
          var responseRoot = createResponseRoot(response);

          if (!responseRoot.find("#comments").length) {
            var responseMessage = $.trim(responseRoot.find(".container").first().text());
            var message =
              responseRoot.find("title").eq(0).text().trim().toLowerCase() === "error"
                ? responseMessage || "评论提交失败！"
                : "评论提交失败！";

            notifier.alert(message);
            finishSubmit(!1);
            return !1;
          }

          var currentComment;
          var commentsCount;

          $("input,textarea", form).attr("disabled", !1);
          $("#textarea").val("");

          var responseCommentHtml = responseRoot.find(".comment-list").html() || "";
          var commentMatches = responseCommentHtml.match(/id=\"?comment-\d+/g);
          if (!commentMatches || !commentMatches.length) {
            finishSubmit(!0);
            notifier.confirm("评论提交成功！");
            return !1;
          }

          currentCommentId = commentMatches
            .join()
            .match(/\d+/g)
            .sort(function (left, right) {
              return left - right;
            })
            .pop();

          if (currentReplyId === "") {
            if ($(".comment-list").length) {
              if (!$(".prev").length) {
                currentComment = responseRoot.find("#li-comment-" + currentCommentId);
                $(".comment-list")
                  .first()
                  .prepend(currentComment.addClass("animated fadeInUp"));
              }
            } else {
              currentComment = responseRoot.find("#li-comment-" + currentCommentId);
              $("#response").after(
                '<div class="comment-data"><ol class="comment-list"></ol></div>',
              );
              $(".comment-list")
                .first()
                .prepend(currentComment.addClass("animated fadeInUp"));
            }

            $("html,body").animate(
              { scrollTop: $("#response").offset().top - 100 },
              1e3,
            );
          } else {
            currentComment = responseRoot.find("#li-comment-" + currentCommentId);

            if (!$("#" + currentReplyId).find(".comment-children").length) {
              $("#" + currentReplyId).append(
                '<div class="comment-children"><ol class="comment-list"></ol></div>',
              );
            }

            $("#" + currentReplyId + " .comment-children .comment-list")
              .first()
              .prepend(currentComment.addClass("animated fadeInUp"));
            TypechoComment.cancelReply();
          }

          commentsCount = parseInt($("#response").text());
          $("#response").html($("#response").html().replace(/\d+/, commentsCount + 1));

          finishSubmit(!0);
          notifier.confirm("评论提交成功！");
        },
        error: function () {
          console.log("Ajax Comment Error");
          window.location.reload();
        },
      });

      return !1;
    });
}

Aria.commentPlus = Aria.commentPlus || {};
Aria.commentPlus.init = function () {
  bindEmotion();
  bindAjaxAvatar();

  if (THEME_CONFIG.ENABLE_AJAX_COMMENT) {
    bindAjaxComment();
  }
};
