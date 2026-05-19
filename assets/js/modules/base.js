/**
 * Aria front-end base globals and shared jQuery helpers.
 */
var Aria = (window.Aria = window.Aria || {});
Aria.helpers = Aria.helpers || {};
Aria.compat = Aria.compat || {};
Aria.state = Aria.state || {};
Aria.notify = Aria.notify || {};

var ARIA_NOTIFY_DELAY = 3000;
var ARIA_NOTIFY_DISAPPEAR_DURATION = 600;

Aria.helpers.toggleNav = function () {
  $("#nav-vertical").toggleClass("nav-open");
  $("#wrapper").toggle();
};

Aria.helpers.goTop = function (target) {
  var button = $(target);
  var scrollingElement =
    document.scrollingElement || document.documentElement || document.body;

  button.stop(!0, !0).animate({ opacity: 0.4 }, 150);

  if (typeof window.scrollTo === "function") {
    try {
      window.scrollTo({ top: 0, behavior: "smooth" });
      window.setTimeout(function () {
        button.animate({ opacity: 1 }, 150);
      }, 500);
      return;
    } catch (error) {
      // Fall back to jQuery animation for older browsers.
    }
  }

  $(scrollingElement)
    .stop(!0)
    .animate({ scrollTop: 0 }, 600, function () {
      button.animate({ opacity: 1 }, 150);
    });
};

Aria.helpers.togglePostOther = function (target) {
  var panel = $(target).next();

  if (panel.css("display") !== "none") {
    panel.fadeOut();
    return;
  }

  panel.fadeIn().css("display", "flex");
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

$.fn.extend({
  animateCss: function (animationName, callback) {
    var animationEnd = (function (element) {
      var animations = {
        animation: "animationend",
        OAnimation: "oAnimationEnd",
        MozAnimation: "mozAnimationEnd",
        WebkitAnimation: "webkitAnimationEnd",
      };

      for (var key in animations) {
        if (typeof element.style[key] !== "undefined") {
          return animations[key];
        }
      }
    })(document.createElement("div"));

    return this.addClass("animated " + animationName).one(
      animationEnd,
      function () {
        $(this).removeClass("animated " + animationName);
        if (typeof callback === "function") {
          callback($(this));
        }
      },
    );
  },
});
