<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php $archiveHeaderViewData = ThemeViewData::getArchiveHeaderViewData($this); ?>

<div id="main" class="col-mb-12 col-8 col-offset-2" >
    <div style="border-radius: 5px;
    background-color: #fff;
    margin: 30px 0;
    color: rgba(0,0,0,.7);
    padding: 15px;">
    <?php echo $archiveHeaderViewData['titleHtml']; ?><br><?php echo $archiveHeaderViewData['descriptionHtml']; ?>
    </div>
    <?php while($this->next()): ?>
        <?php
        $postCardViewData = ThemeViewData::getPostCardViewData($this, 'archive');
        ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'post-card.php'; ?>
    <?php endwhile; ?>

     <div id="page-nav">
        <?php $this->pageNav('<', '>',1,'...',array('wrapTag' => 'ul', 'wrapClass' => '','itemTag' => 'li','currentClass' => 'page-current',)); ?>  
     </div>
</div><!-- end #main-->

	
	<?php $this->need('footer.php'); ?>
