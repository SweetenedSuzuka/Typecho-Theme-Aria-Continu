/**
 * Aria front-end base globals and shared jQuery helpers.
 */
var Aria = (window.Aria = window.Aria || {});
Aria.helpers = Aria.helpers || {};

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

function toggleNav() {
  Aria.helpers.toggleNav();
}

function goTop(target) {
  Aria.helpers.goTop(target);
}

function togglePostOther(target) {
  Aria.helpers.togglePostOther(target);
}

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
