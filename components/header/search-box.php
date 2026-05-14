<div id="search-box" class="animated" style="background: #fff">
    <span class="close"><i class="iconfont icon-aria-close"></i></span>
    <form id="search" method="post" action="./" role="search">
        <input type="text" name="s" id="search-text" placeholder="<?php echo htmlspecialchars($searchPlaceholder, ENT_QUOTES, 'UTF-8'); ?>" />
        <button type="submit" id="search-button" style="background: url(<?php $this->options->themeUrl('assets/img/search.png') ?>) center center no-repeat;background-size: cover;"></button>
    </form>
</div>
