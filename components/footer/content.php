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
    <?php if ($footerRecordsHtml !== ''): ?>
        <p id="footer-records" class="footer-line">
            <?php echo $footerRecordsHtml; ?>
        </p>
    <?php endif; ?>
</footer><!-- end #footer -->
