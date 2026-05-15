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
      textStore.push(heading.innerHTML);

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
    anchor.innerHTML = titles[index];
    listItem.appendChild(anchor);
    currentList.appendChild(listItem);
  }

  mountPoint.appendChild(rootList);
  Aria.toc.titleId = titleIds;
}

Aria.toc = Aria.toc || {};
Aria.toc.titleId = Aria.toc.titleId || [];

Aria.toc.init = function () {
  var toc = $("#toc");
  if (!toc.length) {
    this.titleId = [];
    return;
  }

  toc.empty();
  createDirectory(
    document.getElementsByClassName("post-content")[0],
    toc.get(0),
    !0,
  );

  if (!Aria.state.tocScroll) {
    Aria.state.tocScroll = new SmoothScroll('#toc a[href*="#"]', { offset: 80 });
  }

  $("#toc-container").height($(".post-body").eq(0).height());
  $(".post-body")
    .off("resize.ariaToc")
    .on("resize.ariaToc", function () {
      $("#toc-container").height($(".post-body").eq(0).height());
    });
};
