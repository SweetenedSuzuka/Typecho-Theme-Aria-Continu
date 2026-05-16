<div id="wrapper" data-aria-action="toggle-nav"></div>
<div id="nav-vertical">
    <a class="close" href="javascript:void(0);" data-aria-action="toggle-nav"><i class="iconfont icon-aria-close"></i></a>
    <div id="nav-avatar"><img no-lazyload src="<?php echo htmlspecialchars($headerViewData['navigation']['adminAvatarLargeUrl'], ENT_QUOTES, 'UTF-8'); ?>"></div>
    <ul class="nav-vertical-list">
        <?php Utils::showNav(0, $headerViewData['navigation']['slugs']); ?>
    </ul>
</div>
		<!--[if lt IE 8]>
    <div class="browsehappy" role="dialog"><?php _e('当前网页 <strong>不支持</strong> 你正在使用的浏览器. 为了正常的访问, 请 <a href="http://browsehappy.com/">升级你的浏览器</a>'); ?>.</div>
	<![endif]-->
<div id="nav-menu" role="navigation">
    <div id="nav-left">
        <a href="<?php $this->options->siteUrl(); ?>"><img id="site-avatar" no-lazyload src="<?php echo htmlspecialchars($headerViewData['navigation']['adminAvatarUrl'], ENT_QUOTES, 'UTF-8'); ?>">
<?php $this->options->title(); ?></a>
    </div>
    <div id="nav-right">
        <ul class="nav-right-list">
            <?php Utils::showNav(1, $headerViewData['navigation']['slugs']); ?>
        </ul>
    <div id="nav-btns">
        <i class="iconfont icon-aria-menu" id="nav-menu-btn" data-aria-action="toggle-nav"></i>
        <i class="iconfont icon-aria-search" id="nav-search-btn"></i>
    </div>
    </div>
</div>
