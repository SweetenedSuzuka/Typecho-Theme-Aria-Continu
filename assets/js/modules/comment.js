var Aria = (window.Aria = window.Aria || {});

Aria.commentPlus = {
  init: function () {
    this.emotion();
    this.ajaxAvatar();

    if (THEME_CONFIG.ENABLE_AJAX_COMMENT) {
      this.ajaxComment();
    }
  },

  emotion: function () {
    if (!$(".OwO").length) {
      return;
    }

    new OwO({
      logo: '<i class="iconfont icon-aria-emotion"></i>表情',
      container: document.getElementsByClassName("OwO")[0],
      target: document.getElementsByClassName("textarea")[0],
      api: THEME_CONFIG.OWO_JSON,
      position: "down",
      width: "100%",
      maxHeight: "250px",
    });
  },

  ajaxAvatar: function () {
    if (!$("#comment-avatar").length) {
      return;
    }

    function updateAvatar(selector) {
      var email = $(selector).val();

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

    if ($("input#mail").val() !== "") {
      updateAvatar("input#mail");
    }

    $("input#mail").blur(updateAvatar("input#mail"));
  },

  ajaxComment: function () {
    var currentReplyId = "";

    var bindReplyEvents = function () {
      $(".comment-reply a").click(function () {
        currentReplyId = $(this).parent().parent().parent().parent().attr("id");
      });

      $(".cancel-comment-reply a").click(function () {
        currentReplyId = "";
      });
    };

    bindReplyEvents();

    $("#comment-form").submit(function () {
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
          if (!$("#comments", response).length) {
            var message =
              $("title").eq(0).text().trim().toLowerCase() === "error"
                ? $(".container", response).eq(0).text()
                : "评论提交失败！";

            notifier.alert(message);
            finishSubmit(!1);
            return !1;
          }

          var currentComment;
          var commentsCount;

          $("input,textarea", form).attr("disabled", !1);
          $("#textarea").val("");
          currentCommentId = $(".comment-list", response)
            .html()
            .match(/id=\"?comment-\d+/g)
            .join()
            .match(/\d+/g)
            .sort(function (left, right) {
              return left - right;
            })
            .pop();

          if (currentReplyId === "") {
            if ($(".comment-list").length) {
              if (!$(".prev").length) {
                currentComment = $("#li-comment-" + currentCommentId, response);
                $(".comment-list")
                  .first()
                  .prepend(currentComment.addClass("animated fadeInUp"));
              }
            } else {
              currentComment = $("#li-comment-" + currentCommentId, response);
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
            currentComment = $("#li-comment-" + currentCommentId, response);

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
  },
};
