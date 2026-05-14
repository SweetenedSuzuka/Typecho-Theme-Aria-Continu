var Aria = (window.Aria = window.Aria || {});

$.extend(Aria, {
  init: function () {
    this.action.init();

    if (THEME_CONFIG.ENABLE_PJAX) {
      this.pjax();
    }
    if (THEME_CONFIG.ENABLE_FANCYBOX) {
      this.fancybox();
    }
    if (THEME_CONFIG.SHOW_HITOKOTO) {
      this.hitokoto();
    }

    this.hljs.init();
    this.commentPlus.init();

    if (THEME_CONFIG.ENABLE_LAZYLOAD) {
      this.lazyload();
    }

    this.toc.init();

    console.log(
      "%cVer " +
        THEME_CONFIG.THEME_VERSION +
        "%cAria By Siphils https://eriri.ink",
      "color: #fff; background: #435561; padding:6px;",
      "color: #fff; background: #435561cf; padding:6px;",
    );
  },

  pjax: function () {
    $(document)
      .pjax(
        'a[href^="' +
          THEME_CONFIG.SITE_URL +
          '"]:not(a[target="_blank"], [no-pjax],a[rel~="nofollow"])',
        {
          container: "#pjax-container",
          fragment: "#pjax-container",
          timeout: 8e3,
        },
      )
      .on("pjax:send", function () {
        NProgress.start();
        Aria.doPjaxStartAction();
      })
      .on("pjax:complete", function () {
        NProgress.done();
        Aria.doPjaxCompleteAction();
        if (typeof Aria.reloadAction === "function") {
          Aria.reloadAction();
        }
      });
  },

  doPjaxStartAction: function () {
    $("#header").toggleClass("slideOutUp");
    $("#body").toggleClass("fadeOut");
    $("#wrapper").hide();
  },

  doPjaxCompleteAction: function () {
    $("#header").removeClass("slideOutUp").addClass("slideInDown");
    $("#body").removeClass("fadeOut").addClass("fadeIn");

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

    if (typeof _hmt !== "undefined") {
      _hmt.push(["_trackPageview", location.pathname + location.search]);
    }
    if (window._gaq) {
      _gaq.push(["_trackPageview"]);
    }
    if (window.ga) {
      ga("send", "pageview", {
        page: location.pathname,
        title: document.title,
      });
    }
    if (THEME_CONFIG.ENABLE_MATHJAX && typeof MathJax !== "undefined") {
      MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
    }
    if (typeof loadMeting === "function") {
      loadMeting();
    }

    if (document.getElementsByClassName("dplayer").length) {
      for (var total = dPlayerOptions.length, players = [], i = 0; i < total; i++) {
        players.push(
          new DPlayer({
            container: document.getElementById("player" + dPlayerOptions[i].id),
            autoplay: dPlayerOptions[i].autoplay,
            theme: dPlayerOptions[i].theme,
            loop: dPlayerOptions[i].loop,
            lang: dPlayerOptions[i].lang,
            screenshot: dPlayerOptions[i].screenshot,
            hotkey: dPlayerOptions[i].hotkey,
            preload: dPlayerOptions[i].preload,
            logo: dPlayerOptions[i].logo,
            volume: dPlayerOptions[i].volume,
            mutex: dPlayerOptions[i].mutex,
            video: dPlayerOptions[i].video,
            subtitle: dPlayerOptions[i].subtitle,
            danmaku: dPlayerOptions[i].danmaku,
          }),
        );
      }
    }

    this.action.closeNav();
  },

  fancybox: function () {
    if (!$(".post-content img").length && !$(".comment-content img").length) {
      return;
    }

    $("img:not([class~='link-avatar'],[no-fancybox])", ".post-content").wrap(
      function () {
        return (
          '<a href="' +
          this.src +
          '" data-caption="' +
          this.title +
          '" no-pjax class="fancybox" data-fancybox="gallery" style="outline:0"></a>'
        );
      },
    );

    $("img", ".comment-text").wrap(function () {
      return (
        '<a href="' +
        this.src +
        '" data-caption="' +
        this.title +
        '" no-pjax class="fancybox" style="outline:0"></a>'
      );
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
        $("#hitokoto").html(text);
      },
    });
  },

  hljs: {
    init: function () {
      $("pre code").each(function (index, element) {
        hljs.highlightBlock(element);
        $(element).attr({ id: "hljs-" + index });

        var match = $(this).attr("class").match(/lang-(\w+)/);
        var language = match == null ? "CODE" : match[1].toUpperCase();

        $(this).attr("data-lang", language);
        $(this).after(
          '<a class="copy-code" href="javascript:" data-clipboard-target="#hljs-' +
            index +
            '" title="拷贝代码"><i class="iconfont icon-aria-copy"></i></a>',
        );
      });

      hljs.initLineNumbersOnLoad();
      this.clipboard();
    },

    clipboard: function () {
      var clipboard = new ClipboardJS(".copy-code");
      var notifier = new Notyf({ delay: 3e3 });

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
      $(this).attr("data-original", $(this).attr("src"));
      $(this).attr("src", THEME_CONFIG.THEME_URL + "/assets/img/loading.svg");
    });

    $(".lazyload").lazyload({ effect: "fadeIn" });
    $("img:not([no-lazyload])").lazyload({ effect: "fadeIn" });
  },
});
