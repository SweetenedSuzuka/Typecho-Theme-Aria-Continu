/**
 * Aria front-end bootstrap entry.
 *
 * The actual behavior modules are loaded separately in `assets/js/modules/`
 * and this file only wires the final startup sequence.
 */
var Aria = (window.Aria = window.Aria || {});

window.onload = Aria.init;
