var Aria = (window.Aria = window.Aria || {});

function getLazyloadErrorPlaceholderUrl() {
  return THEME_CONFIG.THEME_URL + "/assets/img/loading.svg";
}

function getLazyloadPendingPlaceholderUrl() {
  if (THEME_CONFIG.ENABLE_LAZYLOAD_PLACEHOLDER) {
    return getLazyloadErrorPlaceholderUrl();
  }

  return 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E';
}

function escapeCssUrl(url) {
  return String(url).replace(/\\/g, "\\\\").replace(/"/g, '\\"');
}

function runAfterNextPaint(callback) {
  if (typeof window.requestAnimationFrame === "function") {
    window.requestAnimationFrame(function () {
      window.requestAnimationFrame(callback);
    });
    return;
  }

  window.setTimeout(callback, 0);
}

function loadLazyImage(image) {
  var source = image.getAttribute("data-aria-lazy-src");
  var errorPlaceholderUrl = getLazyloadErrorPlaceholderUrl();

  if (!source || image.getAttribute("data-aria-lazy-loaded") === "true") {
    return;
  }

  image.setAttribute("data-aria-lazy-loaded", "true");
  image.addEventListener(
    "load",
    function () {
      runAfterNextPaint(function () {
        image.classList.remove("aria-lazy-pending");
        image.classList.add("aria-lazy-loaded");
      });
    },
    { once: true },
  );
  image.addEventListener(
    "error",
    function () {
      image.classList.remove("aria-lazy-loaded");
      image.classList.remove("aria-lazy-pending");
      image.classList.add("aria-lazy-error");
      image.src = errorPlaceholderUrl;
    },
    { once: true },
  );
  image.src = source;
}

function loadLazyBackground(element) {
  var backgroundUrl = element.getAttribute("data-aria-lazy-background");
  var errorPlaceholderUrl = getLazyloadErrorPlaceholderUrl();
  var preloader;

  if (!backgroundUrl || element.getAttribute("data-aria-lazy-loaded") === "true") {
    return;
  }

  element.setAttribute("data-aria-lazy-loaded", "true");
  preloader = new Image();
  preloader.decoding = "async";
  preloader.onload = function () {
    element.style.backgroundImage = 'url("' + escapeCssUrl(backgroundUrl) + '")';
    runAfterNextPaint(function () {
      element.classList.remove("aria-lazy-pending");
      element.classList.add("aria-lazy-loaded");
    });
  };
  preloader.onerror = function () {
    element.style.backgroundImage = 'url("' + escapeCssUrl(errorPlaceholderUrl) + '")';
    element.classList.remove("aria-lazy-loaded");
    element.classList.remove("aria-lazy-pending");
    element.classList.add("aria-lazy-error");
  };
  preloader.src = backgroundUrl;
}

function prepareLazyImages() {
  var errorPlaceholderUrl = getLazyloadErrorPlaceholderUrl();
  var pendingPlaceholderUrl = getLazyloadPendingPlaceholderUrl();
  var images = document.querySelectorAll("img:not([no-lazyload])");
  var pending = [];

  Array.prototype.forEach.call(images, function (image) {
    var source = image.getAttribute("data-aria-lazy-src") || image.getAttribute("src");

    if (
      image.getAttribute("data-aria-lazy-bound") === "true" ||
      !source ||
      source === errorPlaceholderUrl ||
      source === pendingPlaceholderUrl
    ) {
      return;
    }

    image.setAttribute("data-aria-lazy-bound", "true");
    image.setAttribute("data-aria-lazy-src", source);
    image.setAttribute("loading", "lazy");
    image.setAttribute("decoding", "async");
    image.setAttribute("fetchpriority", "low");
    image.classList.add("aria-lazy-image", "aria-lazy-pending");
    image.src = pendingPlaceholderUrl;
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

  table.className = "code-ln";

  lines.forEach(function (line, index) {
    var row = document.createElement("tr");
    var numberCell = document.createElement("td");
    var numberText = document.createElement("div");
    var codeCell = document.createElement("td");

    row.className = "code-ln-line";
    numberCell.className = "code-ln-numbers";
    codeCell.className = "code-ln-code";
    numberText.className = "code-ln-n";
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
    if (THEME_CONFIG.ENABLE_IMAGE_LIGHTBOX && this.lightbox && typeof this.lightbox.init === "function") {
      this.lightbox.init();
    }
    if (THEME_CONFIG.SHOW_HITOKOTO) {
      this.hitokoto();
    }
    if (THEME_CONFIG.ENABLE_LAZYLOAD) {
      this.lazyload();
    }

    this.codeBlock.init();
    this.commentPlus.init();
    this.toc.init();
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

  codeBlock: {
    isContinuo: function () {
      return document.body.classList.contains("aria-style-aria-continuo");
    },

    init: function () {
      var isContinuo = this.isContinuo();

      Array.prototype.forEach.call(
        document.querySelectorAll("pre code"),
        function (element, index) {
          if (element.getAttribute("data-aria-code-block-bound") === "true") {
            return;
          }

          if (isContinuo) {
            Aria.codeBlock.initContinuoBlock(element, index);
          } else {
            Aria.codeBlock.initOriginalBlock(element, index);
          }
        },
      );

      if (isContinuo) {
        this.bindToggleEvents();
      }

      this.clipboard();
    },

    initContinuoBlock: function (element, index) {
      var pre = element.parentNode;
      var shouldAddLineNumbers;
      var rawCodeText;
      var match;
      var language;
      var headerEl;
      var langEl;
      var actionsEl;
      var linesToggle;
      var wrapToggle;
      var copyButton;

      shouldAddLineNumbers = !element.closest(".comment-text");
      rawCodeText = element.textContent || "";

      element.setAttribute("data-aria-code-block-bound", "true");
      element.setAttribute("data-aria-copy-text", rawCodeText);

      hljs.highlightElement(element);

      if (
        shouldAddLineNumbers &&
        element.getAttribute("data-aria-code-lines-bound") !== "true"
      ) {
        element.setAttribute("data-aria-code-lines-bound", "true");
        buildHighlightedCodeLineTable(element);
      }

      element.id = "code-block-" + index;

      match = (element.getAttribute("class") || "").match(/\blang(?:uage)?-([\w-]+)\b/i);
      language = match == null ? "CODE" : match[1].toUpperCase();

      element.setAttribute("data-lang", language);

      headerEl = document.createElement("div");
      headerEl.className = "aria-code-header";

      langEl = document.createElement("span");
      langEl.className = "aria-code-lang";
      langEl.textContent = language;

      actionsEl = document.createElement("div");
      actionsEl.className = "aria-code-actions";

      linesToggle = document.createElement("button");
      linesToggle.className = "aria-code-btn is-active";
      linesToggle.setAttribute("data-action", "toggle-lines");
      linesToggle.title = "切换行号";
      linesToggle.innerHTML = '<i class="iconfont icon-aria-code"></i><span>行号</span>';

      wrapToggle = document.createElement("button");
      wrapToggle.className = "aria-code-btn";
      wrapToggle.setAttribute("data-action", "toggle-wrap");
      wrapToggle.title = "切换自动换行";
      wrapToggle.innerHTML = '<i class="aria-code-icon-wrap"></i><span>换行</span>';

      copyButton = document.createElement("button");
      copyButton.className = "aria-code-btn";
      copyButton.setAttribute("data-action", "copy");
      copyButton.title = "拷贝代码";
      copyButton.innerHTML = '<i class="iconfont icon-aria-copy"></i><span>复制</span>';

      actionsEl.appendChild(linesToggle);
      actionsEl.appendChild(wrapToggle);
      actionsEl.appendChild(copyButton);
      headerEl.appendChild(langEl);
      headerEl.appendChild(actionsEl);
      pre.insertBefore(headerEl, pre.firstChild);

      if (shouldAddLineNumbers) {
        pre.classList.add("is-lines-visible");
      } else {
        linesToggle.style.display = "none";
      }
    },

    initOriginalBlock: function (element, index) {
      var shouldAddLineNumbers;
      var rawCodeText;
      var match;
      var language;
      var nextElement;
      var copyButton;

      shouldAddLineNumbers = !element.closest(".comment-text");
      rawCodeText = element.textContent || "";

      element.setAttribute("data-aria-code-block-bound", "true");
      element.setAttribute("data-aria-copy-text", rawCodeText);

      hljs.highlightElement(element);

      if (
        shouldAddLineNumbers &&
        element.getAttribute("data-aria-code-lines-bound") !== "true"
      ) {
        element.setAttribute("data-aria-code-lines-bound", "true");
        buildHighlightedCodeLineTable(element);
      }

      element.id = "code-block-" + index;

      match = (element.getAttribute("class") || "").match(/\blang(?:uage)?-([\w-]+)\b/i);
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

    bindToggleEvents: function () {
      if (Aria.state.codeBlockTogglesBound) {
        return;
      }

      Aria.state.codeBlockTogglesBound = true;

      document.addEventListener("click", function (event) {
        var btn = event.target.closest(".aria-code-btn");

        if (!btn) {
          return;
        }

        var action = btn.getAttribute("data-action");
        var pre = btn.closest("pre");

        if (!pre) {
          return;
        }

        event.preventDefault();

        if (action === "toggle-lines") {
          Aria.codeBlock.toggleLineNumbers(pre, btn);
        } else if (action === "toggle-wrap") {
          Aria.codeBlock.toggleWrap(pre, btn);
        } else if (action === "copy") {
          Aria.codeBlock.handleCopy(btn);
        }
      });
    },

    toggleLineNumbers: function (pre, btn) {
      var isVisible = pre.classList.contains("is-lines-visible");

      if (isVisible) {
        pre.classList.remove("is-lines-visible");
        btn.classList.remove("is-active");
      } else {
        pre.classList.add("is-lines-visible");
        btn.classList.add("is-active");
      }
    },

    animateButtonWidth: function (btn, after) {
      var oldWidth = btn.offsetWidth;
      var newWidth;

      btn.style.width = oldWidth + "px";
      btn.offsetHeight;

      after();

      btn.style.width = "auto";
      newWidth = btn.offsetWidth;
      btn.style.width = oldWidth + "px";
      btn.offsetHeight;
      btn.style.width = newWidth + "px";

      setTimeout(function () {
        btn.style.width = "";
      }, 220);
    },

    toggleWrap: function (pre, btn) {
      var isWrapped = pre.classList.contains("is-wrapped");
      var labelSpan = btn.querySelector("span");
      var currentHeight;
      var newHeight;

      currentHeight = pre.offsetHeight;
      pre.style.maxHeight = currentHeight + "px";
      pre.style.overflow = "hidden";

      Aria.codeBlock.animateButtonWidth(btn, function () {
        if (isWrapped) {
          pre.classList.remove("is-wrapped");
          btn.classList.remove("is-active");
          if (labelSpan) labelSpan.textContent = "换行";
        } else {
          pre.classList.add("is-wrapped");
          btn.classList.add("is-active");
          if (labelSpan) labelSpan.textContent = "取消换行";
        }
      });

      pre.style.maxHeight = "none";
      pre.style.overflow = "visible";
      newHeight = pre.offsetHeight;

      pre.style.maxHeight = currentHeight + "px";
      pre.style.overflow = "hidden";

      requestAnimationFrame(function () {
        requestAnimationFrame(function () {
          pre.style.maxHeight = newHeight + "px";

          setTimeout(function () {
            pre.style.maxHeight = "";
            pre.style.overflow = "";
          }, 350);
        });
      });
    },

    handleCopy: function (btn) {
      var pre = btn.closest("pre");
      var code = pre ? pre.querySelector("code") : null;
      var text;
      var labelSpan;

      if (!code) {
        return;
      }

      text = code.getAttribute("data-aria-copy-text") || code.textContent || "";

      copyTextToClipboard(text)
        .then(function () {
          labelSpan = btn.querySelector("span");

          Aria.codeBlock.animateButtonWidth(btn, function () {
            btn.classList.add("is-copied");
            if (labelSpan) labelSpan.textContent = "已复制";
          });

          setTimeout(function () {
            Aria.codeBlock.animateButtonWidth(btn, function () {
              btn.classList.remove("is-copied");
              if (labelSpan) labelSpan.textContent = "复制";
            });
          }, 1500);

          Aria.notify.success("代码成功拷贝到剪贴板！");
          if (typeof window.getSelection === "function") {
            window.getSelection().removeAllRanges();
          }
        })
        .catch(function () {
          Aria.notify.error("代码拷贝失败！");
        });
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
