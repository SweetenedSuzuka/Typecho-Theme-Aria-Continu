var Aria = (window.Aria = window.Aria || {});

function bindEmotion() {
  var container = document.querySelector(".OwO");
  var target = document.querySelector(".textarea");

  if (!container || !target || container.getAttribute("data-aria-owo-bound") === "true") {
    return;
  }

  container.setAttribute("data-aria-owo-bound", "true");
  new OwO({
    logo: '<i class="iconfont icon-aria-emotion"></i>表情',
    container: container,
    target: target,
    api: THEME_CONFIG.OWO_JSON,
    position: "down",
    width: "100%",
    maxHeight: "250px",
  });
}

function bindAjaxAvatar() {
  var avatar = document.getElementById("comment-avatar");
  var mailInput = document.getElementById("mail");

  if (!avatar || !mailInput || mailInput.getAttribute("data-aria-avatar-bound") === "true") {
    return;
  }

  async function updateAvatar() {
    var email = mailInput.value.trim();
    var requestUrl;
    var response;
    var avatarUrl;

    if (email === "") {
      return;
    }

    requestUrl = new URL(window.location.href);
    requestUrl.hash = "";
    requestUrl.searchParams.set("action", "ajax_avatar_get");
    requestUrl.searchParams.set("form", THEME_CONFIG.SITE_URL);
    requestUrl.searchParams.set("email", email);

    try {
      response = await fetch(requestUrl.toString(), {
        method: "GET",
        credentials: "same-origin",
      });
      if (!response.ok) {
        return;
      }

      avatarUrl = (await response.text()).trim();
      if (avatarUrl !== "") {
        avatar.src = avatarUrl;
      }
    } catch (error) {
      console.warn("Avatar fetch failed", error);
    }
  }

  mailInput.setAttribute("data-aria-avatar-bound", "true");

  if (mailInput.value.trim() !== "") {
    updateAvatar();
  }

  mailInput.addEventListener("blur", updateAvatar);
}

function bindAjaxComment() {
  var doc = document;
  var form = doc.getElementById("comment-form");
  var submitButton = form ? form.querySelector(".submit") : null;
  var textarea = doc.getElementById("textarea");
  var currentReplyId = "";
  var notifier = new Notyf({ delay: 3e3 });

  function parseResponseDocument(responseText) {
    return new DOMParser().parseFromString(responseText, "text/html");
  }

  function getText(node) {
    return node ? node.textContent.trim() : "";
  }

  function setSubmitting(isSubmitting) {
    if (!form || !submitButton) {
      return;
    }

    submitButton.disabled = isSubmitting;
    submitButton.style.cursor = isSubmitting ? "not-allowed" : "pointer";
    form.style.opacity = isSubmitting ? ".5" : "1";

    if (textarea) {
      textarea.style.background = isSubmitting
        ? 'url("' +
          THEME_CONFIG.THEME_URL +
          '/assets/img/loading.svg") center center no-repeat'
        : "initial";
    }

    Array.prototype.forEach.call(form.querySelectorAll("input,textarea"), function (field) {
      field.disabled = isSubmitting;
    });
  }

  function finishSubmit(success) {
    setSubmitting(!1);

    if (success && textarea) {
      textarea.value = "";
      currentReplyId = "";
    }
  }

  function getLatestCommentNode(responseDoc) {
    var commentIds = Array.prototype.map
      .call(responseDoc.querySelectorAll("[id^='li-comment-']"), function (node) {
        return parseInt(node.id.replace(/\D+/g, ""), 10);
      })
      .filter(function (id) {
        return Number.isFinite(id);
      })
      .sort(function (left, right) {
        return left - right;
      });

    if (!commentIds.length) {
      return null;
    }

    return responseDoc.getElementById("li-comment-" + commentIds.pop());
  }

  function updateCommentsCount() {
    var responseTitle = doc.getElementById("response");
    var currentCount;

    if (!responseTitle) {
      return;
    }

    currentCount = parseInt(responseTitle.textContent, 10);
    if (!Number.isFinite(currentCount)) {
      return;
    }

    responseTitle.innerHTML = responseTitle.innerHTML.replace(/\d+/, String(currentCount + 1));
  }

  function scrollToResponse() {
    var responseTitle = doc.getElementById("response");
    if (!responseTitle) {
      return;
    }

    window.scrollTo({
      top: Math.max(responseTitle.getBoundingClientRect().top + window.scrollY - 100, 0),
      behavior: "smooth",
    });
  }

  function ensureCommentList() {
    var commentList = doc.querySelector(".comment-list");
    var responseTitle;
    var commentData;

    if (commentList) {
      return commentList;
    }

    responseTitle = doc.getElementById("response");
    if (!responseTitle) {
      return null;
    }

    commentData = doc.createElement("div");
    commentData.className = "comment-data";
    commentData.innerHTML = '<ol class="comment-list"></ol>';
    responseTitle.insertAdjacentElement("afterend", commentData);
    return commentData.querySelector(".comment-list");
  }

  function prependAnimatedComment(list, commentNode) {
    if (!list || !commentNode) {
      return;
    }

    commentNode.classList.add("animated", "fadeInUp");
    list.prepend(commentNode);
  }

  function bindReplyStateTracking() {
    if (doc.body.getAttribute("data-aria-reply-bound") === "true") {
      return;
    }

    doc.body.setAttribute("data-aria-reply-bound", "true");
    doc.addEventListener("click", function (event) {
      var replyLink = event.target.closest(".comment-reply a");
      var cancelLink = event.target.closest(".cancel-comment-reply a");
      var replyRoot;

      if (replyLink) {
        replyRoot = replyLink.closest("[id^='li-comment-']");
        currentReplyId = replyRoot ? replyRoot.id : "";
        return;
      }

      if (cancelLink) {
        currentReplyId = "";
      }
    });
  }

  if (!form || !submitButton || form.getAttribute("data-aria-ajax-comment-bound") === "true") {
    return;
  }

  bindReplyStateTracking();
  form.setAttribute("data-aria-ajax-comment-bound", "true");

  form.addEventListener("submit", async function (event) {
    var formData;
    var response;
    var responseText;
    var responseDoc;
    var responseContainer;
    var titleText;
    var responseMessage;
    var message;
    var latestComment;
    var importedComment;
    var commentList;
    var parentComment;
    var childrenRoot;

    event.preventDefault();
    formData = new URLSearchParams(new FormData(form));
    setSubmitting(!0);

    try {
      response = await fetch(form.action, {
        method: (form.getAttribute("method") || "POST").toUpperCase(),
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        },
        body: formData.toString(),
      });
      responseText = await response.text();
      responseDoc = parseResponseDocument(responseText);
      responseContainer = responseDoc.querySelector("#comments");

      if (!responseContainer) {
        titleText = getText(responseDoc.querySelector("title")).toLowerCase();
        responseMessage = getText(responseDoc.querySelector(".container"));
        message = titleText === "error" ? responseMessage || "评论提交失败！" : "评论提交失败！";
        notifier.alert(message);
        finishSubmit(!1);
        return;
      }

      latestComment = getLatestCommentNode(responseDoc);
      if (!latestComment) {
        finishSubmit(!0);
        notifier.confirm("评论提交成功！");
        return;
      }

      importedComment = document.importNode(latestComment, !0);

      if (currentReplyId === "") {
        commentList = ensureCommentList();
        if (commentList && !doc.querySelector(".prev")) {
          prependAnimatedComment(commentList, importedComment);
        }
        scrollToResponse();
      } else {
        parentComment = doc.getElementById(currentReplyId);
        if (!parentComment) {
          finishSubmit(!1);
          notifier.alert("回复目标不存在，页面将刷新后重试。");
          window.location.reload();
          return;
        }

        childrenRoot = parentComment.querySelector(".comment-children .comment-list");
        if (!childrenRoot) {
          parentComment.insertAdjacentHTML(
            "beforeend",
            '<div class="comment-children"><ol class="comment-list"></ol></div>',
          );
          childrenRoot = parentComment.querySelector(".comment-children .comment-list");
        }

        prependAnimatedComment(childrenRoot, importedComment);
        if (window.TypechoComment && typeof window.TypechoComment.cancelReply === "function") {
          window.TypechoComment.cancelReply();
        }
      }

      updateCommentsCount();
      finishSubmit(!0);
      notifier.confirm("评论提交成功！");
    } catch (error) {
      console.error("Ajax Comment Error", error);
      window.location.reload();
    }
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
