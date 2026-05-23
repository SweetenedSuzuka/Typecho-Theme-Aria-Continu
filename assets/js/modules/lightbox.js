var Aria = (window.Aria = window.Aria || {});

var ariaLightboxState = {
  active: false,
  currentIndex: 0,
  currentItems: [],
  lastTrigger: null,
  root: null,
  dialog: null,
  image: null,
  caption: null,
  counter: null,
  closeButton: null,
  prevButton: null,
  nextButton: null,
  closeTimer: null,
  switchDirection: 0,
};

function toArray(nodeList) {
  return Array.prototype.slice.call(nodeList || []);
}

function isImageLightboxEnabled() {
  return !!THEME_CONFIG.ENABLE_IMAGE_LIGHTBOX;
}

function getImageSource(image) {
  return image.getAttribute("data-src") || image.currentSrc || image.src || "";
}

function getImageCaption(image, trigger) {
  return (
    image.getAttribute("title") ||
    image.getAttribute("alt") ||
    (trigger ? trigger.getAttribute("data-caption") : "") ||
    ""
  );
}

function isIgnorableAnchorChild(node) {
  if (!node) {
    return true;
  }

  if (node.nodeType === Node.TEXT_NODE) {
    return (node.textContent || "").trim() === "";
  }

  return node.nodeType === Node.COMMENT_NODE;
}

function isDedicatedMediaAnchor(anchor, mediaContainer) {
  return toArray(anchor.childNodes).every(function (node) {
    return node === mediaContainer || isIgnorableAnchorChild(node);
  });
}

function getImageMediaContainer(image) {
  if (image.parentNode && image.parentNode.tagName === "PICTURE") {
    return image.parentNode;
  }

  return image;
}

function getLightboxItemSource(trigger, image) {
  if (trigger.tagName === "A") {
    return trigger.getAttribute("href") || getImageSource(image);
  }

  return trigger.getAttribute("data-aria-lightbox-src") || getImageSource(image);
}

function resolveGalleryItems(trigger) {
  var groupName = trigger.getAttribute("data-aria-lightbox-group");

  if (!groupName) {
    return [trigger];
  }

  return toArray(document.querySelectorAll('[data-aria-lightbox="true"]')).filter(function (item) {
    return item.getAttribute("data-aria-lightbox-group") === groupName;
  });
}

function updateNavigationState() {
  var total = ariaLightboxState.currentItems.length;
  var hasPrev = ariaLightboxState.currentIndex > 0;
  var hasNext = ariaLightboxState.currentIndex < total - 1;

  ariaLightboxState.prevButton.disabled = !hasPrev;
  ariaLightboxState.nextButton.disabled = !hasNext;
  ariaLightboxState.prevButton.hidden = total < 2;
  ariaLightboxState.nextButton.hidden = total < 2;
  ariaLightboxState.counter.textContent = total > 1
    ? String(ariaLightboxState.currentIndex + 1) + " / " + String(total)
    : "";
}

function renderCurrentImage() {
  var trigger = ariaLightboxState.currentItems[ariaLightboxState.currentIndex];
  var imageUrl;
  var caption;
  var triggerImage;

  if (!trigger) {
    return;
  }

  triggerImage = trigger.tagName === "IMG" ? trigger : trigger.querySelector("img");
  imageUrl = getLightboxItemSource(trigger, triggerImage || ariaLightboxState.image);
  caption = trigger.getAttribute("data-caption") || "";

  ariaLightboxState.root.classList.remove("is-error");
  ariaLightboxState.root.classList.add("is-loading");
  ariaLightboxState.image.removeAttribute("src");
  ariaLightboxState.image.alt = caption;
  ariaLightboxState.caption.textContent = caption;
  ariaLightboxState.caption.hidden = caption === "";
  updateNavigationState();

  ariaLightboxState.image.onload = function () {
    ariaLightboxState.root.classList.remove("is-loading");
    animateImageEntry(ariaLightboxState.switchDirection);
    ariaLightboxState.root.classList.remove("is-switching-prev", "is-switching-next");
    ariaLightboxState.switchDirection = 0;
  };

  ariaLightboxState.image.onerror = function () {
    ariaLightboxState.root.classList.remove("is-loading");
    ariaLightboxState.root.classList.add("is-error");
    ariaLightboxState.root.classList.remove("is-switching-prev", "is-switching-next");
    ariaLightboxState.switchDirection = 0;
    ariaLightboxState.caption.hidden = false;
    ariaLightboxState.caption.textContent = caption
      ? caption + "（图片加载失败）"
      : "图片加载失败";
  };

  ariaLightboxState.image.src = imageUrl;
}

function animateImageEntry(direction) {
  var offsetX = 0;
  var offsetY = 10;

  if (!ariaLightboxState.image || typeof ariaLightboxState.image.animate !== "function") {
    return;
  }

  if (direction > 0) {
    offsetX = 20;
    offsetY = 0;
  } else if (direction < 0) {
    offsetX = -20;
    offsetY = 0;
  }

  ariaLightboxState.image.animate(
    [
      {
        opacity: 0,
        transform: "translate3d(" + String(offsetX) + "px, " + String(offsetY) + "px, 0) scale(0.985)",
      },
      {
        opacity: 1,
        transform: "translate3d(0, 0, 0) scale(1)",
      },
    ],
    {
      duration: direction === 0 ? 400 : 320,
      easing: "cubic-bezier(0.22, 1, 0.36, 1)",
      fill: "both",
    },
  );
}

function focusElementWithoutScroll(element) {
  var scrollX;
  var scrollY;

  if (!element || typeof element.focus !== "function") {
    return;
  }

  scrollX = window.pageXOffset || window.scrollX || 0;
  scrollY = window.pageYOffset || window.scrollY || 0;

  try {
    element.focus({ preventScroll: true });
  } catch (error) {
    element.focus();
    window.scrollTo(scrollX, scrollY);
  }
}

function closeLightbox() {
  if (!ariaLightboxState.root || !ariaLightboxState.active) {
    return;
  }

  ariaLightboxState.active = false;
  if (ariaLightboxState.closeTimer) {
    window.clearTimeout(ariaLightboxState.closeTimer);
    ariaLightboxState.closeTimer = null;
  }
  ariaLightboxState.root.setAttribute("aria-hidden", "true");
  ariaLightboxState.root.classList.add("is-closing");
  ariaLightboxState.root.classList.remove("is-active");
  document.body.classList.remove("aria-lightbox-open");
  document.removeEventListener("keydown", handleLightboxKeydown);

  focusElementWithoutScroll(ariaLightboxState.lastTrigger);

  ariaLightboxState.closeTimer = window.setTimeout(function () {
    ariaLightboxState.root.hidden = true;
    ariaLightboxState.root.classList.remove(
      "is-closing",
      "is-loading",
      "is-error",
      "is-switching-prev",
      "is-switching-next",
    );
    ariaLightboxState.image.removeAttribute("src");
    ariaLightboxState.caption.textContent = "";
    ariaLightboxState.counter.textContent = "";
    ariaLightboxState.currentItems = [];
    ariaLightboxState.switchDirection = 0;
    ariaLightboxState.closeTimer = null;
  }, 420);
}

function moveLightbox(step) {
  var nextIndex = ariaLightboxState.currentIndex + step;

  if (nextIndex < 0 || nextIndex >= ariaLightboxState.currentItems.length) {
    return;
  }

  ariaLightboxState.currentIndex = nextIndex;
  ariaLightboxState.switchDirection = step > 0 ? 1 : -1;
  ariaLightboxState.root.classList.remove("is-switching-prev", "is-switching-next");
  ariaLightboxState.root.classList.add(step > 0 ? "is-switching-next" : "is-switching-prev");
  renderCurrentImage();
}

function handleLightboxKeydown(event) {
  if (!ariaLightboxState.active) {
    return;
  }

  if (event.key === "Escape") {
    event.preventDefault();
    closeLightbox();
    return;
  }

  if (event.key === "ArrowLeft") {
    event.preventDefault();
    moveLightbox(-1);
    return;
  }

  if (event.key === "ArrowRight") {
    event.preventDefault();
    moveLightbox(1);
  }
}

function openLightbox(trigger) {
  var items = resolveGalleryItems(trigger);

  if (!items.length) {
    return;
  }

  ensureLightboxRoot();
  if (ariaLightboxState.closeTimer) {
    window.clearTimeout(ariaLightboxState.closeTimer);
    ariaLightboxState.closeTimer = null;
  }
  ariaLightboxState.active = true;
  ariaLightboxState.currentItems = items;
  ariaLightboxState.currentIndex = Math.max(items.indexOf(trigger), 0);
  ariaLightboxState.lastTrigger = trigger;
  ariaLightboxState.switchDirection = 0;
  ariaLightboxState.root.hidden = false;
  ariaLightboxState.root.setAttribute("aria-hidden", "false");
  
  // 强制浏览器重排，确保初始状态（blur: 0, opacity: 0）被真正应用，防止中途闪现
  void ariaLightboxState.root.offsetWidth;
  
  ariaLightboxState.root.classList.remove("is-closing", "is-switching-prev", "is-switching-next");
  ariaLightboxState.root.classList.add("is-active");
  
  document.body.classList.add("aria-lightbox-open");
  document.addEventListener("keydown", handleLightboxKeydown);
  renderCurrentImage();

  focusElementWithoutScroll(ariaLightboxState.dialog);
}

function handleTriggerClick(event) {
  event.preventDefault();
  event.stopPropagation();
  openLightbox(event.currentTarget);
}

function ensureLightboxRoot() {
  var root;
  var dialog;

  if (ariaLightboxState.root) {
    return;
  }

  root = document.createElement("div");
  root.className = "aria-lightbox";
  root.hidden = true;
  root.setAttribute("aria-hidden", "true");
  root.innerHTML =
    '<button class="aria-lightbox__backdrop" type="button" aria-label="关闭图片查看器"></button>' +
    '<div class="aria-lightbox__dialog" role="dialog" aria-modal="true" aria-label="图片查看器" tabindex="-1">' +
    '<button class="aria-lightbox__close" type="button" aria-label="关闭图片查看器">&times;</button>' +
    '<button class="aria-lightbox__nav aria-lightbox__nav--prev" type="button" aria-label="上一张">&#8249;</button>' +
    '<figure class="aria-lightbox__figure">' +
    '<div class="aria-lightbox__loading"><img src="' +
    THEME_CONFIG.THEME_URL +
    '/assets/img/loading.svg" alt=""></div>' +
    '<img class="aria-lightbox__image" alt="">' +
    '<figcaption class="aria-lightbox__caption"></figcaption>' +
    "</figure>" +
    '<button class="aria-lightbox__nav aria-lightbox__nav--next" type="button" aria-label="下一张">&#8250;</button>' +
    '<div class="aria-lightbox__counter" aria-live="polite"></div>' +
    "</div>";

  document.body.appendChild(root);
  dialog = root.querySelector(".aria-lightbox__dialog");

  ariaLightboxState.root = root;
  ariaLightboxState.dialog = dialog;
  ariaLightboxState.image = root.querySelector(".aria-lightbox__image");
  ariaLightboxState.caption = root.querySelector(".aria-lightbox__caption");
  ariaLightboxState.counter = root.querySelector(".aria-lightbox__counter");
  ariaLightboxState.closeButton = root.querySelector(".aria-lightbox__close");
  ariaLightboxState.prevButton = root.querySelector(".aria-lightbox__nav--prev");
  ariaLightboxState.nextButton = root.querySelector(".aria-lightbox__nav--next");

  root.querySelector(".aria-lightbox__backdrop").addEventListener("click", closeLightbox);
  dialog.addEventListener("click", function (event) {
    if (event.target === dialog) {
      closeLightbox();
    }
  });
  ariaLightboxState.closeButton.addEventListener("click", closeLightbox);
  ariaLightboxState.prevButton.addEventListener("click", function () {
    moveLightbox(-1);
  });
  ariaLightboxState.nextButton.addEventListener("click", function () {
    moveLightbox(1);
  });
}

function bindLightboxTrigger(trigger, image, groupName) {
  if (trigger.tagName === "A" && !trigger.getAttribute("href")) {
    trigger.setAttribute("href", getImageSource(image));
  } else if (trigger.tagName !== "A") {
    trigger.setAttribute("data-aria-lightbox-src", getImageSource(image));
  }

  trigger.classList.add("aria-lightbox-trigger");
  trigger.setAttribute("data-aria-lightbox", "true");
  trigger.setAttribute("data-caption", getImageCaption(image, trigger));

  if (groupName) {
    trigger.setAttribute("data-aria-lightbox-group", groupName);
  } else {
    trigger.removeAttribute("data-aria-lightbox-group");
  }

  if (trigger.getAttribute("data-aria-lightbox-bound") !== "true") {
    trigger.addEventListener("click", handleTriggerClick);
    trigger.setAttribute("data-aria-lightbox-bound", "true");
  }

  image.setAttribute("data-aria-lightbox-bound", "true");
}

function prepareImageTrigger(image, groupName) {
  var mediaContainer = getImageMediaContainer(image);
  var parent = mediaContainer.parentNode;
  var trigger;

  if (!parent || image.getAttribute("data-aria-lightbox-bound") === "true") {
    return;
  }

  if (parent.tagName === "A") {
    if (!isDedicatedMediaAnchor(parent, mediaContainer)) {
      return;
    }

    trigger = parent;
  } else {
    trigger = document.createElement("a");
    parent.insertBefore(trigger, mediaContainer);
    trigger.appendChild(mediaContainer);
  }

  bindLightboxTrigger(trigger, image, groupName);
}

function prepareLightboxImages() {
  toArray(document.querySelectorAll(".post-content img:not(.link-avatar)")).forEach(
    function (image) {
      prepareImageTrigger(image, "post-gallery");
    },
  );

  toArray(document.querySelectorAll(".comment-text img")).forEach(function (image) {
    prepareImageTrigger(image, "");
  });
}

Aria.lightbox = {
  init: function () {
    if (!isImageLightboxEnabled()) {
      return;
    }

    if (!document.querySelector(".post-content img") && !document.querySelector(".comment-text img")) {
      return;
    }

    ensureLightboxRoot();
    prepareLightboxImages();
  },

  close: closeLightbox,
};
