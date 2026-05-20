var Aria = (window.Aria = window.Aria || {});

function applyHeadroomState(navigation, currentScrollY, lastScrollY) {
  var scrollTop = currentScrollY > 0 ? currentScrollY : 0;
  var viewportBottom = scrollTop + window.innerHeight;
  var documentHeight = Math.max(
    document.body ? document.body.scrollHeight : 0,
    document.documentElement ? document.documentElement.scrollHeight : 0,
  );
  var isTop = scrollTop <= 0;
  var isBottom = viewportBottom >= documentHeight - 1;
  var isScrollingDown = scrollTop > lastScrollY;
  var isScrollingUp = scrollTop < lastScrollY;

  navigation.classList.toggle("headroom--top", isTop);
  navigation.classList.toggle("headroom--not-top", !isTop);
  navigation.classList.toggle("headroom--bottom", isBottom);
  navigation.classList.toggle("headroom--not-bottom", !isBottom);

  if (isTop || isScrollingUp) {
    navigation.classList.add("headroom--pinned");
    navigation.classList.remove("headroom--unpinned");
    return;
  }

  if (isScrollingDown) {
    navigation.classList.add("headroom--unpinned");
    navigation.classList.remove("headroom--pinned");
  }
}

function createHeadroomController(navigation) {
  var lastScrollY = window.pageYOffset || 0;

  navigation.classList.add("headroom");

  function handleScroll() {
    var currentScrollY = window.pageYOffset || 0;

    applyHeadroomState(navigation, currentScrollY, lastScrollY);
    lastScrollY = currentScrollY > 0 ? currentScrollY : 0;
  }

  $(window).off("scroll.ariaHeadroom").on("scroll.ariaHeadroom", handleScroll);
  handleScroll();

  return {
    destroy: function () {
      $(window).off("scroll.ariaHeadroom", handleScroll);
    },
  };
}

function headroom() {
  var navigation = document.querySelector("#nav-menu");

  if (!navigation) {
    return;
  }

  if (Aria.state.headroom && typeof Aria.state.headroom.destroy === "function") {
    Aria.state.headroom.destroy();
    Aria.state.headroom = null;
  }

  navigation.classList.remove(
    "headroom",
    "headroom--pinned",
    "headroom--unpinned",
    "headroom--top",
    "headroom--not-top",
    "headroom--bottom",
    "headroom--not-bottom",
  );

  if (!THEME_CONFIG.ENABLE_NAV_HEADROOM) {
    return;
  }

  Aria.state.headroom = createHeadroomController(navigation);
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

function setWowAnimationStyles(element) {
  var duration = element.getAttribute("data-wow-duration");
  var delay = element.getAttribute("data-wow-delay");
  var iteration = element.getAttribute("data-wow-iteration");

  if (duration) {
    element.style.animationDuration = duration;
    element.style.webkitAnimationDuration = duration;
  }

  if (delay) {
    element.style.animationDelay = delay;
    element.style.webkitAnimationDelay = delay;
  }

  if (iteration) {
    element.style.animationIterationCount = iteration;
    element.style.webkitAnimationIterationCount = iteration;
  }
}

function revealWowElement(element) {
  if (!element || element.getAttribute("data-aria-wow-revealed") === "true") {
    return;
  }

  element.setAttribute("data-aria-wow-revealed", "true");
  element.style.visibility = "visible";

  if (
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches
  ) {
    element.classList.remove("animated");
    return;
  }

  element.classList.add("animated");
}

function isWowVisible(element) {
  var offset = parseInt(element.getAttribute("data-wow-offset") || "0", 10);
  var rect = element.getBoundingClientRect();
  var viewportHeight = window.innerHeight || document.documentElement.clientHeight || 0;

  if (isNaN(offset)) {
    offset = 0;
  }

  return rect.top <= viewportHeight - offset && rect.bottom >= 0;
}

function createWowController() {
  function handleIntersect(entries, observer) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting && !isWowVisible(entry.target)) {
        return;
      }

      revealWowElement(entry.target);
      observer.unobserve(entry.target);
    });
  }

  if (typeof window.IntersectionObserver === "function") {
    Aria.state.wowObserver = new window.IntersectionObserver(handleIntersect, {
      threshold: 0,
    });
  } else {
    Aria.state.wowObserver = null;
  }

  return {
    observe: function (element) {
      if (Aria.state.wowObserver) {
        Aria.state.wowObserver.observe(element);
        return;
      }

      revealWowElement(element);
    },
    destroy: function () {
      if (Aria.state.wowObserver) {
        Aria.state.wowObserver.disconnect();
        Aria.state.wowObserver = null;
      }
    },
  };
}

function initWowAnimations() {
  var elements = document.querySelectorAll(".wow");

  if (Aria.state.wowController && typeof Aria.state.wowController.destroy === "function") {
    Aria.state.wowController.destroy();
    Aria.state.wowController = null;
  }

  if (!elements.length) {
    return;
  }

  Aria.state.wowController = createWowController();
  Array.prototype.forEach.call(elements, function (element) {
    setWowAnimationStyles(element);
    element.style.visibility = "hidden";
    element.classList.remove("animated");
    element.setAttribute("data-aria-wow-revealed", "false");
    Aria.state.wowController.observe(element);
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
  initWowAnimations();
};
Aria.action.closeNav = function () {
  return closeNav();
};
