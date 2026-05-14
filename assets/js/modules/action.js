var Aria = (window.Aria = window.Aria || {});

Aria.action = {
  init: function () {
    this.headroom();
    this.gotop();
    this.closeNav();
    this.nav();
    this.search();
    new WOW().init();
  },

  headroom: function () {
    var navigation = document.querySelector("#nav-menu");
    new Headroom(navigation).init();
  },

  gotop: function () {
    $(window).scroll(function () {
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
  },

  nav: function () {
    $(".nav-right-item").hover(
      function () {
        $(".nav-sub", this).addClass("fast");
        $(".nav-sub", this).show();
        $(".nav-sub", this).animateCss("show-sub");
      },
      function () {
        $(".nav-sub", this).hide();
      },
    );
  },

  closeNav: function () {
    if ($("#nav-vertical").hasClass("nav-open")) {
      $("#nav-vertical").removeClass("nav-open");
      $("#wrapper").removeClass("wrapper-open");
    }

    return !1;
  },

  search: function () {
    if ($("#search-box").css("display") === "flex") {
      $("#search-box").css("display", "none");
    }

    $("#nav-search-btn").on("click", function () {
      $("#search-box").css("display", "flex");
      $("#search-box").animateCss("zoomIn", function () {});
    });

    $("#search-box>.close").on("click", function () {
      $("#search-box").hide();
    });
  },
};
