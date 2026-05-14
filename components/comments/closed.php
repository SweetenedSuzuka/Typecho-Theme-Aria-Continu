<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$commentClosedText = Utils::getOptionStringValue('commentClosedText', '评论关闭了哟', false);
?>
<?php if ($commentClosedText !== ''): ?>
    <span style="font-size: 20px;display: block;user-select: none;"><i class="iconfont icon-aria-close" sytle="font-size:20px"></i> <?php echo htmlspecialchars($commentClosedText, ENT_QUOTES, 'UTF-8'); ?></span>
<?php endif; ?>
