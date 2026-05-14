<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div id="main" class="col-mb-12 col-8 col-offset-2">
	<style>#header{height:70vh}#site-meta{display:none}#background{background:url(<?php $this->options->themeUrl('assets/img/404.jpg');?>) center center no-repeat;background-size:cover;z-index:-1;position:relative}.error-page{margin-bottom:30px}input[type="text"]{padding:10px}.submit{width:50%;max-width:200px}</style>
	<div class="error-page">
		<?php
        $notFoundTitle = Utils::getOptionStringValue('notFoundTitle', '404:没有找到界面呢，是书架摆错了吗？');
        ?>
		<h2 class="post-title"><?php echo htmlspecialchars($notFoundTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
		<p>
			<?php
            $notFoundDescription = Utils::getOptionStringValue('notFoundDescription', '这个页面不存在或者被删除，你可以尝试搜索你想要的内容。');
            echo htmlspecialchars($notFoundDescription, ENT_QUOTES, 'UTF-8');
            ?>
		</p>
		<form method="post">
			<p>
				<input type="text" name="s" class="text" autofocus />
			</p>
			<p>
				<center>
					<button type="submit" class="submit">
						<?php _e('搜索'); ?>
					</button>
				</center>
			</p>
		</form>
	</div>

</div>
<!-- end #content-->
<?php $this->need('footer.php'); 
