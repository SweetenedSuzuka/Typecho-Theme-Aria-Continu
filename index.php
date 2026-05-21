<?php
/**
 * 书写属于自己的篇章
 * 让每个期许不再落幕 
 * 
 * <a href="https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo" target="_blank">Github</a> | <a href="https://suzuka.cc" target="_blank">Home</a>
 * 
 * @package Aria Continuo
 * @author SweetenedSuzuka
 * @version 1.16.0
 * @link https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo
 * 
 * Based on Aria by Siphils
 * <a href="https://github.com/Siphils/Typecho-Theme-Aria" target="_blank">Original Github</a> | <a href="https://eriri.ink" target="_blank">Original Home</a>
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 $this->need('header.php');
 ?>

<div id="main" class="col-mb-12 col-8 col-offset-2" >
	<?php while($this->next()): ?>
        <?php if (ThemeViewData::shouldSkipHomePost($this)) continue; ?>
        <?php $postCardViewData = ThemeViewData::getPostCardViewData($this, 'index'); ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'post-card.php'; ?>
	<?php endwhile; ?>

     <div id="page-nav">
        <?php $this->pageNav('<', '>',1,'...',array('wrapTag' => 'ul', 'wrapClass' => '','itemTag' => 'li','currentClass' => 'page-current',)); ?>  
     </div>
</div><!-- end #main-->

<?php $this->need('footer.php'); ?>
