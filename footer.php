<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
</div><!-- end .row -->
</div><!-- end .container -->
</div><!-- end #body -->
</div><!-- end #pjax-container -->
<?php
$footerViewData = Utils::getFooterViewData();
$mathJaxViewData = Utils::getMathJaxViewData();
include __DIR__ . '/components/footer/content.php';
?>
<?php foreach ($footerViewData['scripts'] as $scriptUrl): ?>
    <script src="<?php echo htmlspecialchars($scriptUrl, ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php endforeach; ?>
<?php echo $footerViewData['customScriptHtml']; ?>
<script src="<?php echo htmlspecialchars($footerViewData['mainScriptUrl'], ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php if ($mathJaxViewData['enabled']): ?>
    <script><?php echo $mathJaxViewData['compatScript']; ?></script>
    <script><?php echo $mathJaxViewData['configScript']; ?></script>
    <script><?php echo $mathJaxViewData['ensureScript']; ?></script>
    <script defer src="<?php echo htmlspecialchars($mathJaxViewData['cdnUrl'], ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php endif; ?>
<?php if ($mathJaxViewData['commentObserverScript'] !== ''): ?>
    <script><?php echo $mathJaxViewData['commentObserverScript']; ?></script>
<?php endif; ?>
<?php echo $footerViewData['statisticsHtml']; ?>
<?php $this->footer(); ?>
</body>

</html>
