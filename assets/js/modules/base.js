/**
 * Aria front-end base globals and shared jQuery helpers.
 */
var Aria = (window.Aria = window.Aria || {});
Aria.helpers = Aria.helpers || {};
Aria.compat = Aria.compat || {};
Aria.state = Aria.state || {};
Aria.notify = Aria.notify || {};

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

Aria.notify.getInstance = function () {
  if (Aria.state.notifier) {
    return Aria.state.notifier;
  }

  if (typeof window.Notyf !== "function") {
    return null;
  }

  Aria.state.notifier = new window.Notyf({ delay: 3e3 });
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
