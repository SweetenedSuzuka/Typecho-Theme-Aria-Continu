/**
 * Aria front-end base globals and shared jQuery helpers.
 */
var Aria = (window.Aria = window.Aria || {});
Aria.helpers = Aria.helpers || {};
Aria.compat = Aria.compat || {};
Aria.state = Aria.state || {};

Aria.helpers.toggleNav = function () {
  $("#nav-vertical").toggleClass("nav-open");
  $("#wrapper").toggle();
};

Aria.helpers.goTop = function (target) {
  $(target).animate({ opacity: 0 });
  $("body,html").animate({ scrollTop: 0 }, 1e3, function () {
    $(target).animate({ opacity: 1 });
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
