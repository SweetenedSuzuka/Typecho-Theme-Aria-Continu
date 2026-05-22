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
};

function toArray(nodeList) {
  return Array.prototype.slice.call(nodeList || []);
}

function isImageLightboxEnabled() {
  return !!(THEME_CONFIG.ENABLE_IMAGE_LIGHTBOX || THEME_CONFIG.ENABLE_FANCYBOX);
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

  if (!trigger) {
    return;
  }

  imageUrl = trigger.getAttribute("href") || "";
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
  };

  ariaLightboxState.image.onerror = function () {
    ariaLightboxState.root.classList.remove("is-loading");
    ariaLightboxState.root.classList.add("is-error");
    ariaLightboxState.caption.hidden = false;
    ariaLightboxState.caption.textContent = caption
      ? caption + "（图片加载失败）"
      : "图片加载失败";
  };

  ariaLightboxState.image.src = imageUrl;
}

function closeLightbox() {
  if (!ariaLightboxState.root || !ariaLightboxState.active) {
    return;
  }

  ariaLightboxState.active = false;
  ariaLightboxState.root.hidden = true;
  ariaLightboxState.root.setAttribute("aria-hidden", "true");
  ariaLightboxState.root.classList.remove("is-active", "is-loading", "is-error");
  ariaLightboxState.image.removeAttribute("src");
  ariaLightboxState.caption.textContent = "";
  ariaLightboxState.counter.textContent = "";
  ariaLightboxState.currentItems = [];
  document.body.classList.remove("aria-lightbox-open");
  document.removeEventListener("keydown", handleLightboxKeydown);

  if (ariaLightboxState.lastTrigger && typeof ariaLightboxState.lastTrigger.focus === "function") {
    ariaLightboxState.lastTrigger.focus();
  }
}

function moveLightbox(step) {
  var nextIndex = ariaLightboxState.currentIndex + step;

  if (nextIndex < 0 || nextIndex >= ariaLightboxState.currentItems.length) {
    return;
  }

  ariaLightboxState.currentIndex = nextIndex;
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
  ariaLightboxState.active = true;
  ariaLightboxState.currentItems = items;
  ariaLightboxState.currentIndex = Math.max(items.indexOf(trigger), 0);
  ariaLightboxState.lastTrigger = trigger;
  ariaLightboxState.root.hidden = false;
  ariaLightboxState.root.setAttribute("aria-hidden", "false");
  ariaLightboxState.root.classList.add("is-active");
  document.body.classList.add("aria-lightbox-open");
  document.addEventListener("keydown", handleLightboxKeydown);
  renderCurrentImage();
  ariaLightboxState.dialog.focus();
}

function handleTriggerClick(event) {
  event.preventDefault();
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
  ariaLightboxState.closeButton.addEventListener("click", closeLightbox);
  ariaLightboxState.prevButton.addEventListener("click", function () {
    moveLightbox(-1);
  });
  ariaLightboxState.nextButton.addEventListener("click", function () {
    moveLightbox(1);
  });
}

function bindLightboxTrigger(trigger, image, groupName) {
  if (!trigger.getAttribute("href")) {
    trigger.setAttribute("href", getImageSource(image));
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
  var parent = image.parentNode;
  var trigger;

  if (!parent || image.getAttribute("data-aria-lightbox-bound") === "true") {
    return;
  }

  if (parent.tagName === "A") {
    trigger = parent;
  } else {
    trigger = document.createElement("a");
    parent.insertBefore(trigger, image);
    trigger.appendChild(image);
  }

  bindLightboxTrigger(trigger, image, groupName);
}

function prepareLightboxImages() {
  toArray(document.querySelectorAll(".post-content img:not(.link-avatar):not([no-fancybox])")).forEach(
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

// Compatibility alias for legacy references during the transition away from Fancybox naming.
Aria.fancybox = Aria.lightbox;
