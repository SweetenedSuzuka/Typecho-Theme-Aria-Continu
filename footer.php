<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</div><!-- end .row -->
</div><!-- end .container -->
</div><!-- end #body -->
</div><!-- end #pjax-container -->
<div id="go-top" onclick="goTop(this);">
    <img no-lazyload src="<?php $this->options->themeUrl('assets/img/goTop.png'); ?>">
    <!--div id="scroll-percentage"></div-->
</div>
<footer id="footer" role="contentinfo">
    <?php $this->options->customFooter(); ?>
    <!-- 调用一言接口 -->
    <?php if (Utils::isEnabled('showHitokoto', 'AriaConfig')): ?>
        <p id="hitokoto" class="footer-line"></p>
    <?php endif; ?>
    <p id="footer-info" class="footer-line">
        <span>&copy; <span><?php echo Utils::getCopyrightYears(); ?></span></span>
        <?php Utils::getFooterWidget(); ?>
    </p>
    <?php
    $footerRecordsEnabled = true;
    if (isset($this->options->footerRecordsEnabled)) {
        $footerRecordsEnabled = trim((string) $this->options->footerRecordsEnabled) === '1';
    }
    $footerRecordsHtml = $footerRecordsEnabled ? Utils::getFooterRecordsHtml() : '';
    ?>
    <?php if ($footerRecordsHtml !== ''): ?>
        <p id="footer-records" class="footer-line">
            <?php echo $footerRecordsHtml; ?>
        </p>
    <?php endif; ?>
</footer><!-- end #footer -->
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
        window.ariaEnsureMathJaxCompat();
    </script>
    <script><?php $this->options->MathJaxConfig(); ?></script>
    <script>window.ariaEnsureMathJaxCompat&&window.ariaEnsureMathJaxCompat();</script>
    <script defer src="https://cdn.jsdelivr.net/npm/mathjax@4.1.2/tex-mml-chtml.js"></script>
<?php endif; ?>
<script src="<?php $this->options->themeUrl('assets/js/functions.min.js?v=8b426df9ab'); ?>"></script>
<script src="<?php $this->options->themeUrl('assets/js/main.min.js?v=de446d9d66'); ?>"></script>
<?php echo $this->options->customScript ? "<script>" . $this->options->customScript . "</script>\n" : ""; ?>
<?php if ($this->options->statistics) $this->options->statistics(); ?>
<?php $this->footer(); ?>
</body>

</html>
