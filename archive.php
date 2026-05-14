<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div id="main" class="col-mb-12 col-8 col-offset-2" >
    <div style="border-radius: 5px;
    background-color: #fff;
    margin: 30px 0;
    color: rgba(0,0,0,.7);
    padding: 15px;">
    <?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ''); ?><br><?php echo $this->getDescription(); ?>
    </div>
    <?php while($this->next()): ?>
        <?php
        $ariaCardCategorySeparator = ' ';
        $ariaCardUseLazyload = false;
        $ariaCardShowLine = false;
        $ariaCardMoreTitle = '';
        ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'post-card.php'; ?>
    <?php endwhile; ?>

     <div id="page-nav">
        <?php $this->pageNav('<', '>',1,'...',array('wrapTag' => 'ul', 'wrapClass' => '','itemTag' => 'li','currentClass' => 'page-current',)); ?>  
     </div>
</div><!-- end #main-->

	
	<?php $this->need('footer.php'); ?>
