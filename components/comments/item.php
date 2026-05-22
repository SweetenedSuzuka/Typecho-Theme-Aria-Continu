<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<li id="li-<?php $comments->theId(); ?>" class="comment-body<?php
if ($comments->levels > 0) {
    echo ' comment-child';
    $comments->levelsAlt(' comment-level-odd', ' comment-level-even');
} else {
    echo ' comment-parent';
}
$comments->alt(' comment-odd', ' comment-even');
echo $commentClass;
?>">
    <div id="<?php $comments->theId(); ?>">
        <a class="comment-avatar" href="<?php $comments->permalink(); ?>">
            <?php $comments->gravatar('120', ''); ?>
        </a>
        <div class="comment-content">
            <div class="comment-text"><span class="comment-reply" style="float:right"><a href="#comment-form" data-aria-action="comment-reply" data-parent-id="<?php echo (int) $comments->coid; ?>"><i class="iconfont icon-aria-reply"></i></a></span>
            <?php
            $commentContent = $comments->content;
            if ('waiting' == $comments->status) {
                $waitingText = isset($commentsViewData['waitingText']) ? (string) $commentsViewData['waitingText'] : '';
                if ($waitingText !== '') {
                    echo '<p><em>' . htmlspecialchars($waitingText, ENT_QUOTES, 'UTF-8') . '</em></p>';
                }
            }
            ?><?php echo $commentContent; ?>
            </div>
<?php
$commentAuthor = htmlspecialchars((string) $comments->author, ENT_QUOTES, 'UTF-8');
$commentUrl = trim((string) $comments->url);
?>
<p class="comment-meta">By <span><?php if ($commentUrl !== ''): ?><a href="<?php echo htmlspecialchars($commentUrl, ENT_QUOTES, 'UTF-8'); ?>" rel="external nofollow" target="_blank"><?php echo $commentAuthor; ?></a><?php else: ?><?php echo $commentAuthor; ?><?php endif; ?></span> 于 <?php $comments->date(); ?>. <?php if (!empty($commentsViewData['showUserAgent'])): ?><span class="comment-ua"><?php echo Comments::parseUserAgent($comments->agent); ?></span><?php endif; ?></p>
        </div>
    </div><!-- 单条评论者信息及内容 -->
    <?php if ($comments->children) { ?>
    <div class="comment-children">
        <?php $comments->threadedComments($singleCommentOptions); ?>
    </div>
    <?php } ?>
</li>
