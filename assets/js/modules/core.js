var Aria = (window.Aria = window.Aria || {});

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
      var notifier = new Notyf({ delay: 3e3 });
      Aria.state.clipboard = clipboard;

      clipboard.on("success", function (event) {
        notifier.confirm("代码成功拷贝到剪贴板！");
        event.clearSelection();
      });

      clipboard.on("error", function () {
        notifier.alertL("代码拷贝失败！");
      });
    },
  },

  lazyload: function () {
    $("img:not([no-lazyload])").each(function () {
      if ($(this).attr("data-aria-lazyload-bound") === "true") {
        return;
      }

      $(this).attr("data-aria-lazyload-bound", "true");
      $(this).attr("data-original", $(this).attr("src"));
      $(this).attr("src", THEME_CONFIG.THEME_URL + "/assets/img/loading.svg");
    });

    $(".lazyload").lazyload({ effect: "fadeIn" });
    $("img:not([no-lazyload])").lazyload({ effect: "fadeIn" });
  },
});
