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

function getCopyTargetElement(trigger) {
  var selector;

  if (!trigger || typeof trigger.getAttribute !== "function") {
    return null;
  }

  selector = trigger.getAttribute("data-clipboard-target");
  if (!selector) {
    return null;
  }

  try {
    return document.querySelector(selector);
  } catch (error) {
    return null;
  }
}

function copyTextWithExecCommand(text) {
  return new Promise(function (resolve, reject) {
    var textarea = document.createElement("textarea");
    var successful = false;

    textarea.value = text;
    textarea.setAttribute("readonly", "readonly");
    textarea.setAttribute("aria-hidden", "true");
    textarea.style.position = "fixed";
    textarea.style.top = "-9999px";
    textarea.style.left = "-9999px";

    document.body.appendChild(textarea);
    textarea.focus();
    textarea.select();
    textarea.setSelectionRange(0, textarea.value.length);

    try {
      successful = document.execCommand("copy");
    } catch (error) {
      successful = false;
    }

    document.body.removeChild(textarea);

    if (successful) {
      resolve();
      return;
    }

    reject(new Error("Copy command failed"));
  });
}

function copyTextToClipboard(text) {
  if (
    typeof navigator !== "undefined" &&
    navigator.clipboard &&
    typeof navigator.clipboard.writeText === "function"
  ) {
    return navigator.clipboard.writeText(text).catch(function () {
      return copyTextWithExecCommand(text);
    });
  }

  return copyTextWithExecCommand(text);
}

function copyCodeFromTrigger(trigger) {
  var target = getCopyTargetElement(trigger);
  var text;

  if (!target) {
    return Promise.reject(new Error("Copy target not found"));
  }

  if (target.hasAttribute("data-aria-copy-text")) {
    return copyTextToClipboard(target.getAttribute("data-aria-copy-text") || "");
  }

  text = target.innerText || target.textContent || "";
  return copyTextToClipboard(text);
}

function createHighlightedLineState(openElements) {
  var state = {
    fragment: document.createDocumentFragment(),
    containers: [],
  };

  openElements.forEach(function (element) {
    var clone = element.cloneNode(false);
    var parent = state.containers[state.containers.length - 1] || state.fragment;

    parent.appendChild(clone);
    state.containers.push(clone);
  });

  return state;
}

function appendNodeToHighlightedLine(state, node) {
  var parent = state.containers[state.containers.length - 1] || state.fragment;
  parent.appendChild(node);
}

function getHighlightedLineDataList(codeElement) {
  var openElements = [];
  var lineStates = [];
  var currentState = createHighlightedLineState(openElements);

  function startNewLine() {
    currentState = createHighlightedLineState(openElements);
    lineStates.push(currentState);
  }

  function walk(node) {
    if (node.nodeType === Node.TEXT_NODE) {
      var parts = node.textContent.split("\n");

      parts.forEach(function (part, index) {
        if (part) {
          appendNodeToHighlightedLine(currentState, document.createTextNode(part));
        }

        if (index < parts.length - 1) {
          startNewLine();
        }
      });
      return;
    }

    if (node.nodeType !== Node.ELEMENT_NODE) {
      return;
    }

    var clone = node.cloneNode(false);
    appendNodeToHighlightedLine(currentState, clone);
    openElements.push(node);
    currentState.containers.push(clone);

    Array.prototype.forEach.call(node.childNodes, walk);

    currentState.containers.pop();
    openElements.pop();
  }

  lineStates.push(currentState);
  Array.prototype.forEach.call(codeElement.childNodes, walk);

  if (
    /\n$/.test(codeElement.textContent || "") &&
    lineStates.length > 1 &&
    lineStates[lineStates.length - 1].fragment.textContent === ""
  ) {
    lineStates.pop();
  }

  return lineStates.map(function (state) {
    var wrapper = document.createElement("div");
    wrapper.appendChild(state.fragment);

    return {
      html: wrapper.innerHTML,
      text: wrapper.textContent || "",
    };
  });
}

function buildHighlightedCodeLineTable(codeElement) {
  var lines = getHighlightedLineDataList(codeElement);
  var table = document.createElement("table");
  var tbody = document.createElement("tbody");

  table.className = "hljs-ln";

  lines.forEach(function (line, index) {
    var row = document.createElement("tr");
    var numberCell = document.createElement("td");
    var numberText = document.createElement("div");
    var codeCell = document.createElement("td");

    row.className = "hljs-ln-line";
    numberCell.className = "hljs-ln-numbers";
    codeCell.className = "hljs-ln-code";
    numberText.className = "hljs-ln-n";
    numberText.textContent = String(index + 1);

    numberCell.appendChild(numberText);
    codeCell.innerHTML = line.text === "" ? " " : line.html;
    row.appendChild(numberCell);
    row.appendChild(codeCell);
    tbody.appendChild(row);
  });

  table.appendChild(tbody);
  codeElement.innerHTML = "";
  codeElement.appendChild(table);
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

function createFancyboxAnchor(image, useGallery) {
  var anchor = document.createElement("a");

  anchor.href = image.src;
  anchor.setAttribute("data-caption", image.title || "");
  anchor.className = "fancybox";
  anchor.style.outline = "0";

  if (useGallery) {
    anchor.setAttribute("data-fancybox", "gallery");
  }

  return anchor;
}

function wrapImageWithFancybox(image, useGallery) {
  var anchor;
  var parent;

  if (!image || image.getAttribute("data-aria-fancybox-bound") === "true") {
    return;
  }

  parent = image.parentNode;
  if (!parent) {
    return;
  }

  anchor = createFancyboxAnchor(image, useGallery);
  image.setAttribute("data-aria-fancybox-bound", "true");
  parent.insertBefore(anchor, image);
  anchor.appendChild(image);
}

function prepareFancyboxImages() {
  Array.prototype.forEach.call(
    document.querySelectorAll(".post-content img:not(.link-avatar):not([no-fancybox])"),
    function (image) {
      wrapImageWithFancybox(image, !0);
    },
  );

  Array.prototype.forEach.call(
    document.querySelectorAll(".comment-text img"),
    function (image) {
      wrapImageWithFancybox(image, !1);
    },
  );
}

Object.assign(Aria, {
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
    if (
      !document.querySelector(".post-content img") &&
      !document.querySelector(".comment-content img")
    ) {
      return;
    }

    prepareFancyboxImages();

    if (!document.querySelector("a.fancybox")) {
      return;
    }

    $("a.fancybox").fancybox({
      animationEffect: "zoom-in-out",
      animationDuration: 500,
      transitionEffect: "tube",
      transitionDuration: 500,
      backFocus: false,
      spinnerTpl:
        '<img style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);" src="' +
        THEME_CONFIG.THEME_URL +
        '/assets/img/loading.svg">',
    });
  },

  hitokoto: function () {
    var hitokoto = document.getElementById("hitokoto");

    if (!hitokoto) {
      return;
    }

    fetch(THEME_CONFIG.HITOKOTO_ORIGIN, {
      method: "GET",
      credentials: "same-origin",
    })
      .then(function (response) {
        return response.text();
      })
      .then(function (text) {
        hitokoto.textContent = text;
      })
      .catch(function () {});
  },

  hljs: {
    init: function () {
      Array.prototype.forEach.call(
        document.querySelectorAll("pre code"),
        function (element, index) {
          var shouldAddLineNumbers;
          var rawCodeText;
          var match;
          var language;
          var nextElement;
          var copyButton;

          if (element.getAttribute("data-aria-hljs-bound") === "true") {
            return;
          }

          shouldAddLineNumbers = !element.closest(".comment-text");
          rawCodeText = element.textContent || "";

          element.setAttribute("data-aria-hljs-bound", "true");
          element.setAttribute("data-aria-copy-text", rawCodeText);
          hljs.highlightBlock(element);
          if (
            shouldAddLineNumbers &&
            element.getAttribute("data-aria-hljs-lines-bound") !== "true"
          ) {
            element.setAttribute("data-aria-hljs-lines-bound", "true");
            buildHighlightedCodeLineTable(element);
          }
          element.id = "hljs-" + index;

          match = (element.getAttribute("class") || "").match(/lang-(\w+)/);
          language = match == null ? "CODE" : match[1].toUpperCase();

          element.setAttribute("data-lang", language);
          nextElement = element.nextElementSibling;
          if (nextElement && nextElement.classList.contains("copy-code")) {
            return;
          }

          copyButton = document.createElement("a");
          copyButton.className = "copy-code";
          copyButton.href = "javascript:";
          copyButton.title = "拷贝代码";
          copyButton.setAttribute("data-clipboard-target", "#" + element.id);
          copyButton.innerHTML = '<i class="iconfont icon-aria-copy"></i>';
          element.insertAdjacentElement("afterend", copyButton);
        },
      );

      this.clipboard();
    },

    clipboard: function () {
      if (Aria.state.copyCodeClickHandler) {
        document.removeEventListener("click", Aria.state.copyCodeClickHandler);
      }

      Aria.state.copyCodeClickHandler = function (event) {
        var trigger = event.target.closest(".copy-code");

        if (!trigger) {
          return;
        }

        event.preventDefault();

        copyCodeFromTrigger(trigger)
          .then(function () {
            Aria.notify.success("代码成功拷贝到剪贴板！");
            if (typeof window.getSelection === "function") {
              window.getSelection().removeAllRanges();
            }
          })
          .catch(function () {
            Aria.notify.error("代码拷贝失败！");
          });
      };

      document.addEventListener("click", Aria.state.copyCodeClickHandler);
    },
  },

  lazyload: function () {
    observeLazyTargets(prepareLazyImages().concat(prepareLazyBackgrounds()));
  },
});
