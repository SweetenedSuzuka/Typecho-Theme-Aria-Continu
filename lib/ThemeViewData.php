<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeViewData.php
 * 主题视图数据辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.16.0
 */
class ThemeViewData
{
    /**
     * HTML 文本转义
     *
     * @param string $value
     *
     * @return string
     */
    private static function escapeHtml($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * HTML 属性值转义
     *
     * @param string $value
     *
     * @return string
     */
    private static function escapeAttr($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * 获取页头视图数据
     *
     * @param Widget_Archive $archive
     * @param bool $is404Page
     *
     * @return array
     */
    public static function getHeaderViewData($archive, $is404Page = false)
    {
        $isContentHeroPage = $archive->is('post')
            || $archive->is('page')
            || $archive->is('single')
            || $archive->is('archive');
        $options = Helper::options();
        $headerBackgroundUrl = self::getHeaderBackgroundUrl($archive, $is404Page);

        return array(
            'head' => array(
                'styles' => self::getHeaderStyleUrls(),
                'scripts' => self::getHeaderScriptUrls(),
                'legacyScripts' => self::getHeaderLegacyScriptUrls(),
                'customHtml' => self::getCustomHeaderHtml(),
            ),
            'body' => array(
                'className' => self::getBodyClassName(),
                'style' => self::getBodyStyle(),
            ),
            'navigation' => array(
                'slugs' => ThemeSiteLookup::getPagesInfo(),
                'adminAvatarUrl' => ThemeSiteLookup::getAdminAvatarUrl(50),
                'adminAvatarLargeUrl' => ThemeSiteLookup::getAdminAvatarUrl(150),
                'siteUrl' => rtrim((string) $options->siteUrl, '/') . '/',
                'siteTitle' => trim((string) $options->title),
            ),
            'search' => array(
                'placeholder' => self::getSearchPlaceholder(),
                'buttonBackgroundUrl' => ThemeAssetHelper::getThemeAssetUrl('assets/img/search.png'),
            ),
            'hero' => array(
                'className' => implode(' ', self::getHeaderClassNames($isContentHeroPage, $is404Page)),
                'backgroundCss' => self::getHeaderBackgroundCss($headerBackgroundUrl),
                'siteTitle' => trim((string) $options->title),
                'subtitle' => self::getHeroSubtitle(),
            ),
        );
    }

    /**
     * 获取评论展示视图数据
     *
     * @return array
     */
    public static function getCommentsViewData()
    {
        $mathJaxEnabled = ThemeOptions::isMathJaxEnabled();
        $mathJaxEnabledInComments = ThemeOptions::isMathJaxInCommentsEnabled();
        $options = Helper::options();
        $commentsRequireMail = !empty($options->commentsRequireMail);
        $commentsRequireUrl = !empty($options->commentsRequireURL);
        $commentsMarkdown = !empty($options->commentsMarkdown);
        $allowedHtmlTags = isset($options->commentsHTMLTagAllowed)
            ? (string) $options->commentsHTMLTagAllowed
            : '';

        return array(
            'ignoreMathJax' => $mathJaxEnabled && !$mathJaxEnabledInComments,
            'waitingText' => ThemeOptions::getOptionStringValue(
                'commentWaitingText',
                '正在思考这条评论和不和谐.jpg（评论正在等待审核）',
                false
            ),
            'closedText' => ThemeOptions::getOptionStringValue('commentClosedText', '评论关闭了哟', false),
            'showUserAgent' => ThemeOptions::isCommentUserAgentEnabled(),
            'form' => array(
                'className' => self::getCommentFormClassName(),
                'style' => self::getCommentFormStyle(),
                'newResponseText' => '添加新评论',
                'requireMail' => $commentsRequireMail,
                'requireUrl' => $commentsRequireUrl,
                'supportsMarkdown' => $commentsMarkdown,
                'markdownGuideUrl' => 'https://guides.github.com/features/mastering-markdown/',
                'markdownHintText' => '评论可以使用 Markdown 语法',
                'supportsImageInsertion' => $commentsMarkdown
                    && $allowedHtmlTags !== ''
                    && strpos($allowedHtmlTags, 'img') !== false,
                'imageInsertText' => '图片',
                'showCommentToMail' => ThemeOptions::isCommentToMailEnabled(),
                'banMailStrongText' => '不接收',
                'banMailLabelText' => '回复邮件通知',
                'guestAvatarPrefix' => __TYPECHO_GRAVATAR_PREFIX__,
                'nicknamePlaceholder' => '（必填）昵称',
                'mailPlaceholder' => ($commentsRequireMail ? '（必填）' : '（选填）') . '邮箱',
                'urlPlaceholder' => ($commentsRequireUrl ? '（必填）' : '（选填）') . '网站',
                'textPlaceholder' => isset($options->placeholder)
                    ? (string) $options->placeholder
                    : '',
                'submitText' => '投送',
            ),
        );
    }

    /**
     * 获取页脚展示视图数据
     *
     * @return array
     */
    public static function getFooterContentViewData()
    {
        $recordsEnabled = ThemeOptions::getCheckboxOptionState('footerRecordsEnabled', true);

        return array(
            'showHitokoto' => ThemeOptions::isHitokotoEnabled(),
            'widgetHtml' => self::getFooterWidgetHtml(),
            'recordsHtml' => $recordsEnabled ? self::getFooterRecordsHtml() : '',
            'customFooterHtml' => self::getCustomFooterHtml(),
            'goTopImageUrl' => ThemeAssetHelper::getThemeAssetUrl('assets/img/goTop.png'),
            'copyrightYears' => self::getCopyrightYears(),
        );
    }

    /**
     * 获取页脚完整视图数据
     *
     * @return array
     */
    public static function getFooterViewData()
    {
        return array_merge(self::getFooterContentViewData(), array(
            'scripts' => ThemeScriptAssets::getFooterScriptUrls(),
            'customScriptHtml' => ThemeScriptAssets::getCustomScriptHtml(),
            'mainScriptUrl' => ThemeScriptAssets::getMainScriptUrl(),
            'statisticsHtml' => ThemeScriptAssets::getStatisticsHtml(),
        ));
    }

    /**
     * 获取文章页与页面页的视图数据
     *
     * @param Widget_Archive $archive
     * @param string $context
     *
     * @return array
     */
    public static function getPostViewData($archive, $context = 'post')
    {
        $isPostContext = $context === 'post';
        $postOtherHtml = $isPostContext ? Contents::getPostOtherHtml($archive) : '';
        $nextPrevHtml = $isPostContext ? Contents::getNextPrevHtml($archive) : '';

        return array(
            'meta' => array(
                'showCategory' => $isPostContext,
                'categorySeparator' => ' • ',
                'viewsSuffix' => '次阅读',
                'viewCount' => Contents::getPostViewCount($archive),
            ),
            'showTags' => $isPostContext,
            'showNextPrev' => $nextPrevHtml !== '',
            'tocHtml' => !empty($archive->fields->showTOC)
                ? '<div class="col-mb-12 col-2 kit-hidden-tb"><div id="toc-container"><div id="toc"></div></div></div>'
                : '',
            'postOtherHtml' => $postOtherHtml,
            'nextPrevHtml' => $nextPrevHtml,
        );
    }

    /**
     * 判断首页文章是否应被排除
     *
     * @param Widget_Archive $archive
     *
     * @return bool
     */
    public static function shouldSkipHomePost($archive)
    {
        $postCategorySlug = isset($archive->category) ? trim((string) $archive->category) : '';
        if ($postCategorySlug === '') {
            return false;
        }

        return in_array($postCategorySlug, ThemeOptions::getHomeExcludeCategorySlugs(), true);
    }

    /**
     * 获取归档页头展示数据
     *
     * @param Widget_Archive $archive
     *
     * @return array
     */
    public static function getArchiveHeaderViewData($archive)
    {
        $titleMappings = array(
            'category' => _t('分类 %s 下的文章'),
            'search' => _t('包含关键字 %s 的文章'),
            'tag' => _t('标签 %s 下的文章'),
            'author' => _t('%s 发布的文章'),
        );

        ob_start();
        $archive->archiveTitle($titleMappings, '', '');
        $titleHtml = trim((string) ob_get_clean());

        return array(
            'titleHtml' => $titleHtml,
            'descriptionHtml' => trim((string) $archive->getDescription()),
        );
    }

    /**
     * 获取文章卡片视图数据
     *
     * @param Widget_Archive $archive
     * @param string $context
     *
     * @return array
     */
    public static function getPostCardViewData($archive, $context = 'index')
    {
        $isArchiveContext = $context === 'archive';
        $thumbnailUrl = self::getPostCardThumbnailUrl($archive);
        $loadingImageUrl = ThemeOptions::isLazyloadPlaceholderEnabled()
            ? ThemeAssetHelper::getThemeAssetUrl('assets/img/loading.svg')
            : '';
        $useLazyload = !$isArchiveContext && ThemeOptions::isLazyloadEnabled();

        return array(
            'thumbnailUrl' => $thumbnailUrl,
            'loadingImageUrl' => $loadingImageUrl,
            'thumbnailHtml' => self::getPostCardThumbnailHtml($archive, $thumbnailUrl, $useLazyload, $loadingImageUrl),
            'bodyHtml' => self::getPostCardBodyHtml($archive),
            'viewCount' => Contents::getPostViewCount($archive),
            'categorySeparator' => $isArchiveContext ? ' ' : ' • ',
            'useLazyload' => $useLazyload,
            'showLine' => !$isArchiveContext,
            'moreTitle' => $isArchiveContext ? '' : 'Read More',
        );
    }

    /**
     * 获取文章卡片缩略图 HTML
     *
     * @param Widget_Archive $archive
     * @param string $thumbnailUrl
     * @param bool $useLazyload
     * @param string $loadingImageUrl
     *
     * @return string
     */
    private static function getPostCardThumbnailHtml($archive, $thumbnailUrl, $useLazyload, $loadingImageUrl)
    {
        $permalink = self::getArchivePermalink($archive);
        $thumbnailUrl = self::escapeAttr($thumbnailUrl);
        $permalink = self::escapeAttr($permalink);

        if ($useLazyload) {
            $style = '';
            if ($loadingImageUrl !== '') {
                $style = ' style="background:url('
                    . self::escapeAttr($loadingImageUrl)
                    . ') center center no-repeat;background-size: 100% auto;"';
            }

            return '<a href="' . $permalink . '"><div class="card-thumbnail" data-aria-lazy-background="'
                . $thumbnailUrl
                . '"' . $style . '></div></a>';
        }

        return '<a class="card-thumbnail" href="'
            . $permalink
            . '" style="background:url('
            . $thumbnailUrl
            . ') center center no-repeat;background-size: 100% auto;"></a>';
    }

    /**
     * 获取文章卡片正文摘要 HTML
     *
     * @param Widget_Archive $archive
     *
     * @return string
     */
    private static function getPostCardBodyHtml($archive)
    {
        ob_start();
        if (!empty($archive->fields->previewContent)) {
            $archive->fields->previewContent();
        } else {
            $archive->excerpt(50, '...');
        }

        return trim((string) ob_get_clean());
    }

    /**
     * 获取评论表单 class
     *
     * @return string
     */
    private static function getCommentFormClassName()
    {
        return ThemeOptions::isOptionEnabled('customCommentBoxBackgroundEnabled', false)
            && ThemeAssetHelper::getCustomCommentBoxBackgroundUrl() !== ''
            ? 'comment-form--custom-background'
            : '';
    }

    /**
     * 获取文章永久链接字符串
     *
     * @param Widget_Archive $archive
     *
     * @return string
     */
    private static function getArchivePermalink($archive)
    {
        ob_start();
        $archive->permalink();
        return trim((string) ob_get_clean());
    }

    /**
     * 获取归一化后的页脚链接配置
     *
     * @return array
     */
    public static function getFooterLinkItems()
    {
        return array_merge(self::getFooterBaseLinkItems(), ThemeOptions::getFooterWidgetItems());
    }

    /**
     * 获取页脚链接 HTML
     *
     * @return string
     */
    public static function getFooterWidgetHtml()
    {
        $html = '';
        foreach (self::getFooterLinkItems() as $item) {
            $html .= self::renderFooterLinkItem($item);
        }

        return $html;
    }

    /**
     * 获取页脚备案信息 HTML
     *
     * @return string
     */
    public static function getFooterRecordsHtml()
    {
        $records = ThemeOptions::getFooterRecords();
        if (empty($records)) {
            return '';
        }

        $html = '';
        $separator = '';
        foreach ($records as $record) {
            if (!is_array($record)) {
                continue;
            }

            $text = array_key_exists('text', $record) ? trim((string) $record['text']) : '';
            if ($text === '') {
                continue;
            }

            $url = array_key_exists('url', $record) ? trim((string) $record['url']) : '';
            $icon = array_key_exists('icon', $record) ? trim((string) $record['icon']) : '';
            $title = array_key_exists('title', $record) ? trim((string) $record['title']) : $text;
            $content = '';

            if ($icon !== '') {
                $content .= self::renderFooterRecordIcon($icon);
            }
            $content .= self::escapeHtml($text);

            if ($url !== '') {
                $html .= $separator . '<a href="' . self::escapeAttr($url) . '" title="' . self::escapeAttr($title) . '" target="_blank">' . $content . '</a>';
            } else {
                $html .= $separator . '<span title="' . self::escapeAttr($title) . '">' . $content . '</span>';
            }

            $separator = '<span> | </span>';
        }

        return $html;
    }

    /**
     * 渲染页脚备案图标
     *
     * @param string $icon
     *
     * @return string
     */
    private static function renderFooterRecordIcon($icon)
    {
        $icon = trim((string) $icon);
        if ($icon === '') {
            return '';
        }

        if (preg_match('/^(https?:)?\/\//i', $icon) || strpos($icon, '/') !== false || strpos($icon, '.') !== false) {
            return '<img src="'
                . self::escapeAttr($icon)
                . '" alt="" aria-hidden="true" loading="lazy" decoding="async" fetchpriority="low" referrerpolicy="no-referrer" style="width:1em; height:auto; vertical-align:middle; margin-right:0.3em">';
        }

        return '<i class="'
            . self::escapeAttr($icon)
            . '" aria-hidden="true" style="margin-right:0.3em"></i>';
    }

    /**
     * 获取页脚版权年份文本
     *
     * @return string
     */
    public static function getCopyrightYears()
    {
        $options = Helper::options();
        $value = trim((string) $options->cpr);

        if ($value === '') {
            $value = '2022-{Y}';
        }

        return strtr($value, array(
            '{Y}' => date('Y'),
            '{y}' => date('y'),
            '{year}' => date('Y'),
        ));
    }

    /**
     * 获取页头背景图 URL
     *
     * @param Widget_Archive $archive
     * @param bool $is404Page
     *
     * @return string
     */
    private static function getHeaderBackgroundUrl($archive, $is404Page)
    {
        if ($archive->is('post') || $archive->is('page') || $archive->is('single')) {
            return $archive->fields->thumbnail ? $archive->fields->thumbnail : ThemeAssetHelper::getThumbnail();
        }

        if ($is404Page) {
            return ThemeAssetHelper::get404BackgroundUrl();
        }

        return ThemeAssetHelper::getBackgroundUrl();
    }

    /**
     * 获取页头 class 列表
     *
     * @param bool $isContentHeroPage
     * @param bool $is404Page
     *
     * @return array
     */
    private static function getHeaderClassNames($isContentHeroPage, $is404Page)
    {
        $classNames = array('clearfix', 'animated', 'fadeInDown');

        if ($isContentHeroPage || $is404Page) {
            $classNames[] = 'header--compact';
            $classNames[] = 'header--hide-meta';
        }

        if ($isContentHeroPage) {
            $classNames[] = 'header--compact-mobile';
        }

        return $classNames;
    }

    /**
     * 将背景图 URL 转为页头 style 变量
     *
     * @param string $backgroundUrl
     *
     * @return string
     */
    private static function getHeaderBackgroundCss($backgroundUrl)
    {
        return sprintf(
            "--aria-header-bg: url('%s');",
            str_replace(
                array('\\', "'"),
                array('\\\\', "\\'"),
                (string) $backgroundUrl
            )
        );
    }

    /**
     * 获取网页背景自定义 body class
     *
     * @return string
     */
    private static function getBodyClassName()
    {
        return ThemeOptions::isOptionEnabled('customPageBackgroundEnabled', false)
            ? 'body--custom-background'
            : '';
    }

    /**
     * 获取网页背景自定义 body style
     *
     * @return string
     */
    private static function getBodyStyle()
    {
        if (!ThemeOptions::isOptionEnabled('customPageBackgroundEnabled', false)) {
            return '';
        }

        return sprintf(
            "--aria-page-bg: url('%s');",
            str_replace(
                array('\\', "'"),
                array('\\\\', "\\'"),
                ThemeAssetHelper::getCustomPageBackgroundUrl()
            )
        );
    }

    /**
     * 获取页头样式资源列表
     *
     * @return array
     */
    private static function getHeaderStyleUrls()
    {
        $styles = array();

        if (ThemeOptions::isFancyboxEnabled()) {
            $styles[] = ThemeAssetHelper::getThemeAssetUrl('assets/css/jquery.fancybox.min.css');
        }

        return array_merge($styles, array(
            ThemeAssetHelper::getThemeAssetUrl('assets/css/animate.min.css'),
            ThemeAssetHelper::getThemeAssetUrl('assets/css/iconfont.css'),
            ThemeAssetHelper::getThemeAssetUrl('assets/css/restored/base.css'),
            ThemeAssetHelper::getThemeAssetUrl('assets/css/restored/layout.css'),
            ThemeAssetHelper::getThemeAssetUrl('assets/css/restored/post.css'),
            ThemeAssetHelper::getThemeAssetUrl('assets/css/restored/comments.css'),
            ThemeAssetHelper::getThemeAssetUrl('assets/css/restored/extras.css'),
            ThemeAssetHelper::getThemeAssetUrl('assets/css/pages.css'),
        ), self::getOptionalIconPackStyleUrls());
    }

    /**
     * 获取按需启用的附加图标包样式
     *
     * @return array
     */
    private static function getOptionalIconPackStyleUrls()
    {
        $enabledPacks = ThemeOptions::getOptionArrayValues('iconPacks');
        $styles = array();

        if (in_array('remixicon', $enabledPacks, true)) {
            $styles[] = ThemeAssetHelper::getThemeAssetUrl('assets/vendor/icons/remixicon/remixicon.css');
        }

        if (in_array('bootstrap-icons', $enabledPacks, true)) {
            $styles[] = ThemeAssetHelper::getThemeAssetUrl('assets/vendor/icons/bootstrap-icons/bootstrap-icons.min.css');
        }

        if (in_array('font-awesome', $enabledPacks, true)) {
            $styles[] = ThemeAssetHelper::getThemeAssetUrl('assets/vendor/icons/font-awesome/css/all.min.css');
            $styles[] = ThemeAssetHelper::getThemeAssetUrl('assets/vendor/icons/font-awesome/css/v4-font-face.min.css');
            $styles[] = ThemeAssetHelper::getThemeAssetUrl('assets/vendor/icons/font-awesome/css/v4-shims.min.css');
        }

        return $styles;
    }

    /**
     * 获取页头脚本资源列表
     *
     * @return array
     */
    private static function getHeaderScriptUrls()
    {
        return array(
            ThemeAssetHelper::getThemeAssetUrl('assets/js/jquery.min.js'),
        );
    }

    /**
     * 获取低版本 IE 兼容脚本资源列表
     *
     * @return array
     */
    private static function getHeaderLegacyScriptUrls()
    {
        return array(
            'https://cdn.staticfile.org/html5shiv/r29/html5.min.js',
            'https://cdn.staticfile.org/respond.js/1.3.0/respond.min.js',
        );
    }

    /**
     * 获取头部自定义注入内容
     *
     * @return string
     */
    private static function getCustomHeaderHtml()
    {
        return ThemeOptions::hasOption('customHeader') ? (string) Helper::options()->customHeader : '';
    }

    /**
     * 获取评论表单样式
     *
     * @return string
     */
    private static function getCommentFormStyle()
    {
        $backgroundUrl = ThemeAssetHelper::getCustomCommentBoxBackgroundUrl();
        if (!ThemeOptions::isOptionEnabled('customCommentBoxBackgroundEnabled', false) || $backgroundUrl === '') {
            return '';
        }

        return sprintf(
            "--aria-comment-box-bg: url('%s');",
            str_replace(
                array('\\', "'"),
                array('\\\\', "\\'"),
                $backgroundUrl
            )
        );
    }

    /**
     * 获取文章卡片缩略图 URL
     *
     * @param Widget_Archive $archive
     *
     * @return string
     */
    private static function getPostCardThumbnailUrl($archive)
    {
        ob_start();
        if ($archive->fields->thumbnail) {
            $archive->fields->thumbnail();
        } else {
            echo ThemeAssetHelper::getThumbnail();
        }

        return trim(ob_get_clean());
    }

    /**
     * 获取搜索框占位文本
     *
     * @return string
     */
    public static function getSearchPlaceholder()
    {
        if (!ThemeOptions::hasOption('searchPlaceholder')) {
            return '要看书架吗？请吧';
        }

        return ThemeOptions::getOptionStringValue('searchPlaceholder', '', false);
    }

    /**
     * 获取页头副标题
     *
     * @return string
     */
    public static function getHeroSubtitle()
    {
        if (!ThemeOptions::hasOption('heroSubtitle')) {
            return '越过喧嚣找到你';
        }

        $heroSubtitle = ThemeOptions::getOptionStringValue('heroSubtitle', '', false);
        if ($heroSubtitle !== '') {
            return $heroSubtitle;
        }

        return ThemeOptions::getOptionStringValue('description', '', false);
    }

    /**
     * 渲染页脚链接项
     *
     * @param array $item
     *
     * @return string
     */
    private static function renderFooterLinkItem(array $item)
    {
        $text = array_key_exists('text', $item) ? trim((string) $item['text']) : '';
        if ($text === '') {
            return '';
        }

        $href = array_key_exists('href', $item) ? trim((string) $item['href']) : '';
        $title = array_key_exists('title', $item) ? trim((string) $item['title']) : '';
        $target = array_key_exists('target', $item) ? trim((string) $item['target']) : '';
        $escapedText = self::escapeHtml($text);

        if ($href === '') {
            return '<span> • ' . $escapedText . '</span>';
        }

        $attributes = 'href="' . self::escapeAttr($href) . '"';
        if ($title !== '') {
            $attributes .= ' title="' . self::escapeAttr($title) . '"';
        }
        if ($target !== '') {
            $attributes .= ' target="' . self::escapeAttr($target) . '"';
        }

        return '<span><a ' . $attributes . '> • ' . $escapedText . '</a></span>';
    }

    /**
     * 获取页脚基础链接配置
     *
     * @return array
     */
    private static function getFooterBaseLinkItems()
    {
        $items = array(
            array(
                'text' => ThemeOptions::getOptionStringValue('footerSiteName', '网站名称'),
                'href' => ThemeOptions::getOptionStringValue('footerSiteUrl', 'https://example.com/'),
            ),
            array(
                'text' => 'Typecho',
                'href' => 'https://www.typecho.org',
                'title' => '念念不忘，必有回响。',
                'target' => '_blank',
            ),
        );

        $creditsMode = ThemeOptions::getOptionStringValue('footerCreditsMode', 'Continuo');
        if ($creditsMode === 'original') {
            $items[] = array(
                'text' => 'Aria',
                'href' => 'https://eriri.ink/archives/Typecho-Theme-Aria.html',
                'title' => 'Typecho-Theme-Aria Ver ' . ARIA_VERSION . ' by Siphils',
                'target' => '_blank',
            );
            $items[] = array(
                'text' => 'Theme by Siphils',
                'href' => 'https://eriri.ink/archives/Typecho-Theme-Aria.html',
                'title' => 'Typecho-Theme-Aria Ver ' . ARIA_VERSION . ' by Siphils',
                'target' => '_blank',
            );
        } elseif ($creditsMode === 'Continuo') {
            $items[] = array(
                'text' => 'Aria Continuo',
                'href' => 'https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo',
                'title' => 'Aria Continuo V' . ARIA_VERSION,
                'target' => '_blank',
            );
            $items[] = array(
                'text' => 'Modified by 永見涼花',
                'href' => 'https://suzuka.cc',
                'title' => '永見涼花',
                'target' => '_blank',
            );
        } elseif ($creditsMode === 'custom') {
            $creditsText = ThemeOptions::getOptionStringValue('footerCreditsText', '用户自定义内容');
            $creditsLink = ThemeOptions::getOptionStringValue('footerCreditsLink', '', false);

            $items[] = array(
                'text' => 'Aria Continuo',
                'href' => 'https://github.com/SweetenedSuzuka/Typecho-Theme-Aria-Continuo',
                'title' => 'Aria Continuo V' . ARIA_VERSION,
                'target' => '_blank',
            );
            $items[] = array(
                'text' => $creditsText,
                'href' => $creditsLink,
                'target' => $creditsLink !== '' ? '_blank' : '',
            );
        }

        return $items;
    }

    /**
     * 获取底部自定义内容
     *
     * @return string
     */
    private static function getCustomFooterHtml()
    {
        return ThemeOptions::hasOption('customFooter') ? (string) Helper::options()->customFooter : '';
    }
}
