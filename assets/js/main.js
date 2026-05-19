/**
 * Aria front-end bootstrap entry.
 *
 * The actual behavior modules are loaded separately in `assets/js/modules/`
 * and this file only wires the final startup sequence.
 */
var Aria = (window.Aria = window.Aria || {});

function bootstrapAria() {
  if (Aria.state && Aria.state.bootstrapped) {
    return;
  }

  Aria.state.bootstrapped = !0;
  if (Aria.compat && typeof Aria.compat.installLegacyGlobals === 'function') {
    Aria.compat.installLegacyGlobals();
  }
  Aria.init();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootstrapAria, { once: true });
} else {
  bootstrapAria();
}
