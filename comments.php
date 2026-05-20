<?php function threadedComments($comments, $singleCommentOptions) {
    $commentClass = '';
    if ($comments->authorId) {
        if ($comments->authorId == $comments->ownerId) {
            $commentClass .= ' comment-by-author';  //如果是文章作者的评论添加 .comment-by-author 样式
        } else {
            $commentClass .= ' comment-by-user';  //如果是评论作者的添加 .comment-by-user 样式
        }
    }
?>
<?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'comments' . DIRECTORY_SEPARATOR . 'item.php'; ?>
<?php } ?>

<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<?php $commentsViewData = ThemeViewData::getCommentsViewData(); ?>

<div id="comments"<?php echo $commentsViewData['ignoreMathJax'] ? ' class="aria-mathjax-ignore"' : ''; ?>>
	<?php if($this->allow('comment')): ?>

	<?php $this->comments()->to($comments); ?>
	<span id="response">
		<p>
			<i class="iconfont icon-aria-comment"></i>
			<?php $this->commentsNum(_t('0 条评论'), _t('1 条评论'), _t('%d 条评论')); ?>
		</p>	
	</span>
		
		<?php if ($comments->have()): ?>
		
	<div class="comment-data">

		<?php $comments->listComments(); ?>

	</div>
		<div id="page-nav">
			<?php $comments->pageNav('<', '>',1,'...',array('wrapTag' => 'ul', 'wrapClass' => '','itemTag' => 'li','currentClass' => 'page-current',)); ?>
		</div>

		<?php endif; ?>
	<?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'comments' . DIRECTORY_SEPARATOR . 'form.php'; ?>
	<?php else: ?>
    <?php include __DIR__ . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'comments' . DIRECTORY_SEPARATOR . 'closed.php'; ?>
    <?php endif; ?>
</div>
