<div id="search-box" class="animated" style="background: #fff">
    <span class="close"><i class="iconfont icon-aria-close"></i></span>
    <form id="search" method="post" action="./" role="search">
        <input type="text" name="s" id="search-text" placeholder="<?php echo htmlspecialchars($headerViewData['search']['placeholder'], ENT_QUOTES, 'UTF-8'); ?>" />
        <button type="submit" id="search-button" style="background: url(<?php echo htmlspecialchars($headerViewData['search']['buttonBackgroundUrl'], ENT_QUOTES, 'UTF-8'); ?>) center center no-repeat;background-size: cover;"></button>
    </form>
</div>
