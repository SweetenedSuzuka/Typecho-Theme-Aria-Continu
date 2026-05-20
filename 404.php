<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $GLOBALS['ARIA_IS_404_PAGE'] = true; ?>
<?php $this->need('header.php'); ?>
<div id="main" class="col-mb-12 col-8 col-offset-2 error-page-main">
	<div class="error-page">
		<?php
        $notFoundTitle = ThemeOptions::getOptionStringValue('notFoundTitle', '404:没有找到界面呢，是书架摆错了吗？');
        ?>
		<h2 class="post-title"><?php echo htmlspecialchars($notFoundTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
		<p>
			<?php
            $notFoundDescription = ThemeOptions::getOptionStringValue('notFoundDescription', '这个页面不存在或者被删除，你可以尝试搜索你想要的内容。');
            echo htmlspecialchars($notFoundDescription, ENT_QUOTES, 'UTF-8');
            ?>
		</p>
		<form method="post">
			<p>
				<input type="text" name="s" class="text error-page-search-input" autofocus />
			</p>
			<div class="error-page-search-actions">
				<button type="submit" class="submit error-page-search-submit">
					<?php _e('搜索'); ?>
				</button>
			</div>
		</form>
	</div>

</div>
<!-- end #content-->
<?php $this->need('footer.php'); 
