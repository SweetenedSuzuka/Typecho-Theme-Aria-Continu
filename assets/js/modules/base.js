/**
 * Aria front-end base globals and shared helper utilities.
 */
var Aria = (window.Aria = window.Aria || {});
Aria.helpers = Aria.helpers || {};
Aria.compat = Aria.compat || {};
Aria.state = Aria.state || {};
Aria.notify = Aria.notify || {};

var ARIA_NOTIFY_DELAY = 3000;
var ARIA_NOTIFY_DISAPPEAR_DURATION = 600;

function getAnimationEndEventName() {
  var animationEnd = Aria.state.animationEndEventName;
  var element;
  var animations;
  var key;

  if (animationEnd) {
    return animationEnd;
  }

  element = document.createElement("div");
  animations = {
    animation: "animationend",
    OAnimation: "oAnimationEnd",
    MozAnimation: "mozAnimationEnd",
    WebkitAnimation: "webkitAnimationEnd",
  };

  for (key in animations) {
    if (typeof element.style[key] !== "undefined") {
      Aria.state.animationEndEventName = animations[key];
      return animations[key];
    }
  }

  return null;
}

function isElementVisible(element) {
  return !!element && window.getComputedStyle(element).display !== "none";
}

function stopElementAnimations(element) {
  if (!element || typeof element.getAnimations !== "function") {
    return;
  }

  element.getAnimations().forEach(function (animation) {
    animation.cancel();
  });
}

function fadeElement(element, shouldShow, displayValue, duration) {
  var animation;

  if (!element) {
    return;
  }

  duration = typeof duration === "number" ? duration : 200;
  stopElementAnimations(element);

  if (shouldShow) {
    element.style.display = displayValue || "block";
  }

  if (typeof element.animate === "function") {
    animation = element.animate(
      [{ opacity: shouldShow ? 0 : 1 }, { opacity: shouldShow ? 1 : 0 }],
      { duration: duration, easing: "ease" },
    );
    animation.onfinish = function () {
      element.style.opacity = "";
      if (!shouldShow) {
        element.style.display = "none";
      }
    };
    return;
  }

  if (!shouldShow) {
    element.style.display = "none";
  }
}

Aria.helpers.toggleNav = function () {
  var navigation = document.getElementById("nav-vertical");
  var wrapper = document.getElementById("wrapper");
  var isOpen;

  if (!navigation) {
    return;
  }

  isOpen = !navigation.classList.contains("nav-open");
  navigation.classList.toggle("nav-open", isOpen);

  if (!wrapper) {
    return;
  }

  wrapper.classList.toggle("wrapper-open", isOpen);
  wrapper.style.display = isOpen ? "block" : "none";
};

Aria.helpers.goTop = function (target) {
  var button = target && target.nodeType === 1 ? target : document.getElementById("go-top");
  var buttonImage = button ? button.querySelector("img") : null;
  var scrollingElement =
    document.scrollingElement || document.documentElement || document.body;

  if (buttonImage) {
    buttonImage.style.transition = "opacity 150ms ease";
    buttonImage.style.opacity = "0.4";
  }

  function restoreButtonOpacity() {
    if (!buttonImage) {
      return;
    }

    buttonImage.style.opacity = "1";
  }

  if (typeof window.scrollTo === "function") {
    try {
      window.scrollTo({ top: 0, behavior: "smooth" });
      window.setTimeout(function () {
        restoreButtonOpacity();
      }, 500);
      return;
    } catch (error) {
      try {
        window.scrollTo(0, 0);
        restoreButtonOpacity();
        return;
      } catch (fallbackError) {
        // Fall back to directly mutating the scrolling container.
      }
    }
  }

  if (scrollingElement) {
    scrollingElement.scrollTop = 0;
  }

  window.setTimeout(function () {
    restoreButtonOpacity();
  }, 150);
};

Aria.helpers.togglePostOther = function (target) {
  var panel = target ? target.nextElementSibling : null;

  if (isElementVisible(panel)) {
    fadeElement(panel, !1, "flex");
    return;
  }

  fadeElement(panel, !0, "flex");
};

Aria.helpers.animateCss = function (element, animationName, callback) {
  var animationEnd = getAnimationEndEventName();

  if (!element) {
    return;
  }

  if (!animationEnd) {
    if (typeof callback === "function") {
      callback(element);
    }
    return;
  }

  element.classList.remove("animated", animationName);
  void element.offsetWidth;
  element.classList.add("animated", animationName);
  element.addEventListener(
    animationEnd,
    function handleAnimationEnd() {
      element.classList.remove("animated", animationName);
      if (typeof callback === "function") {
        callback(element);
      }
    },
    { once: true },
  );
};

Aria.compat.installLegacyGlobals = function () {
  if (Aria.state.legacyGlobalsInstalled) {
    return;
  }

  Aria.state.legacyGlobalsInstalled = !0;
  window.toggleNav =
    typeof window.toggleNav === "function"
      ? window.toggleNav
      : function () {
          Aria.helpers.toggleNav();
        };
  window.goTop =
    typeof window.goTop === "function"
      ? window.goTop
      : function (target) {
          Aria.helpers.goTop(target);
        };
  window.togglePostOther =
    typeof window.togglePostOther === "function"
      ? window.togglePostOther
      : function (target) {
          Aria.helpers.togglePostOther(target);
        };
};

Aria.notify.getContainer = function () {
  var container;

  if (Aria.state.notifyContainer && document.body.contains(Aria.state.notifyContainer)) {
    return Aria.state.notifyContainer;
  }

  if (!document.body) {
    return null;
  }

  container = document.createElement("div");
  container.className = "notyf-container";
  document.body.appendChild(container);
  Aria.state.notifyContainer = container;
  return container;
};

Aria.notify.scheduleRemoval = function (element) {
  if (!element || element.getAttribute("data-aria-notify-closing") === "true") {
    return;
  }

  element.setAttribute("data-aria-notify-closing", "true");
  element.classList.add("disappear");
  window.setTimeout(function () {
    if (element.parentNode) {
      element.parentNode.removeChild(element);
    }
  }, ARIA_NOTIFY_DISAPPEAR_DURATION);
};

Aria.notify.createElement = function (message, type) {
  var element = document.createElement("div");
  var wrapper = document.createElement("div");
  var iconCell = document.createElement("div");
  var icon = document.createElement("i");
  var text = document.createElement("div");
  var iconClass = type === "confirm" ? "notyf-confirm-icon" : "notyf-alert-icon";

  element.className = "notyf " + (type === "confirm" ? "confirm" : "alert");
  wrapper.className = "notyf-wrapper";
  iconCell.className = "notyf-icon";
  text.className = "notyf-message";

  icon.className = iconClass;
  text.textContent = String(message);

  iconCell.appendChild(icon);
  wrapper.appendChild(iconCell);
  wrapper.appendChild(text);
  element.appendChild(wrapper);

  return element;
};

Aria.notify.push = function (message, type) {
  var container = Aria.notify.getContainer();
  var element;

  if (!container) {
    return false;
  }

  element = Aria.notify.createElement(message, type);
  container.appendChild(element);
  window.setTimeout(function () {
    Aria.notify.scheduleRemoval(element);
  }, ARIA_NOTIFY_DELAY);

  return true;
};

Aria.notify.getInstance = function () {
  if (Aria.state.notifier) {
    return Aria.state.notifier;
  }

  Aria.state.notifier = {
    confirm: function (message) {
      return Aria.notify.push(message, "confirm");
    },
    alert: function (message) {
      return Aria.notify.push(message, "alert");
    },
  };

  return Aria.state.notifier;
};

Aria.notify.success = function (message) {
  var notifier = Aria.notify.getInstance();

  if (notifier && typeof notifier.confirm === "function") {
    notifier.confirm(message);
    return;
  }

  console.log(message);
};

Aria.notify.error = function (message) {
  var notifier = Aria.notify.getInstance();

  if (notifier && typeof notifier.alert === "function") {
    notifier.alert(message);
    return;
  }

  if (typeof window.alert === "function") {
    window.alert(message);
    return;
  }

  console.error(message);
};
