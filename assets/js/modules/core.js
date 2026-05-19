var Aria = (window.Aria = window.Aria || {});

function getLazyloadPlaceholderUrl() {
  return THEME_CONFIG.THEME_URL + "/assets/img/loading.svg";
}

function escapeCssUrl(url) {
  return String(url).replace(/\\/g, "\\\\").replace(/"/g, '\\"');
}

function loadLazyImage(image) {
  var source = image.getAttribute("data-aria-lazy-src");
  var placeholderUrl = getLazyloadPlaceholderUrl();

  if (!source || image.getAttribute("data-aria-lazy-loaded") === "true") {
    return;
  }

  image.setAttribute("data-aria-lazy-loaded", "true");
  image.addEventListener(
    "load",
    function () {
      image.classList.remove("aria-lazy-pending");
      image.classList.add("aria-lazy-loaded");
    },
    { once: true },
  );
  image.addEventListener(
    "error",
    function () {
      image.classList.remove("aria-lazy-loaded");
      image.classList.remove("aria-lazy-pending");
      image.classList.add("aria-lazy-error");
      image.src = placeholderUrl;
    },
    { once: true },
  );
  image.src = source;
}

function loadLazyBackground(element) {
  var backgroundUrl = element.getAttribute("data-aria-lazy-background");
  var preloader;

  if (!backgroundUrl || element.getAttribute("data-aria-lazy-loaded") === "true") {
    return;
  }

  element.setAttribute("data-aria-lazy-loaded", "true");
  preloader = new Image();
  preloader.decoding = "async";
  preloader.onload = function () {
    element.style.backgroundImage = 'url("' + escapeCssUrl(backgroundUrl) + '")';
    element.classList.remove("aria-lazy-pending");
    element.classList.add("aria-lazy-loaded");
  };
  preloader.onerror = function () {
    element.classList.remove("aria-lazy-loaded");
    element.classList.remove("aria-lazy-pending");
    element.classList.add("aria-lazy-error");
  };
  preloader.src = backgroundUrl;
}

function prepareLazyImages() {
  var placeholderUrl = getLazyloadPlaceholderUrl();
  var images = document.querySelectorAll("img:not([no-lazyload])");
  var pending = [];

  Array.prototype.forEach.call(images, function (image) {
    var source = image.getAttribute("data-aria-lazy-src") || image.getAttribute("src");

    if (
      image.getAttribute("data-aria-lazy-bound") === "true" ||
      !source ||
      source === placeholderUrl
    ) {
      return;
    }

    image.setAttribute("data-aria-lazy-bound", "true");
    image.setAttribute("data-aria-lazy-src", source);
    image.setAttribute("loading", "lazy");
    image.setAttribute("decoding", "async");
    image.setAttribute("fetchpriority", "low");
    image.classList.add("aria-lazy-image", "aria-lazy-pending");
    image.src = placeholderUrl;
    pending.push(image);
  });

  return pending;
}

function prepareLazyBackgrounds() {
  var elements = document.querySelectorAll("[data-aria-lazy-background]");
  var pending = [];

  Array.prototype.forEach.call(elements, function (element) {
    if (
      element.getAttribute("data-aria-lazy-bound") === "true" ||
      !element.getAttribute("data-aria-lazy-background")
    ) {
      return;
    }

    element.setAttribute("data-aria-lazy-bound", "true");
    element.classList.add("aria-lazy-background", "aria-lazy-pending");
    pending.push(element);
  });

  return pending;
}

function observeLazyTargets(targets) {
  var observer;

  if (!targets.length) {
    return;
  }

  if (typeof window.IntersectionObserver !== "function") {
    Array.prototype.forEach.call(targets, function (target) {
      if (target.hasAttribute("data-aria-lazy-src")) {
        loadLazyImage(target);
        return;
      }

      if (target.hasAttribute("data-aria-lazy-background")) {
        loadLazyBackground(target);
      }
    });
    return;
  }

  if (!Aria.state.lazyObserver) {
    Aria.state.lazyObserver = new window.IntersectionObserver(
      function (entries, currentObserver) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }

          if (entry.target.hasAttribute("data-aria-lazy-src")) {
            loadLazyImage(entry.target);
          } else if (entry.target.hasAttribute("data-aria-lazy-background")) {
            loadLazyBackground(entry.target);
          }

          currentObserver.unobserve(entry.target);
        });
      },
      {
        rootMargin: "120px 0px",
        threshold: 0.01,
      },
    );
  }

  observer = Aria.state.lazyObserver;
  Array.prototype.forEach.call(targets, function (target) {
    observer.observe(target);
  });
}

function logVersion() {
  if (Aria.state.versionLogged) {
    return;
  }

  Aria.state.versionLogged = !0;
  console.log(
    "%cVer " +
      THEME_CONFIG.THEME_VERSION +
      "%cAria Continuo By SweetenedSuzuka",
    "color: #fff; background: #435561; padding:6px;",
    "color: #fff; background: #435561cf; padding:6px;",
  );
  console.log("%cBased on Aria By Siphils", "color: #fff; background: #435561cf; padding:6px;");
}

$.extend(Aria, {
  init: function () {
    if (this.state.initialized) {
      this.refresh();
      return;
    }

    this.state.initialized = !0;
    this.action.init();
    this.refresh();
    logVersion();
  },

  refresh: function () {
    if (THEME_CONFIG.ENABLE_FANCYBOX) {
      this.fancybox();
    }
    if (THEME_CONFIG.SHOW_HITOKOTO) {
      this.hitokoto();
    }
    if (THEME_CONFIG.ENABLE_LAZYLOAD) {
      this.lazyload();
    }

    this.hljs.init();
    this.commentPlus.init();
    this.toc.init();
  },

  fancybox: function () {
    if (!$(".post-content img").length && !$(".comment-content img").length) {
      return;
    }

    $("img:not([class~='link-avatar'],[no-fancybox])", ".post-content")
      .not("[data-aria-fancybox-bound]")
      .attr("data-aria-fancybox-bound", "true")
      .wrap(function () {
        var anchor = document.createElement("a");
        anchor.href = this.src;
        anchor.setAttribute("data-caption", this.title || "");
        anchor.className = "fancybox";
        anchor.setAttribute("data-fancybox", "gallery");
        anchor.style.outline = "0";
        return anchor;
      });

    $("img", ".comment-text")
      .not("[data-aria-fancybox-bound]")
      .attr("data-aria-fancybox-bound", "true")
      .wrap(function () {
        var anchor = document.createElement("a");
        anchor.href = this.src;
        anchor.setAttribute("data-caption", this.title || "");
        anchor.className = "fancybox";
        anchor.style.outline = "0";
        return anchor;
      });

    $("a.fancybox").fancybox({
      animationEffect: "zoom-in-out",
      animationDuration: 500,
      transitionEffect: "tube",
      transitionDuration: 500,
      spinnerTpl:
        '<img style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);" src="' +
        THEME_CONFIG.THEME_URL +
        '/assets/img/loading.svg">',
    });
  },

  hitokoto: function () {
    $.ajax({
      type: "GET",
      url: THEME_CONFIG.HITOKOTO_ORIGIN,
      success: function (text) {
        $("#hitokoto").text(text);
      },
    });
  },

  hljs: {
    init: function () {
      $("pre code").each(function (index, element) {
        if ($(element).attr("data-aria-hljs-bound") === "true") {
          return;
        }

        var shouldAddLineNumbers = !$(element).closest(".comment-text").length;

        $(element).attr("data-aria-hljs-bound", "true");
        hljs.highlightBlock(element);
        if (
          shouldAddLineNumbers &&
          typeof hljs.lineNumbersBlock === "function" &&
          $(element).attr("data-aria-hljs-lines-bound") !== "true"
        ) {
          $(element).attr("data-aria-hljs-lines-bound", "true");
          hljs.lineNumbersBlock(element);
        }
        $(element).attr({ id: "hljs-" + index });

        var match = $(this).attr("class").match(/lang-(\w+)/);
        var language = match == null ? "CODE" : match[1].toUpperCase();

        $(this).attr("data-lang", language);
        if (!$(this).next(".copy-code").length) {
          $(this).after(
            '<a class="copy-code" href="javascript:" data-clipboard-target="#hljs-' +
              index +
              '" title="拷贝代码"><i class="iconfont icon-aria-copy"></i></a>',
          );
        }
      });
      this.clipboard();
    },

    clipboard: function () {
      if (Aria.state.clipboard) {
        Aria.state.clipboard.destroy();
      }

      var clipboard = new ClipboardJS(".copy-code");
      Aria.state.clipboard = clipboard;

      clipboard.on("success", function (event) {
        Aria.notify.success("代码成功拷贝到剪贴板！");
        event.clearSelection();
      });

      clipboard.on("error", function () {
        Aria.notify.error("代码拷贝失败！");
      });
    },
  },

  lazyload: function () {
    observeLazyTargets(prepareLazyImages().concat(prepareLazyBackgrounds()));
  },
});
