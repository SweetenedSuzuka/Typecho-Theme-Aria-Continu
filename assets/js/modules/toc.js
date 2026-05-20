var Aria = (window.Aria = window.Aria || {});

function children(nodes, match) {
  var node;
  var index;
  var total;
  var result = [];
  var isObjectMatch = typeof match === "object";
  var isStringMatch = typeof match === "string";

  for (index = 0, total = nodes.length; index < total; index++) {
    node = nodes[index];

    if (node.nodeType !== 1 && node.nodeType !== 9) {
      continue;
    }
    if (
      match &&
      !(
        (isObjectMatch && match.test(node.tagName.toLowerCase())) ||
        (isStringMatch && node.tagName.toLowerCase() === match)
      )
    ) {
      continue;
    }

    result.push(node);
  }

  return result;
}

function createDirectory(container, mountPoint) {
  var titles = [];
  var titleIds = [];

  var levels = (function (element, textStore, idStore) {
    var heading;
    var previousLevel = 1;
    var currentLevel = 1;
    var offsetSum = 0;
    var headings = children(element.childNodes, /^h[2-3]$/);
    var result = [];

    (Math.random() + "").replace(/\D/, "");

    while (headings.length) {
      heading = headings.shift();
      textStore.push(heading.textContent || heading.innerText || "");

      var headingLevel = +heading.tagName.match(/\d/)[0];
      if (previousLevel < headingLevel) {
        result.push(1);
        currentLevel += 1;
      } else if (headingLevel === currentLevel || (currentLevel < headingLevel && headingLevel <= previousLevel)) {
        result.push(0);
      } else if (headingLevel < currentLevel) {
        result.push(headingLevel - currentLevel);
        currentLevel = headingLevel;
      }

      offsetSum += result[result.length - 1];
      previousLevel = headingLevel;

      heading.id = heading.id || "toc-" + heading.innerText;
      heading.id = heading.id.replace(
        /[\s|\~|`|\!|\@|\#|\$|\%|\^|\&|\*|\(|\)|\_|\+|\=|\||\|\[|\]|\{|\}|\;|\:|\"|\'|\,|\<|\.|\>|\/|\?]/g,
        "",
      );
      idStore.push(heading.id);
    }

    if (offsetSum !== 0 && result[0] === 1) {
      result[0] = 0;
    }

    return result;
  })(container, titles, titleIds);

  var rootList = document.createElement("ul");
  var currentList = rootList;
  var dirNum = [0];

  for (var index = 0; index < levels.length; index++) {
    var level = levels[index];
    var childList;

    if (level === 1) {
      childList = document.createElement("ul");
      if (!currentList.lastElementChild) {
        currentList.appendChild(document.createElement("li"));
      }
      currentList.lastElementChild.appendChild(childList);
      currentList = childList;
      dirNum.push(0);
    } else if (level < 0) {
      for (level *= 2; level++; ) {
        if (level % 2) {
          dirNum.pop();
        }
        currentList = currentList.parentNode;
      }
    }

    dirNum[dirNum.length - 1]++;

    var listItem = document.createElement("li");
    var anchor = document.createElement("a");
    anchor.href = "#" + titleIds[index];
    anchor.setAttribute("class", "toc-a");
    anchor.textContent = titles[index];
    listItem.appendChild(anchor);
    currentList.appendChild(listItem);
  }

  mountPoint.appendChild(rootList);
  Aria.toc.titleId = titleIds;
}

Aria.toc = Aria.toc || {};
Aria.toc.titleId = Aria.toc.titleId || [];

function getTocTargetFromLink(link) {
  var href;

  if (!link || typeof link.getAttribute !== "function") {
    return null;
  }

  href = link.getAttribute("href") || "";
  if (!href || href.charAt(0) !== "#") {
    return null;
  }

  try {
    return document.querySelector(href);
  } catch (error) {
    return null;
  }
}

function getTocScrollTop(target) {
  var offset = 80;
  var top = target.getBoundingClientRect().top + window.pageYOffset - offset;

  return top > 0 ? top : 0;
}

function getTocScrollBehavior() {
  if (
    window.matchMedia &&
    window.matchMedia("(prefers-reduced-motion: reduce)").matches
  ) {
    return "auto";
  }

  return "smooth";
}

function updateHashWithoutJump(hash) {
  if (!hash) {
    return;
  }

  if (window.history && typeof window.history.pushState === "function") {
    window.history.pushState(null, document.title, hash);
    return;
  }

  window.location.hash = hash;
}

function destroyTocScrollBinding() {
  if (Aria.state.tocScrollHandler) {
    document.removeEventListener("click", Aria.state.tocScrollHandler);
    Aria.state.tocScrollHandler = null;
  }
}

function bindTocScroll() {
  destroyTocScrollBinding();

  Aria.state.tocScrollHandler = function (event) {
    var link = event.target.closest('#toc a[href*="#"]');
    var target;
    var href;

    if (!link) {
      return;
    }

    target = getTocTargetFromLink(link);
    href = link.getAttribute("href") || "";

    if (!target) {
      return;
    }

    event.preventDefault();
    window.scrollTo({
      top: getTocScrollTop(target),
      behavior: getTocScrollBehavior(),
    });
    updateHashWithoutJump(href);
  };

  document.addEventListener("click", Aria.state.tocScrollHandler);
}

function setTocContainerHeight() {
  var tocContainer = document.getElementById("toc-container");
  var postBody = document.querySelector(".post-body");

  if (!tocContainer || !postBody) {
    return;
  }

  tocContainer.style.height = postBody.offsetHeight + "px";
}

function destroyTocHeightSync() {
  if (Aria.state.tocResizeObserver) {
    Aria.state.tocResizeObserver.disconnect();
    Aria.state.tocResizeObserver = null;
  }

  if (Aria.state.tocResizeHandler) {
    window.removeEventListener("resize", Aria.state.tocResizeHandler);
    Aria.state.tocResizeHandler = null;
  }
}

function bindTocHeightSync() {
  var postBody = document.querySelector(".post-body");

  destroyTocHeightSync();
  setTocContainerHeight();

  if (!postBody) {
    return;
  }

  if (typeof window.ResizeObserver === "function") {
    Aria.state.tocResizeObserver = new window.ResizeObserver(function () {
      setTocContainerHeight();
    });
    Aria.state.tocResizeObserver.observe(postBody);
    return;
  }

  Aria.state.tocResizeHandler = function () {
    setTocContainerHeight();
  };
  window.addEventListener("resize", Aria.state.tocResizeHandler);
}

Aria.toc.init = function () {
  var toc = document.getElementById("toc");
  var postContent = document.querySelector(".post-content");

  if (!toc || !postContent) {
    this.titleId = [];
    destroyTocScrollBinding();
    destroyTocHeightSync();
    return;
  }

  toc.innerHTML = "";
  createDirectory(postContent, toc, !0);

  bindTocScroll();
  bindTocHeightSync();
};
