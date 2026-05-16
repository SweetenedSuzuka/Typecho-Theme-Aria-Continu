<?php
/**
 * 归档页面 时间轴
 * 
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
 $this->need('header.php');
 ?>
<div id="main" class="col-mb-12 col-8 col-offset-2 archives-page">
    <?php $this->widget('Widget_Metas_Tag_Cloud', 'ignoreZeroCount=0')->to($tags); ?>
    <!-- 原主题输出TAG位置在此（分类上面） -->
    <!-- 输出所有分类 -->
    <div id="archives-categories" class="animated bounceInRight">
        <div>分类</div>
        <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
        <ul id="archives-cate-list">
            <?php while ($category->next()): ?>
            <li class="archives-cate-item"><a href="<?php $category->permalink(); ?>" target="_blank"><?php $category->name(); ?></a></li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div id="timeline-container" class="animated bounceInLeft">
        <?php echo Contents::getArchiveTimelineHtml(); ?>
    </div><!-- end timeline container -->
    <!-- 输出所有TAG -->
    <?php if($tags->have()): ?>
    <div id="archives-tags" class="animated bounceInLeft">
        <div>标签Tag</div>
        <ul id="archives-tags-list">
        <?php while($tags->next()): ?>
            <li class="archives-tags-item"><a href="<?php $tags->permalink(); ?>" target="_blank"><?php $tags->name(); ?></a></li>
        <?php endwhile; ?>
        </ul>
    </div>
    <?php endif; ?>
</div><!-- end #main-->

<?php $this->need('footer.php'); ?>
