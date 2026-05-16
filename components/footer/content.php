<div id="go-top" data-aria-action="go-top">
    <img no-lazyload src="<?php echo htmlspecialchars($footerViewData['goTopImageUrl'], ENT_QUOTES, 'UTF-8'); ?>">
    <!--div id="scroll-percentage"></div-->
</div>
<footer id="footer" role="contentinfo">
    <?php echo $footerViewData['customFooterHtml']; ?>
    <!-- 调用一言接口 -->
    <?php if (!empty($footerViewData['showHitokoto'])): ?>
        <p id="hitokoto" class="footer-line"></p>
    <?php endif; ?>
    <p id="footer-info" class="footer-line">
        <span>&copy; <span><?php echo Utils::getCopyrightYears(); ?></span></span>
        <?php echo $footerViewData['widgetHtml']; ?>
    </p>
    <?php if (!empty($footerViewData['recordsHtml'])): ?>
        <p id="footer-records" class="footer-line">
            <?php echo $footerViewData['recordsHtml']; ?>
        </p>
    <?php endif; ?>
</footer><!-- end #footer -->
