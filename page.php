<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php $postViewData = ThemeViewData::getPostViewData($this, 'page'); ?>

<div id="main" class="col-mb-12 col-8 col-offset-2">
    <?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'post-content.php'; ?>
    <?php $this->need('comments.php'); ?>

</div><!-- end #main-->
<?php echo $postViewData['tocHtml']; ?>
<?php $this->need('footer.php'); ?>
