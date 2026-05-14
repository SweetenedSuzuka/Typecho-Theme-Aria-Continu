<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</div><!-- end .row -->
</div><!-- end .container -->
</div><!-- end #body -->
</div><!-- end #pjax-container -->
<?php
$footerRecordsEnabled = Utils::isOptionEnabled('footerRecordsEnabled', true);
$footerRecordsHtml = $footerRecordsEnabled ? Utils::getFooterRecordsHtml() : '';
include __DIR__ . '/components/footer/content.php';
?>
<!-- pajx -->
<?php if (Utils::isEnabled('enablePjax', 'AriaConfig')): ?>
    <script src="<?php $this->options->themeUrl('assets/js/jquery.pjax.min.js'); ?>"></script>
<?php endif; ?>
<!-- fancybox插件 -->
<?php if (Utils::isEnabled('enableFancybox', 'AriaConfig')): ?>
    <script src="<?php $this->options->themeUrl('assets/js/jquery.fancybox.min.js'); ?>"></script>
<?php endif; ?>
<!-- highlight高亮 懒加载 -->
<script src="<?php $this->options->themeUrl('assets/js/highlight.min.js'); ?>"></script>
<?php if (Utils::isEnabled('enableLazyload', 'AriaConfig')): ?>
    <script src="<?php $this->options->themeUrl('assets/js/jquery.lazyload.min.js'); ?>"></script>
<?php endif; ?>
<!-- 评论颜文字 -->
<script src="<?php $this->options->themeUrl('assets/OwO/OwO.min.js') ?>"></script>
<?php if (Utils::isEnabled('enableMathJax', 'AriaConfig')): ?>
    <script>
        window.ariaEnsureMathJaxCompat = window.ariaEnsureMathJaxCompat || function () {
            window.MathJax = window.MathJax || {};
            window.MathJax.tex = window.MathJax.tex || {};
            window.MathJax.options = window.MathJax.options || {};
            window.MathJax.Hub = window.MathJax.Hub || {};
            window.MathJax.Hub.Config = window.MathJax.Hub.Config || function (config) {
                if (!config || !config.tex2jax) {
                    return;
                }
                var tex2jax = config.tex2jax || {};
                if (tex2jax.inlineMath) {
                    window.MathJax.tex.inlineMath = tex2jax.inlineMath;
                }
                if (tex2jax.displayMath) {
                    window.MathJax.tex.displayMath = tex2jax.displayMath;
                }
                if (typeof tex2jax.processEscapes !== 'undefined') {
                    window.MathJax.tex.processEscapes = tex2jax.processEscapes;
                }
            };
            window.MathJax.Hub.Queue = window.MathJax.Hub.Queue || function () {
                if (!window.MathJax) {
                    return;
                }
                if (typeof window.MathJax.typesetPromise === 'function') {
                    var container = document.getElementById('pjax-container');
                    return window.MathJax.typesetPromise(container ? [container] : undefined);
                }
            };
        };
        window.ariaTypesetMathJax = window.ariaTypesetMathJax || function (targets) {
            if (!window.MathJax) {
                return;
            }

            if (typeof window.MathJax.typesetPromise === 'function') {
                if (!targets) {
                    return window.MathJax.typesetPromise();
                }

                if (!Array.isArray(targets)) {
                    targets = [targets];
                }

                targets = targets.filter(function (target) {
                    return target && target.nodeType === 1;
                });

                return window.MathJax.typesetPromise(targets.length ? targets : undefined);
            }

            if (window.MathJax.Hub && typeof window.MathJax.Hub.Queue === 'function') {
                return window.MathJax.Hub.Queue(['Typeset', window.MathJax.Hub]);
            }
        };
        window.ariaEnsureMathJaxCompat();
        (function(){var cls='aria-mathjax-ignore';var opt=window.MathJax&&window.MathJax.options;opt=opt||{};window.MathJax.options=opt;var cur=opt.ignoreHtmlClass;if(typeof cur!=='string'||cur.trim()===''){opt.ignoreHtmlClass='tex2jax_ignore|'+cls;return}if(!new RegExp('(^|\\\\|)'+cls+'($|\\\\|)').test(cur)){opt.ignoreHtmlClass=cur+'|'+cls}})();
    </script>
    <script><?php
        $mathJaxConfig = isset($this->options->MathJaxConfig) ? trim((string) $this->options->MathJaxConfig) : '';
        if ($mathJaxConfig === '') {
            $mathJaxConfig = "MathJax = MathJax || {};\nMathJax.tex = MathJax.tex || {};\nMathJax.tex.inlineMath = [['$', '$'], ['\\\\(', '\\\\)']];\nMathJax.tex.displayMath = [['$$', '$$'], ['\\\\[', '\\\\]']];\nMathJax.tex.processEscapes = true;";
        }
        echo $mathJaxConfig;
    ?></script>
    <script>window.ariaEnsureMathJaxCompat&&window.ariaEnsureMathJaxCompat();</script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathjax@4.1.2/tex-mml-chtml.js"></script>
<?php endif; ?>
<script src="<?php $this->options->themeUrl('assets/js/functions.min.js?v=8b426df9ab'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/main.min.js?v=de446d9d66'); ?>"></script>
<?php if (Utils::isEnabled('enableMathJax', 'AriaConfig') && Utils::isEnabled('enableMathJaxInComments', 'AriaConfig') && Utils::isEnabled('enableAjaxComment', 'AriaConfig')): ?>
    <script>
        (function () {
            var observer = null;
            var queuedTargets = [];
            var flushTimer = null;

            function hasIgnoreClass(node) {
                return !!(node && node.closest && node.closest('.aria-mathjax-ignore'));
            }

            function queueTarget(node) {
                if (!node || node.nodeType !== 1 || hasIgnoreClass(node)) {
                    return;
                }

                if (node.tagName && node.tagName.toLowerCase() === 'mjx-container') {
                    return;
                }

                queuedTargets.push(node);
                if (flushTimer !== null) {
                    return;
                }

                flushTimer = window.setTimeout(flushTargets, 60);
            }

            function flushTargets() {
                var commentsRoot = document.getElementById('comments');
                var targets = queuedTargets.slice();

                flushTimer = null;
                queuedTargets = [];

                if (!commentsRoot || !targets.length || hasIgnoreClass(commentsRoot)) {
                    return;
                }

                if (observer) {
                    observer.disconnect();
                }

                Promise.resolve(window.ariaTypesetMathJax && window.ariaTypesetMathJax(targets))
                    .then(function () {
                        if (observer) {
                            observer.observe(commentsRoot, { childList: true, subtree: true });
                        }
                    }, function () {
                        if (observer) {
                            observer.observe(commentsRoot, { childList: true, subtree: true });
                        }
                    });
            }

            function installObserver() {
                var commentsRoot = document.getElementById('comments');
                if (!commentsRoot || hasIgnoreClass(commentsRoot) || typeof MutationObserver === 'undefined') {
                    return;
                }

                if (observer) {
                    observer.disconnect();
                }

                observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        Array.prototype.forEach.call(mutation.addedNodes, function (node) {
                            queueTarget(node);
                        });
                    });
                });

                observer.observe(commentsRoot, { childList: true, subtree: true });
            }

            installObserver();

            if (window.jQuery) {
                window.jQuery(document).on('pjax:complete', function () {
                    window.setTimeout(installObserver, 0);
                });
            }
        })();
    </script>
<?php endif; ?>
<?php echo $this->options->customScript ? "<script>" . $this->options->customScript . "</script>\n" : ""; ?>
<?php if ($this->options->statistics) $this->options->statistics(); ?>
<?php $this->footer(); ?>
</body>

</html>
