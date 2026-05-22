<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeMathJax.php
 * 主题 MathJax 辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.18.0
 */
class ThemeMathJax
{
    /**
     * 获取默认 MathJax 配置脚本
     *
     * @return string
     */
    public static function getDefaultConfigScript()
    {
        return "MathJax = MathJax || {};\n"
            . "MathJax.tex = MathJax.tex || {};\n"
            . "MathJax.tex.inlineMath = [['$', '$'], ['\\\\(', '\\\\)']];\n"
            . "MathJax.tex.displayMath = [['$$', '$$'], ['\\\\[', '\\\\]']];\n"
            . "MathJax.tex.processEscapes = true;";
    }

    /**
     * 获取归一化后的 MathJax 配置脚本
     *
     * @return string
     */
    public static function getConfigScript()
    {
        return ThemeOptions::getOptionStringValue(
            'MathJaxConfig',
            self::getDefaultConfigScript()
        );
    }

    /**
     * 获取 MathJax 兼容层脚本
     *
     * @return string
     */
    public static function getCompatScript()
    {
        return <<<'JS'
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
            return window.MathJax.typesetPromise();
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
(function () {
    var cls = 'aria-mathjax-ignore';
    var opt = window.MathJax && window.MathJax.options;
    opt = opt || {};
    window.MathJax.options = opt;
    var cur = opt.ignoreHtmlClass;
    if (typeof cur !== 'string' || cur.trim() === '') {
        opt.ignoreHtmlClass = 'tex2jax_ignore|' + cls;
        return;
    }
    if (!new RegExp('(^|\\\\|)' + cls + '($|\\\\|)').test(cur)) {
        opt.ignoreHtmlClass = cur + '|' + cls;
    }
})();
JS;
    }

    /**
     * 获取 MathJax 兼容层补执行脚本
     *
     * @return string
     */
    public static function getEnsureScript()
    {
        return 'window.ariaEnsureMathJaxCompat&&window.ariaEnsureMathJaxCompat();';
    }

    /**
     * 获取评论区 MathJax 补排版脚本
     *
     * @return string
     */
    public static function getCommentObserverScript()
    {
        return <<<'JS'
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

})();
JS;
    }

    /**
     * 获取 MathJax 视图数据
     *
     * @return array
     */
    public static function getViewData()
    {
        $enabled = ThemeOptions::isMathJaxEnabled();

        return array(
            'enabled' => $enabled,
            'enabledInComments' => $enabled && ThemeOptions::isMathJaxInCommentsEnabled(),
            'configScript' => self::getConfigScript(),
            'compatScript' => self::getCompatScript(),
            'ensureScript' => self::getEnsureScript(),
            'commentObserverScript' => $enabled
                && ThemeOptions::isMathJaxInCommentsEnabled()
                && ThemeOptions::isAjaxCommentEnabled()
                ? self::getCommentObserverScript()
                : '',
            'cdnUrl' => 'https://cdn.jsdelivr.net/npm/mathjax@4.1.2/tex-mml-chtml.js',
        );
    }
}
