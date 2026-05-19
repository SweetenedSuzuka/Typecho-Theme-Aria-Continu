<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
$postCardViewData = isset($postCardViewData) && is_array($postCardViewData) ? $postCardViewData : array();
$ariaCardCategorySeparator = isset($postCardViewData['categorySeparator'])
    ? (string) $postCardViewData['categorySeparator']
    : (isset($ariaCardCategorySeparator) ? (string) $ariaCardCategorySeparator : ' • ');
$ariaCardUseLazyload = isset($postCardViewData['useLazyload'])
    ? !empty($postCardViewData['useLazyload'])
    : !empty($ariaCardUseLazyload);
$ariaCardShowLine = isset($postCardViewData['showLine'])
    ? !empty($postCardViewData['showLine'])
    : !empty($ariaCardShowLine);
$ariaCardMoreTitle = isset($postCardViewData['moreTitle'])
    ? (string) $postCardViewData['moreTitle']
    : (isset($ariaCardMoreTitle) ? (string) $ariaCardMoreTitle : '');
$ariaCardThumbnail = isset($postCardViewData['thumbnailUrl'])
    ? trim((string) $postCardViewData['thumbnailUrl'])
    : Utils::getThumbnail();
$ariaCardLoadingImageUrl = isset($postCardViewData['loadingImageUrl'])
    ? trim((string) $postCardViewData['loadingImageUrl'])
    : Utils::getThemeAssetUrl('assets/img/loading.svg');
?>
<article itemscope itemtype="http://schema.org/BlogPosting" class="card animated wow fadeIn" data-wow-duration="1s" data-wow-offset="10">
    <div class="card-title">
        <a href="<?php $this->permalink(); ?>"><?php $this->sticky(); $this->title(); ?></a>
    </div>
    <div class="card-meta-top">
        <span class="card-meta-cate"><i class="iconfont icon-aria-category"></i> <?php $this->category($ariaCardCategorySeparator, true, '无'); ?></span><span class="card-meta-date"><i class="iconfont icon-aria-date"></i> <?php $this->date(); ?></span>
    </div>
    <?php if ($ariaCardUseLazyload): ?>
        <a href="<?php $this->permalink(); ?>">
            <div class="card-thumbnail" data-aria-lazy-background="<?php echo htmlspecialchars($ariaCardThumbnail, ENT_QUOTES, 'UTF-8'); ?>" style="background:url(<?php echo htmlspecialchars($ariaCardLoadingImageUrl, ENT_QUOTES, 'UTF-8'); ?>) center center no-repeat;background-size: 100% auto;">
            </div>
        </a>
    <?php else: ?>
        <a class="card-thumbnail" href="<?php $this->permalink(); ?>" style="background:url(<?php echo htmlspecialchars($ariaCardThumbnail, ENT_QUOTES, 'UTF-8'); ?>) center center no-repeat;background-size: 100% auto;">
        </a>
    <?php endif; ?>
    <div class="card-body">
        <?php
        if ($this->fields->previewContent) {
            $this->fields->previewContent();
        } else {
            $this->excerpt(50, '...');
        }
        ?>
    </div>
    <?php if ($ariaCardShowLine): ?>
        <div class="card-line"></div>
    <?php endif; ?>
    <ul class="card-meta-bottom">
        <li class="card-meta-label card-meta-more"><a href="<?php $this->permalink(); ?>"<?php if ($ariaCardMoreTitle !== ''): ?> title="<?php echo htmlspecialchars($ariaCardMoreTitle, ENT_QUOTES, 'UTF-8'); ?>"<?php endif; ?> target="_blank"><i class="iconfont icon-aria-more"></i><i class="iconfont icon-aria-more"></i></a></li>
        <li class="card-meta-label card-meta-views card-meta-right"><i class="iconfont icon-aria-view"></i> <?php Contents::getPostView($this); ?></li>
        <li class="card-meta-label card-meta-comments card-meta-right"><i class="iconfont icon-aria-comment"></i> <?php $this->commentsNum('%d'); ?></li>
        <!--li class="card-meta-label card-meta-likes"></li-->
    </ul>
</article>
