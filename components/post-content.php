<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<article class="post" itemscope itemtype="http://schema.org/BlogPosting">
    <div class="post-header">
        <h3 class="post-title"><a href="<?php $this->permalink() ?>" class="post-link"><?php $this->title() ?></a></h3>
        <div class="post-meta">
            <span class="post-meta-label post-meta-views"><?php echo (int) $postViewData['meta']['viewCount']; ?><?php echo htmlspecialchars($postViewData['meta']['viewsSuffix'], ENT_QUOTES, 'UTF-8'); ?></span>
            <?php if (!empty($postViewData['meta']['showCategory'])): ?>
            <span class="post-meta-label post-meta-cate"><?php $this->category($postViewData['meta']['categorySeparator']); ?></span>
            <?php endif; ?>
            <span class="post-meta-label post-meta-date"><?php $this->date(); ?></span>
        </div>
    </div>
    <div class="post-body">
        <div class="post-content">
            <?php $this->content(); ?>
        </div>
        <?php echo $postViewData['postOtherHtml']; ?>
        <div class="post-update">
            <?php /* ?><i class="iconfont icon-aria-date"></i>&nbsp;最后一次更新于<?php echo date($this->options->postDateFormat,$this->modified) ?><?php */ ?>
        </div>
    </div>
    <?php if (!empty($postViewData['showTags'])): ?>
    <div class="post-tags">
        <?php $this->tags(' ', true, '<a>None</a>'); ?>
        <a class="post-zan"><i class="iconfont icon-aria-like"></i></a>
        <?php //Typecho_Widget::widget('Zan_Action')->showZan($this->cid); ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($postViewData['showNextPrev'])): ?>
    <div class="post-footer nextprev">
        <?php echo $postViewData['nextPrevHtml']; ?>
    </div>
    <?php endif; ?>
</article>
