var Aria = (window.Aria = window.Aria || {});

function headroom() {
  var navigation = document.querySelector("#nav-menu");
  new Headroom(navigation).init();
}

function gotop() {
  $(window)
    .off("scroll.ariaGoTop")
    .on("scroll.ariaGoTop", function () {
      if ($(window).scrollTop() > 100) {
        $("#go-top").fadeIn(500);
        $("#site-avatar").css({
          height: "25px",
          width: "25px",
          margin: "19.5px 5px 0 0",
        });
      } else {
        $("#go-top").fadeOut(500, function () {
          $("#go-top").css("display", "none");
        });
        $("#site-avatar").css({
          height: "35px",
          width: "35px",
          margin: "14.5px 5px 0 0",
        });
      }

      if (!$("#toc").length) {
        return;
      }

      var currentTop = $(this).scrollTop();
      var titleIds = Aria.toc.titleId;
      var currentAnchor = null;

      for (var index in titleIds) {
        var selector = "#" + titleIds[index];
        if ($(selector).offset().top > currentTop + 100) {
          continue;
        }

        if (currentAnchor) {
          if ($(selector).offset().top >= $(currentAnchor).offset().top) {
            currentAnchor = selector;
          }
        } else {
          currentAnchor = selector;
        }
      }

      if (currentAnchor) {
        $("#toc a").removeClass("toc-active");
        $('#toc a[href="' + currentAnchor + '"]').addClass("toc-active");
      }
    });
}

function nav() {
  $(".nav-right-item")
    .off("mouseenter.ariaNavSub mouseleave.ariaNavSub")
    .on("mouseenter.ariaNavSub", function () {
      $(".nav-sub", this).addClass("fast");
      $(".nav-sub", this).show();
      $(".nav-sub", this).animateCss("show-sub");
    })
    .on("mouseleave.ariaNavSub", function () {
      $(".nav-sub", this).hide();
    });
}

function closeNav() {
  if ($("#nav-vertical").hasClass("nav-open")) {
    $("#nav-vertical").removeClass("nav-open");
    $("#wrapper").removeClass("wrapper-open");
  }

  return !1;
}

function search() {
  if ($("#search-box").css("display") === "flex") {
    $("#search-box").css("display", "none");
  }

  $("#nav-search-btn")
    .off("click.ariaSearch")
    .on("click.ariaSearch", function () {
      $("#search-box").css("display", "flex");
      $("#search-box").animateCss("zoomIn", function () {});
    });

  $("#search-box>.close")
    .off("click.ariaSearch")
    .on("click.ariaSearch", function () {
      $("#search-box").hide();
    });
}

function bindActions() {
  $(document)
    .off("click.ariaToggleNav", "[data-aria-action='toggle-nav']")
    .on("click.ariaToggleNav", "[data-aria-action='toggle-nav']", function (event) {
      event.preventDefault();
      Aria.helpers.toggleNav();
    });

  $(document)
    .off("click.ariaGoTop", "[data-aria-action='go-top']")
    .on("click.ariaGoTop", "[data-aria-action='go-top']", function (event) {
      event.preventDefault();
      Aria.helpers.goTop(this);
    });

  $(document)
    .off("click.ariaPostOther", "[data-aria-action='toggle-post-other']")
    .on(
      "click.ariaPostOther",
      "[data-aria-action='toggle-post-other']",
      function (event) {
        event.preventDefault();
        Aria.helpers.togglePostOther(this);
      },
    );

  $(document)
    .off("click.ariaCommentImage", "[data-aria-action='insert-comment-image']")
    .on(
      "click.ariaCommentImage",
      "[data-aria-action='insert-comment-image']",
      function () {
        var textarea = document.getElementById("textarea");
        if (!textarea) {
          return;
        }

        textarea.value += "![图片描述](图片地址)";
      },
    );
}

Aria.action = Aria.action || {};
Aria.action.init = function () {
  headroom();
  gotop();
  closeNav();
  nav();
  search();
  bindActions();
  new WOW().init();
};
Aria.action.closeNav = function () {
  return closeNav();
};
