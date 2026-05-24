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
  var scrollOptions = { passive: !0 };

  navigation.classList.add("headroom");

  function handleScroll() {
    var currentScrollY = window.pageYOffset || 0;

    applyHeadroomState(navigation, currentScrollY, lastScrollY);
    lastScrollY = currentScrollY > 0 ? currentScrollY : 0;
  }

  window.addEventListener("scroll", handleScroll, scrollOptions);
  handleScroll();

  return {
    destroy: function () {
      window.removeEventListener("scroll", handleScroll, scrollOptions);
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

function parseContrastColor(value) {
  var match;
  var hex;
  var alphaHex;

  if (!value || value === "transparent") {
    return null;
  }

  if (value.indexOf("rgb") === 0) {
    match = value.match(/rgba?\(([^)]+)\)/);
    if (!match) {
      return null;
    }

    match = match[1].split(",").map(function (part) {
      return parseFloat(part.trim());
    });

    return {
      r: match[0],
      g: match[1],
      b: match[2],
      a: typeof match[3] === "number" && !isNaN(match[3]) ? match[3] : 1,
    };
  }

  if (value.charAt(0) === "#") {
    hex = value.slice(1);
    if (hex.length === 3 || hex.length === 4) {
      hex = hex.split("").map(function (digit) {
        return digit + digit;
      }).join("");
    }

    if (hex.length !== 6 && hex.length !== 8) {
      return null;
    }

    alphaHex = hex.length === 8 ? parseInt(hex.slice(6, 8), 16) / 255 : 1;
    return {
      r: parseInt(hex.slice(0, 2), 16),
      g: parseInt(hex.slice(2, 4), 16),
      b: parseInt(hex.slice(4, 6), 16),
      a: alphaHex,
    };
  }

  return null;
}

function getContrastLuminance(color) {
  function normalize(channel) {
    var srgb = channel / 255;
    return srgb <= 0.03928 ? srgb / 12.92 : Math.pow((srgb + 0.055) / 1.055, 2.4);
  }

  return 0.2126 * normalize(color.r) + 0.7152 * normalize(color.g) + 0.0722 * normalize(color.b);
}

function getContrastImageState(src) {
  var cache = Aria.state.adaptiveInkImageCache || (Aria.state.adaptiveInkImageCache = {});
  var entry = cache[src];

  if (entry) {
    return entry;
  }

  entry = {
    image: new Image(),
    ready: !1,
    failed: !1,
  };

  entry.image.crossOrigin = "anonymous";
  entry.image.decoding = "async";
  entry.image.onload = function () {
    entry.ready = !0;
    scheduleAdaptiveNavInkUpdate();
  };
  entry.image.onerror = function () {
    entry.failed = !0;
  };
  entry.image.src = src;
  cache[src] = entry;
  return entry;
}

function getContrastCanvasContext() {
  var canvas = Aria.state.adaptiveInkCanvas;
  if (!canvas) {
    canvas = document.createElement("canvas");
    canvas.width = 1;
    canvas.height = 1;
    Aria.state.adaptiveInkCanvas = canvas;
    Aria.state.adaptiveInkContext = canvas.getContext("2d", { willReadFrequently: !0 });
  }
  return Aria.state.adaptiveInkContext;
}

function sampleContrastImage(entry, imageX, imageY) {
  var context;
  var data;
  var clampedX;
  var clampedY;

  if (!entry || !entry.ready || entry.failed) {
    return null;
  }

  context = getContrastCanvasContext();
  if (!context) {
    return null;
  }

  clampedX = Math.max(0, Math.min(entry.image.naturalWidth - 1, Math.round(imageX)));
  clampedY = Math.max(0, Math.min(entry.image.naturalHeight - 1, Math.round(imageY)));

  try {
    context.clearRect(0, 0, 1, 1);
    context.drawImage(entry.image, clampedX, clampedY, 1, 1, 0, 0, 1, 1);
    data = context.getImageData(0, 0, 1, 1).data;
    return {
      r: data[0],
      g: data[1],
      b: data[2],
      a: data[3] / 255,
    };
  } catch (error) {
    entry.failed = !0;
    return null;
  }
}

function mapCoverSamplePoint(containerWidth, containerHeight, imageWidth, imageHeight, xRatio, yRatio, fitMode) {
  var scale;
  var renderedWidth;
  var renderedHeight;
  var offsetX;
  var offsetY;

  fitMode = fitMode === "contain" ? "contain" : "cover";
  scale = fitMode === "contain"
    ? Math.min(containerWidth / imageWidth, containerHeight / imageHeight)
    : Math.max(containerWidth / imageWidth, containerHeight / imageHeight);

  renderedWidth = imageWidth * scale;
  renderedHeight = imageHeight * scale;
  offsetX = (containerWidth - renderedWidth) / 2;
  offsetY = (containerHeight - renderedHeight) / 2;

  return {
    x: ((xRatio * containerWidth) - offsetX) / renderedWidth * imageWidth,
    y: ((yRatio * containerHeight) - offsetY) / renderedHeight * imageHeight,
  };
}

function extractBackgroundImageUrl(backgroundImage) {
  var match;

  if (!backgroundImage || backgroundImage === "none") {
    return null;
  }

  match = backgroundImage.match(/url\((['"]?)(.*?)\1\)/);
  return match ? match[2] : null;
}

function sampleImageElementContrast(element, x, y) {
  var rect = element.getBoundingClientRect();
  var entry;
  var point;
  var fitMode;

  if (!rect.width || !rect.height || !element.currentSrc && !element.src) {
    return null;
  }

  entry = getContrastImageState(element.currentSrc || element.src);
  if (!entry.ready || entry.failed || !entry.image.naturalWidth || !entry.image.naturalHeight) {
    return null;
  }

  fitMode = window.getComputedStyle(element).objectFit || "cover";
  point = mapCoverSamplePoint(
    rect.width,
    rect.height,
    entry.image.naturalWidth,
    entry.image.naturalHeight,
    (x - rect.left) / rect.width,
    (y - rect.top) / rect.height,
    fitMode
  );

  return sampleContrastImage(entry, point.x, point.y);
}

function sampleBackgroundImageContrast(element, style, x, y) {
  var src = extractBackgroundImageUrl(style.backgroundImage);
  var rect = element.getBoundingClientRect();
  var entry;
  var sizeMode;
  var point;

  if (!src || !rect.width || !rect.height) {
    return null;
  }

  entry = getContrastImageState(src);
  if (!entry.ready || entry.failed || !entry.image.naturalWidth || !entry.image.naturalHeight) {
    return null;
  }

  sizeMode = style.backgroundSize === "contain" ? "contain" : "cover";
  point = mapCoverSamplePoint(
    rect.width,
    rect.height,
    entry.image.naturalWidth,
    entry.image.naturalHeight,
    (x - rect.left) / rect.width,
    (y - rect.top) / rect.height,
    sizeMode
  );

  return sampleContrastImage(entry, point.x, point.y);
}

function shouldIgnoreContrastElement(element, ignoredElements) {
  if (!element) {
    return !0;
  }

  if (element.id === "aria-optical-surfaces" || element.closest("#aria-optical-surfaces")) {
    return !0;
  }

  return ignoredElements.some(function (ignored) {
    return ignored && (ignored === element || ignored.contains(element));
  });
}

function resolveContrastColorAtPoint(x, y, ignoredElements) {
  var stack = typeof document.elementsFromPoint === "function"
    ? document.elementsFromPoint(x, y)
    : [document.elementFromPoint(x, y)];
  var fallback = parseContrastColor(window.getComputedStyle(document.body).backgroundColor) || { r: 242, g: 242, b: 242, a: 1 };
  var softFallback = null;

  stack.some(function (element) {
    var style;
    var backgroundColor;
    var sampledImageColor;

    if (!element || shouldIgnoreContrastElement(element, ignoredElements)) {
      return !1;
    }

    style = window.getComputedStyle(element);
    if (
      style.display === "none" ||
      style.visibility === "hidden" ||
      parseFloat(style.opacity || "1") <= 0
    ) {
      return !1;
    }

    if (element.tagName === "IMG") {
      sampledImageColor = sampleImageElementContrast(element, x, y);
      if (sampledImageColor) {
        fallback = sampledImageColor;
        return !0;
      }
    }

    sampledImageColor = sampleBackgroundImageContrast(element, style, x, y);
    if (sampledImageColor) {
      fallback = sampledImageColor;
      return !0;
    }

    backgroundColor = parseContrastColor(style.backgroundColor);
    if (!backgroundColor || backgroundColor.a <= 0.03) {
      return !1;
    }

    if (backgroundColor.a >= 0.4) {
      fallback = backgroundColor;
      return !0;
    }

    softFallback = backgroundColor;
    return !1;
  });

  return softFallback || fallback;
}

function getAverageContrastTone(points, ignoredElements) {
  var luminanceTotal = 0;

  if (!points.length) {
    return "dark";
  }

  points.forEach(function (point) {
    luminanceTotal += getContrastLuminance(
      resolveContrastColorAtPoint(point.x, point.y, ignoredElements),
    );
  });

  return luminanceTotal / points.length < 0.48 ? "light" : "dark";
}

function applyNavInkTone(element, classPrefix, tone) {
  if (!element) {
    return;
  }

  element.classList.remove(classPrefix + "-light", classPrefix + "-dark");
  element.classList.add(classPrefix + "-" + tone);
}

function updateAdaptiveNavInk() {
  var navigation = document.getElementById("nav-menu");
  var openSubmenus;
  var navRect;
  var navPoints;

  if (!document.body.classList.contains("aria-style-aria-continuo") || !navigation) {
    return;
  }

  navRect = navigation.getBoundingClientRect();
  navPoints = [0.14, 0.32, 0.5, 0.68, 0.86].map(function (ratio) {
    return {
      x: navRect.left + navRect.width * ratio,
      y: navRect.top + Math.min(navRect.height - 8, Math.max(16, navRect.height * 0.5)),
    };
  });

  applyNavInkTone(
    navigation,
    "aria-nav-tone",
    getAverageContrastTone(navPoints, [navigation]),
  );

  openSubmenus = navigation.querySelectorAll(".nav-sub.show-sub");
  Array.prototype.forEach.call(openSubmenus, function (submenu) {
    var rect = submenu.getBoundingClientRect();
    var xInset = Math.min(24, rect.width * 0.2);
    var yInset = Math.min(14, rect.height * 0.2);
    var submenuPoints = [
      { x: rect.left + xInset, y: rect.top + yInset },
      { x: rect.left + rect.width * 0.5, y: rect.top + yInset },
      { x: rect.right - xInset, y: rect.top + yInset },
      { x: rect.left + rect.width * 0.32, y: rect.bottom - yInset },
      { x: rect.left + rect.width * 0.68, y: rect.bottom - yInset },
    ];

    applyNavInkTone(
      submenu,
      "aria-submenu-tone",
      getAverageContrastTone(submenuPoints, [navigation, submenu]),
    );
  });
}

function scheduleAdaptiveNavInkUpdate() {
  if (Aria.state.adaptiveNavInkFrame) {
    return;
  }

  Aria.state.adaptiveNavInkFrame = window.requestAnimationFrame(function () {
    Aria.state.adaptiveNavInkFrame = null;
    updateAdaptiveNavInk();
  });
}

function adaptiveNavInk() {
  var state = Aria.state.adaptiveNavInk || {};
  var navigation = document.getElementById("nav-menu");

  if (state.scrollHandler) {
    window.removeEventListener("scroll", state.scrollHandler);
    window.removeEventListener("resize", state.scrollHandler);
    window.removeEventListener("load", state.scrollHandler);
  }

  state.scrollHandler = null;

  if (!document.body.classList.contains("aria-style-aria-continuo") || !navigation) {
    Aria.state.adaptiveNavInk = state;
    return;
  }

  state.scrollHandler = function () {
    scheduleAdaptiveNavInkUpdate();
  };

  window.addEventListener("scroll", state.scrollHandler, { passive: !0 });
  window.addEventListener("resize", state.scrollHandler);
  window.addEventListener("load", state.scrollHandler);
  Aria.state.adaptiveNavInk = state;
  scheduleAdaptiveNavInkUpdate();
}

function gotop() {
  var scrollOptions = { passive: !0 };
  var visibleOffset = 100;
  var goTopButton = document.getElementById("go-top");
  var siteAvatar = document.getElementById("site-avatar");

  function setGoTopButtonVisible(visible) {
    if (!goTopButton) {
      return;
    }

    if (Aria.state.goTopVisible === visible) {
      return;
    }

    Aria.state.goTopVisible = visible;
    goTopButton.classList.toggle("go-top-visible", visible);
    goTopButton.setAttribute("aria-hidden", visible ? "false" : "true");
  }

  function setSiteAvatarCompact(compact) {
    if (!siteAvatar || Aria.state.siteAvatarCompact === compact) {
      return;
    }

    Aria.state.siteAvatarCompact = compact;
    siteAvatar.style.height = compact ? "25px" : "35px";
    siteAvatar.style.width = compact ? "25px" : "35px";
    siteAvatar.style.margin = compact ? "19.5px 5px 0 0" : "14.5px 5px 0 0";
  }

  function updateActiveTocLink(currentTop) {
    var toc = document.getElementById("toc");
    var titleIds = (Aria.toc && Aria.toc.titleId) || [];
    var currentAnchorId = null;
    var currentAnchorTop = -Infinity;

    if (!toc || !titleIds.length) {
      return;
    }

    titleIds.forEach(function (titleId) {
      var target = document.getElementById(titleId);
      var targetTop;

      if (!target) {
        return;
      }

      targetTop = target.getBoundingClientRect().top + window.pageYOffset;
      if (targetTop > currentTop + 100 || targetTop < currentAnchorTop) {
        return;
      }

      currentAnchorId = titleId;
      currentAnchorTop = targetTop;
    });

    var hasActive = false;

    Array.prototype.forEach.call(
      toc.querySelectorAll("a"),
      function (anchor) {
        var isActive = !!currentAnchorId && anchor.getAttribute("href") === "#" + currentAnchorId;
        anchor.classList.toggle("toc-active", isActive);
        
        // 动态更新 TOC 滑块的位置和高度
        if (isActive) {
          hasActive = true;
          var tocRect = toc.getBoundingClientRect();
          var anchorRect = anchor.getBoundingClientRect();
          // 计算 anchor 相对 toc padding-box 的精确 top
          var relativeTop = anchorRect.top - (tocRect.top + toc.clientTop) + toc.scrollTop;
          // 稍微扩展滑块高度，使其更好地包裹文字
          toc.style.setProperty("--toc-marker-top", (relativeTop - 2) + "px");
          toc.style.setProperty("--toc-marker-height", (anchorRect.height + 4) + "px");
        }
      },
    );

    // 兼容不支持 :has() 的浏览器
    toc.classList.toggle("has-active", hasActive);
  }

  function handleScroll() {
    var currentTop =
      window.pageYOffset ||
      document.documentElement.scrollTop ||
      document.body.scrollTop ||
      0;

    setGoTopButtonVisible(currentTop > visibleOffset);
    setSiteAvatarCompact(currentTop > visibleOffset);
    updateActiveTocLink(currentTop);
  }

  if (Aria.state.goTopScrollHandler) {
    window.removeEventListener("scroll", Aria.state.goTopScrollHandler, scrollOptions);
  }

  Aria.state.goTopScrollHandler = handleScroll;
  window.addEventListener("scroll", handleScroll, scrollOptions);
  handleScroll();
}

function nav() {
  var hoverBindings = Aria.state.navHoverBindings || [];
  var continuoCloseDelay = 700;
  var continuoCloseAnimationMs = 420;

  if (Aria.optical && typeof Aria.optical.unregister === "function") {
    Aria.optical.unregister("nav-submenu");
  }

  hoverBindings.forEach(function (binding) {
    binding.element.removeEventListener("mouseenter", binding.enterHandler);
    binding.element.removeEventListener("mouseleave", binding.leaveHandler);
    if (binding.submenu) {
      binding.submenu.removeEventListener(
        "mouseenter",
        binding.submenuEnterHandler,
      );
      binding.submenu.removeEventListener(
        "mouseleave",
        binding.submenuLeaveHandler,
      );
    }
    if (binding.submenu && binding.submenu._ariaCloseTimer) {
      window.clearTimeout(binding.submenu._ariaCloseTimer);
      binding.submenu._ariaCloseTimer = null;
    }
    if (binding.submenu && binding.submenu._ariaExitTimer) {
      window.clearTimeout(binding.submenu._ariaExitTimer);
      binding.submenu._ariaExitTimer = null;
    }
    if (binding.submenu && binding.submenu._ariaEnterFrame) {
      window.cancelAnimationFrame(binding.submenu._ariaEnterFrame);
      binding.submenu._ariaEnterFrame = null;
    }
  });

  Aria.state.navHoverBindings = [];

  var isContinuo = document.body.classList.contains("aria-style-aria-continuo");
  var activeContinuoSubmenu = null;

  function syncContinuoSubmenuSurface(submenu) {
    if (!isContinuo || !Aria.optical || typeof Aria.optical.register !== "function") {
      return;
    }

    if (!submenu) {
      if (typeof Aria.optical.unregister === "function") {
        Aria.optical.unregister("nav-submenu");
      }
      return;
    }

    Aria.optical.register("nav-submenu", {
      host: submenu,
      sourceRoot: submenu,
      variant: "nav-submenu",
      mirroredClasses: ["show-sub", "aria-submenu-entered", "aria-submenu-closing"],
    });
  }

  function clearContinuoCloseTimer(submenu) {
    if (!submenu || !submenu._ariaCloseTimer) {
      return;
    }

    window.clearTimeout(submenu._ariaCloseTimer);
    submenu._ariaCloseTimer = null;
  }

  function clearContinuoAnimationTimers(submenu) {
    if (!submenu) {
      return;
    }

    clearContinuoCloseTimer(submenu);

    if (submenu._ariaExitTimer) {
      window.clearTimeout(submenu._ariaExitTimer);
      submenu._ariaExitTimer = null;
    }

    if (submenu._ariaEnterFrame) {
      window.cancelAnimationFrame(submenu._ariaEnterFrame);
      submenu._ariaEnterFrame = null;
    }
  }

  function openContinuoSubmenu(submenu) {
    if (!submenu) {
      return;
    }

    clearContinuoAnimationTimers(submenu);
    submenu.classList.add("show-sub");
    submenu.classList.remove("aria-submenu-closing");

    submenu._ariaEnterFrame = window.requestAnimationFrame(function () {
      submenu._ariaEnterFrame = null;
      submenu.classList.add("aria-submenu-entered");
      scheduleAdaptiveNavInkUpdate();
    });
  }

  function finishContinuoSubmenuClose(submenu) {
    if (!submenu) {
      return;
    }

    submenu.classList.remove("show-sub", "aria-submenu-entered", "aria-submenu-closing");
    submenu.classList.remove("aria-submenu-tone-light", "aria-submenu-tone-dark");
    submenu._ariaExitTimer = null;

    if (activeContinuoSubmenu === submenu) {
      activeContinuoSubmenu = null;
      syncContinuoSubmenuSurface(null);
    }

    scheduleAdaptiveNavInkUpdate();
  }

  function closeContinuoSubmenu(submenu) {
    if (!submenu) {
      return;
    }

    clearContinuoAnimationTimers(submenu);

    if (!submenu.classList.contains("show-sub")) {
      finishContinuoSubmenuClose(submenu);
      return;
    }

    submenu.classList.remove("aria-submenu-entered");
    submenu.classList.add("aria-submenu-closing");
    submenu._ariaExitTimer = window.setTimeout(function () {
      finishContinuoSubmenuClose(submenu);
    }, continuoCloseAnimationMs);
    scheduleAdaptiveNavInkUpdate();
  }

  function scheduleContinuoClose(submenu) {
    clearContinuoCloseTimer(submenu);
    submenu._ariaCloseTimer = window.setTimeout(function () {
      submenu._ariaCloseTimer = null;
      closeContinuoSubmenu(submenu);
    }, continuoCloseDelay);
  }

  Array.prototype.forEach.call(
    document.querySelectorAll(".nav-right-item"),
    function (element) {
      var submenu = element.querySelector(".nav-sub");

      Array.prototype.forEach.call(
        element.querySelectorAll(".sub-item"),
        function (subItem, index) {
          subItem.style.setProperty("--aria-sub-item-index", index);
        },
      );

      function handleMouseEnter() {
        if (!submenu) {
          return;
        }

        submenu.classList.add("fast");
        submenu.style.display = "block";

        if (isContinuo) {
          clearContinuoAnimationTimers(submenu);

          // 如果是从其他菜单移过来，立即收起其他菜单
          if (activeContinuoSubmenu && activeContinuoSubmenu !== submenu) {
            closeContinuoSubmenu(activeContinuoSubmenu);
          }

          if (
            submenu.classList.contains("show-sub") &&
            submenu.classList.contains("aria-submenu-entered") &&
            !submenu.classList.contains("aria-submenu-closing")
          ) {
            activeContinuoSubmenu = submenu;
            scheduleAdaptiveNavInkUpdate();
            return;
          }

          openContinuoSubmenu(submenu);
          activeContinuoSubmenu = submenu;
          syncContinuoSubmenuSurface(submenu);
          scheduleAdaptiveNavInkUpdate();
        } else {
          Aria.helpers.animateCss(submenu, "show-sub");
        }
      }

      function handleMouseLeave() {
        if (!submenu) {
          return;
        }

        if (isContinuo) {
          scheduleContinuoClose(submenu);
        } else {
          submenu.style.display = "none";
        }

        scheduleAdaptiveNavInkUpdate();
      }

      function handleSubmenuMouseEnter() {
        if (!isContinuo || !submenu) {
          return;
        }

        clearContinuoAnimationTimers(submenu);

        if (submenu.classList.contains("aria-submenu-closing")) {
          openContinuoSubmenu(submenu);
          activeContinuoSubmenu = submenu;
          syncContinuoSubmenuSurface(submenu);
        }

        scheduleAdaptiveNavInkUpdate();
      }

      function handleSubmenuMouseLeave() {
        if (!isContinuo || !submenu) {
          return;
        }

        scheduleContinuoClose(submenu);
      }

      element.addEventListener("mouseenter", handleMouseEnter);
      element.addEventListener("mouseleave", handleMouseLeave);
      if (submenu) {
        submenu.addEventListener("mouseenter", handleSubmenuMouseEnter);
        submenu.addEventListener("mouseleave", handleSubmenuMouseLeave);
      }
      Aria.state.navHoverBindings.push({
        element: element,
        enterHandler: handleMouseEnter,
        leaveHandler: handleMouseLeave,
        submenu: submenu,
        submenuEnterHandler: handleSubmenuMouseEnter,
        submenuLeaveHandler: handleSubmenuMouseLeave,
      });
    },
  );
}

function closeNav() {
  var navigation = document.getElementById("nav-vertical");
  var wrapper = document.getElementById("wrapper");

  if (navigation) {
    navigation.classList.remove("nav-open");
  }

  if (wrapper) {
    wrapper.classList.remove("wrapper-open");
    wrapper.style.display = "none";
  }

  return !1;
}

function search() {
  var searchBox = document.getElementById("search-box");
  var searchButton = document.getElementById("nav-search-btn");
  var searchCloseButton = document.querySelector("#search-box>.close");

  if (searchBox && window.getComputedStyle(searchBox).display === "flex") {
    searchBox.style.display = "none";
  }

  if (
    Aria.state.searchOpenTarget &&
    Aria.state.searchOpenHandler
  ) {
    Aria.state.searchOpenTarget.removeEventListener(
      "click",
      Aria.state.searchOpenHandler,
    );
  }

  if (
    Aria.state.searchCloseTarget &&
    Aria.state.searchCloseHandler
  ) {
    Aria.state.searchCloseTarget.removeEventListener(
      "click",
      Aria.state.searchCloseHandler,
    );
  }

  Aria.state.searchOpenHandler = function () {
    var currentSearchBox = document.getElementById("search-box");

    if (!currentSearchBox) {
      return;
    }

    currentSearchBox.style.display = "flex";
    Aria.helpers.animateCss(currentSearchBox, "zoomIn");
  };

  Aria.state.searchCloseHandler = function () {
    var currentSearchBox = document.getElementById("search-box");

    if (!currentSearchBox) {
      return;
    }

    currentSearchBox.style.display = "none";
  };

  Aria.state.searchOpenTarget = searchButton || null;
  Aria.state.searchCloseTarget = searchCloseButton || null;

  if (searchButton) {
    searchButton.addEventListener("click", Aria.state.searchOpenHandler);
  }

  if (searchCloseButton) {
    searchCloseButton.addEventListener("click", Aria.state.searchCloseHandler);
  }
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
  if (Aria.state.actionClickHandler) {
    document.removeEventListener("click", Aria.state.actionClickHandler);
  }

  Aria.state.actionClickHandler = function (event) {
    var actionTarget = event.target.closest("[data-aria-action]");
    var textarea;

    if (!actionTarget) {
      return;
    }

    switch (actionTarget.getAttribute("data-aria-action")) {
      case "toggle-nav":
        event.preventDefault();
        Aria.helpers.toggleNav();
        break;
      case "go-top":
        event.preventDefault();
        Aria.helpers.goTop(actionTarget);
        break;
      case "toggle-post-other":
        event.preventDefault();
        Aria.helpers.togglePostOther(actionTarget);
        break;
      case "insert-comment-image":
        textarea = document.getElementById("textarea");
        if (!textarea) {
          return;
        }

        textarea.value += "![图片描述](图片地址)";
        break;
      default:
        break;
    }
  };

  document.addEventListener("click", Aria.state.actionClickHandler);
}

function normalizePaginationLabels(selector) {
  function normalizeEllipsisLabel(listItem) {
    var ellipsisLabel = listItem.querySelector(".label");
    var dotIndex;
    var dot;

    if (!listItem.classList.contains("page-ellipsis") || !ellipsisLabel) {
      return;
    }

    if (ellipsisLabel.querySelector(".dot")) {
      return;
    }

    if (ellipsisLabel.textContent.trim() !== "...") {
      return;
    }

    ellipsisLabel.textContent = "";
    for (dotIndex = 0; dotIndex < 3; dotIndex += 1) {
      dot = document.createElement("span");
      dot.className = "dot";
      dot.textContent = ".";
      ellipsisLabel.appendChild(dot);
    }
  }

  Array.prototype.forEach.call(
    document.querySelectorAll(selector + " li"),
    function (listItem) {
      var currentLink;
      var item;
      var rawText;
      var label;
      var directSpan;

      if (listItem.classList.contains("page-current")) {
        currentLink = listItem.querySelector("a");
        if (currentLink) {
          rawText = currentLink.textContent ? currentLink.textContent.trim() : "";
          listItem.textContent = "";
          if (rawText) {
            label = document.createElement("span");
            label.className = "label";
            label.textContent = rawText;
            listItem.appendChild(label);
          }
        }
      }

      item = listItem.querySelector("a") || listItem;

      if (item.querySelector(".label")) {
        if (!listItem.querySelector("a") && !listItem.classList.contains("page-current")) {
          listItem.classList.add("page-ellipsis");
        }
        normalizeEllipsisLabel(listItem);
        return;
      }

      directSpan = item.querySelector(":scope > span");
      if (directSpan) {
        directSpan.classList.add("label");
        if (!listItem.querySelector("a") && !listItem.classList.contains("page-current")) {
          listItem.classList.add("page-ellipsis");
        }
        normalizeEllipsisLabel(listItem);
        return;
      }

      rawText = item.textContent;
      if (!rawText) {
        return;
      }

      label = document.createElement("span");
      label.className = "label";
      label.textContent = rawText.trim();
      item.textContent = "";
      item.appendChild(label);

      if (!listItem.querySelector("a") && !listItem.classList.contains("page-current")) {
        listItem.classList.add("page-ellipsis");
      }

      normalizeEllipsisLabel(listItem);
    },
  );
}

function observeMainPagination() {
  var paginationContainer = document.querySelector("#main > #page-nav");
  if (!paginationContainer || !window.IntersectionObserver) {
    return;
  }

  // 检查是否关闭了动画
  if (window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    paginationContainer.classList.add("in-view");
    return;
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (entry.isIntersecting) {
        paginationContainer.classList.add("in-view");
        observer.unobserve(paginationContainer);
      }
    });
  }, {
    threshold: 0.1 // 当 10% 进入视口时就触发
  });

  observer.observe(paginationContainer);
}

function registerMainPaginationOpticalSurface() {
  var paginationRoot = document.querySelector("body.aria-style-aria-continuo #main > #page-nav");
  var paginationTrack = document.querySelector("body.aria-style-aria-continuo #main > #page-nav ul");

  if (!Aria.optical || typeof Aria.optical.register !== "function") {
    return;
  }

  if (!paginationRoot || !paginationTrack) {
    Aria.optical.unregister("pagination");
    return;
  }

  Aria.optical.register("pagination", {
    host: paginationTrack,
    sourceRoot: paginationRoot,
    variant: "pagination",
    mirroredClasses: ["is-awake", "in-view"],
  });
}

function initDockPagination(rootSelector, containerSelector) {
  var container = document.querySelector(containerSelector);
  var paginationRoot = document.querySelector(rootSelector);
  if (!container || !paginationRoot) return;

  var items = Array.prototype.slice.call(container.querySelectorAll("li"));

  function setAwakeState(isAwake) {
    paginationRoot.classList.toggle("is-awake", isAwake);
  }

  container.addEventListener("mousemove", function (e) {
    var targetItem = null;
    var targetOffset = 0;
    var minDistance = Infinity;

    setAwakeState(true);

    items.forEach(function (li) {
      var rect = li.getBoundingClientRect();
      var distance = 0;

      if (e.clientX >= rect.left && e.clientX <= rect.right) {
        distance = 0;
      } else {
        distance = Math.min(Math.abs(e.clientX - rect.left), Math.abs(e.clientX - rect.right));
      }

      if (distance < minDistance) {
        minDistance = distance;
        targetItem = li;
        if (e.clientX < rect.left) {
          targetOffset = 0;
        } else if (e.clientX > rect.right) {
          targetOffset = 1;
        } else {
          targetOffset = (e.clientX - rect.left) / rect.width;
        }
      }
    });

    if (targetItem) {
      targetOffset = Math.max(0, Math.min(1, targetOffset));

      items.forEach(function (el) {
        el.style.setProperty("--dock-scale", "0");
      });

      var prev = targetItem.previousElementSibling;
      var next = targetItem.nextElementSibling;

      if (prev) {
        prev.style.setProperty("--dock-scale", String(1 - targetOffset));
      }

      targetItem.style.setProperty("--dock-scale", "1");

      if (next) {
        next.style.setProperty("--dock-scale", String(targetOffset));
      }
    }
  });

  container.addEventListener("mouseleave", function () {
    setAwakeState(false);
    items.forEach(function (el) {
      el.style.setProperty("--dock-scale", "0");
    });
  });

  container.addEventListener("mouseenter", function () {
    setAwakeState(true);
  });

  container.addEventListener("focusin", function () {
    setAwakeState(true);
  });

  container.addEventListener("focusout", function () {
    window.requestAnimationFrame(function () {
      if (!container.contains(document.activeElement)) {
        setAwakeState(false);
      }
    });
  });
}

Aria.action = Aria.action || {};
Aria.action.init = function () {
  if (Aria.helpers && typeof Aria.helpers.cleanupPageEntryAnimation === "function") {
    Aria.helpers.cleanupPageEntryAnimation();
  }
  headroom();
  adaptiveNavInk();
  gotop();
  closeNav();
  nav();
  search();
  bindActions();
  normalizePaginationLabels("#main > #page-nav");
  normalizePaginationLabels("#comments > .page-navigator");
  observeMainPagination();
  initDockPagination("body.aria-style-aria-continuo #main > #page-nav", "body.aria-style-aria-continuo #main > #page-nav ul");
  initDockPagination("body.aria-style-aria-continuo #comments > .page-navigator", "body.aria-style-aria-continuo #comments > .page-navigator ul");
  registerMainPaginationOpticalSurface();
  initWowAnimations();
};
Aria.action.closeNav = function () {
  return closeNav();
};
