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

  hoverBindings.forEach(function (binding) {
    binding.element.removeEventListener("mouseenter", binding.enterHandler);
    binding.element.removeEventListener("mouseleave", binding.leaveHandler);
  });

  Aria.state.navHoverBindings = [];

  Array.prototype.forEach.call(
    document.querySelectorAll(".nav-right-item"),
    function (element) {
      function handleMouseEnter() {
        var submenu = element.querySelector(".nav-sub");

        if (!submenu) {
          return;
        }

        submenu.classList.add("fast");
        submenu.style.display = "block";
        Aria.helpers.animateCss(submenu, "show-sub");
      }

      function handleMouseLeave() {
        var submenu = element.querySelector(".nav-sub");

        if (!submenu) {
          return;
        }

        submenu.style.display = "none";
      }

      element.addEventListener("mouseenter", handleMouseEnter);
      element.addEventListener("mouseleave", handleMouseLeave);
      Aria.state.navHoverBindings.push({
        element: element,
        enterHandler: handleMouseEnter,
        leaveHandler: handleMouseLeave,
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
  headroom();
  gotop();
  closeNav();
  nav();
  search();
  bindActions();
  normalizePaginationLabels("#main > #page-nav");
  normalizePaginationLabels("#comments > .page-navigator");
  observeMainPagination();
  initDockPagination(".aria-visual-enhancements #main > #page-nav", ".aria-visual-enhancements #main > #page-nav ul");
  initDockPagination(".aria-visual-enhancements #comments > .page-navigator", ".aria-visual-enhancements #comments > .page-navigator ul");
  initWowAnimations();
};
Aria.action.closeNav = function () {
  return closeNav();
};
