<?php
/**
 * 书写属于自己的篇章
 * 让每个期许不再落幕 
 * 
 * <a href="https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo" target="_blank">Github</a> | <a href="https://suzuka.cc" target="_blank">Home</a>
 * 
 * @package Aria Continuo
 * @author SweetenedSuzuka
 * @version 1.13.1
 * @link https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo
 * 
 * Based on Aria by Siphils
 * <a href="https://github.com/Siphils/Typecho-Theme-Aria" target="_blank">Original Github</a> | <a href="https://eriri.ink" target="_blank">Original Home</a>
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 $this->need('header.php');
 ?>

<div id="main" class="col-mb-12 col-8 col-offset-2" >
    <?php
    $homeExcludeEnabled = Utils::isOptionEnabled('homeExcludeCategoriesEnabled', true);
    $homeExcludeCategoriesText = Utils::hasOption('homeExcludeCategories')
        ? Utils::getOptionStringValue('homeExcludeCategories', '', false)
        : '填写分类的缩略名，可以参见Typecho后台的管理-分类';
    $homeExcludeSlugs = $homeExcludeEnabled ? Utils::splitOptionList($homeExcludeCategoriesText) : array();
    ?>
	<?php while($this->next()): ?>
        <?php
        $postCategorySlug = isset($this->category) ? (string) $this->category : '';
        if ($postCategorySlug !== '' && in_array($postCategorySlug, $homeExcludeSlugs, true)) {
            continue;
        }
        $postCardViewData = Utils::getPostCardViewData($this, 'index');
        ?>
        <?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'post-card.php'; ?>
	<?php endwhile; ?>

     <div id="page-nav">
        <?php $this->pageNav('<', '>',1,'...',array('wrapTag' => 'ul', 'wrapClass' => '','itemTag' => 'li','currentClass' => 'page-current',)); ?>  
     </div>
</div><!-- end #main-->

<?php $this->need('footer.php'); ?>
