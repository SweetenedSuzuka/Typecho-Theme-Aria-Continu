(function ($, window) {
  var timeoutId;
  var trackedElements = $([]);
  var resizeState = ($.resize = $.extend($.resize, {}));
  var timeoutMethod = "setTimeout";
  var resizeEvent = "resize";
  var dataKey = resizeEvent + "-special-event";
  var delayKey = "delay";
  var throttleKey = "throttleWindow";

  resizeState[delayKey] = 250;
  resizeState[throttleKey] = !0;

  $.event.special[resizeEvent] = {
    setup: function () {
      if (!resizeState[throttleKey] && this[timeoutMethod]) {
        return !1;
      }

      var element = $(this);
      trackedElements = trackedElements.add(element);
      $.data(this, dataKey, { w: element.width(), h: element.height() });

      if (trackedElements.length === 1) {
        (function poll() {
          timeoutId = window[timeoutMethod](function () {
            trackedElements.each(function () {
              var currentElement = $(this);
              var width = currentElement.width();
              var height = currentElement.height();
              var stored = $.data(this, dataKey);

              if (width === stored.w && height === stored.h) {
                return;
              }

              stored.w = width;
              stored.h = height;
              currentElement.trigger(resizeEvent, [stored.w, stored.h]);
            });

            poll();
          }, resizeState[delayKey]);
        })();
      }
    },

    teardown: function () {
      if (!resizeState[throttleKey] && this[timeoutMethod]) {
        return !1;
      }

      var element = $(this);
      trackedElements = trackedElements.not(element);
      element.removeData(dataKey);

      if (!trackedElements.length) {
        clearTimeout(timeoutId);
      }
    },

    add: function (handler) {
      if (!resizeState[throttleKey] && this[timeoutMethod]) {
        return !1;
      }

      var originalHandler;

      function wrappedHandler(event, width, height) {
        var element = $(this);
        var stored = $.data(this, dataKey);

        stored.w = typeof width !== "undefined" ? width : element.width();
        stored.h = typeof height !== "undefined" ? height : element.height();

        originalHandler.apply(this, arguments);
      }

      if ($.isFunction(handler)) {
        originalHandler = handler;
        return wrappedHandler;
      }

      originalHandler = handler.handler;
      handler.handler = wrappedHandler;
    },
  };
})(jQuery, this);
