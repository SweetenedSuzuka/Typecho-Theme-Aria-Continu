<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Utils.php
 * 部分工具
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.13.0
 */

class Utils
{
    /**
     * 判断配置项是否存在
     *
     * @param string $name
     *
     * @return bool
     */
    public static function hasOption($name)
    {
        $options = Helper::options();
        return isset($options->$name);
    }

    /**
     * 获取主题配置项字符串值
     *
     * @param string $name
     * @param string $default
     *
     * @return string
     */
    private static function getOptionString($name, $default = '')
    {
        $options = Helper::options();
        if (!self::hasOption($name)) {
            return $default;
        }

        $value = trim((string) $options->$name);
        return $value !== '' ? $value : $default;
    }

    /**
     * 获取配置项字符串值
     *
     * @param string $name
     * @param string $default
     * @param bool $useDefaultWhenEmpty
     *
     * @return string
     */
    public static function getOptionStringValue($name, $default = '', $useDefaultWhenEmpty = true)
    {
        if (!self::hasOption($name)) {
            return $default;
        }

        $value = trim((string) Helper::options()->$name);
        if ($value !== '') {
            return $value;
        }

        return $useDefaultWhenEmpty ? $default : '';
    }

    /**
     * 获取 1/0 形式配置项的启用状态
     *
     * @param string $name
     * @param bool $default
     *
     * @return bool
     */
    public static function isOptionEnabled($name, $default = false)
    {
        if (!self::hasOption($name)) {
            return $default;
        }

        $value = Helper::options()->$name;

        if (is_array($value)) {
            foreach ($value as $item) {
                $item = strtolower(trim((string) $item));
                if (in_array($item, array('1', 'true', 'on', 'yes'), true)) {
                    return true;
                }
            }

            return false;
        }

        $value = strtolower(trim((string) $value));
        return in_array($value, array('1', 'true', 'on', 'yes'), true);
    }

    /**
     * 获取独立布尔配置项的启用状态，缺失时回退到旧的 Checkbox 配置组
     *
     * @param string $name
     * @param string $legacyConfig
     * @param bool $default
     *
     * @return bool
     */
    public static function isFeatureEnabled($name, $legacyConfig = 'AriaConfig', $default = false)
    {
        if (self::hasOption($name)) {
            return self::isOptionEnabled($name, $default);
        }

        if (self::hasOption($legacyConfig)) {
            return self::isEnabled($name, $legacyConfig);
        }

        return $default;
    }

    /**
     * 将文本配置拆分为字符串列表
     *
     * @param string $value
     *
     * @return array
     */
    public static function splitOptionList($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return array();
        }

        $parts = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($parts)) {
            return array();
        }

        return array_values(array_unique($parts));
    }

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
     * 导航文本转义，并仅允许显式换行标记生效
     *
     * @param string $value
     *
     * @return string
     */
    private static function renderNavText($value)
    {
        $text = self::escapeHtml($value);
        return str_replace(array('[[br]]', "\r\n", "\n", "\r"), '<br>', $text);
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
     * 获取带版本号的主题静态资源 URL
     *
     * @param string $relativePath
     *
     * @return string
     */
    public static function getThemeAssetUrl($relativePath)
    {
        $relativePath = ltrim((string) $relativePath, '/');
        $themeUrl = rtrim((string) Helper::options()->themeUrl, '/');
        $themeDir = dirname(__DIR__);
        $assetPath = $themeDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $version = file_exists($assetPath) ? (string) filemtime($assetPath) : ARIA_VERSION;

        return $themeUrl . '/' . $relativePath . '?v=' . rawurlencode($version);
    }

    /**
     * 获取不带版本号的主题资源 URL
     *
     * @param string $relativePath
     *
     * @return string
     */
    public static function getThemeStaticUrl($relativePath)
    {
        $relativePath = ltrim((string) $relativePath, '/');
        $themeUrl = rtrim((string) Helper::options()->themeUrl, '/');

        return $themeUrl . '/' . $relativePath;
    }

    /**
     * 判断是否为绝对 URL
     *
     * @param string $value
     *
     * @return bool
     */
    private static function isAbsoluteUrl($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return false;
        }

        return preg_match('#^(?:[a-z][a-z0-9+\-.]*:)?//#i', $value) === 1
            || preg_match('#^[a-z][a-z0-9+\-.]*:#i', $value) === 1;
    }

    /**
     * 将主题目录相对路径或绝对路径解析为可用 URL
     *
     * @param string $value
     * @param string $defaultRelativePath
     *
     * @return string
     */
    private static function resolveThemeRelativeOrAbsoluteUrl($value, $defaultRelativePath = '')
    {
        $value = trim((string) $value);
        if ($value === '') {
            return $defaultRelativePath !== '' ? self::getThemeStaticUrl($defaultRelativePath) : '';
        }

        if (self::isAbsoluteUrl($value)) {
            return $value;
        }

        return self::getThemeStaticUrl($value);
    }

    /**
     * 获取默认 MathJax 配置脚本
     *
     * @return string
     */
    private static function getDefaultMathJaxConfigScript()
    {
        return "MathJax = MathJax || {};\n"
            . "MathJax.tex = MathJax.tex || {};\n"
            . "MathJax.tex.inlineMath = [['$', '$'], ['\\\\(', '\\\\)']];\n"
            . "MathJax.tex.displayMath = [['$$', '$$'], ['\\\\[', '\\\\]']];\n"
            . "MathJax.tex.processEscapes = true;";
    }

    /**
     * 获取归一化后的 MathJax 配置脚本
     *
     * @return string
     */
    public static function getMathJaxConfigScript()
    {
        return self::getOptionStringValue(
            'MathJaxConfig',
            self::getDefaultMathJaxConfigScript()
        );
    }

    /**
     * 获取 MathJax 兼容层脚本
     *
     * @return string
     */
    private static function getMathJaxCompatScript()
    {
        return <<<'JS'
window.ariaEnsureMathJaxCompat = window.ariaEnsureMathJaxCompat || function () {
    window.MathJax = window.MathJax || {};
    window.MathJax.tex = window.MathJax.tex || {};
    window.MathJax.options = window.MathJax.options || {};
    window.MathJax.Hub = window.MathJax.Hub || {};
    window.MathJax.Hub.Config = window.MathJax.Hub.Config || function (config) {
        if (!config || !config.tex2jax) {
            return;
        }
        var tex2jax = config.tex2jax || {};
        if (tex2jax.inlineMath) {
            window.MathJax.tex.inlineMath = tex2jax.inlineMath;
        }
        if (tex2jax.displayMath) {
            window.MathJax.tex.displayMath = tex2jax.displayMath;
        }
        if (typeof tex2jax.processEscapes !== 'undefined') {
            window.MathJax.tex.processEscapes = tex2jax.processEscapes;
        }
    };
    window.MathJax.Hub.Queue = window.MathJax.Hub.Queue || function () {
        if (!window.MathJax) {
            return;
        }
        if (typeof window.MathJax.typesetPromise === 'function') {
            var container = document.getElementById('pjax-container');
            return window.MathJax.typesetPromise(container ? [container] : undefined);
        }
    };
};
window.ariaTypesetMathJax = window.ariaTypesetMathJax || function (targets) {
    if (!window.MathJax) {
        return;
    }

    if (typeof window.MathJax.typesetPromise === 'function') {
        if (!targets) {
            return window.MathJax.typesetPromise();
        }

        if (!Array.isArray(targets)) {
            targets = [targets];
        }

        targets = targets.filter(function (target) {
            return target && target.nodeType === 1;
        });

        return window.MathJax.typesetPromise(targets.length ? targets : undefined);
    }

    if (window.MathJax.Hub && typeof window.MathJax.Hub.Queue === 'function') {
        return window.MathJax.Hub.Queue(['Typeset', window.MathJax.Hub]);
    }
};
window.ariaEnsureMathJaxCompat();
(function () {
    var cls = 'aria-mathjax-ignore';
    var opt = window.MathJax && window.MathJax.options;
    opt = opt || {};
    window.MathJax.options = opt;
    var cur = opt.ignoreHtmlClass;
    if (typeof cur !== 'string' || cur.trim() === '') {
        opt.ignoreHtmlClass = 'tex2jax_ignore|' + cls;
        return;
    }
    if (!new RegExp('(^|\\\\|)' + cls + '($|\\\\|)').test(cur)) {
        opt.ignoreHtmlClass = cur + '|' + cls;
    }
})();
JS;
    }

    /**
     * 获取 MathJax 兼容层补执行脚本
     *
     * @return string
     */
    private static function getMathJaxEnsureScript()
    {
        return 'window.ariaEnsureMathJaxCompat&&window.ariaEnsureMathJaxCompat();';
    }

    /**
     * 获取评论区 MathJax 补排版脚本
     *
     * @return string
     */
    private static function getMathJaxCommentObserverScript()
    {
        return <<<'JS'
(function () {
    var observer = null;
    var queuedTargets = [];
    var flushTimer = null;

    function hasIgnoreClass(node) {
        return !!(node && node.closest && node.closest('.aria-mathjax-ignore'));
    }

    function queueTarget(node) {
        if (!node || node.nodeType !== 1 || hasIgnoreClass(node)) {
            return;
        }

        if (node.tagName && node.tagName.toLowerCase() === 'mjx-container') {
            return;
        }

        queuedTargets.push(node);
        if (flushTimer !== null) {
            return;
        }

        flushTimer = window.setTimeout(flushTargets, 60);
    }

    function flushTargets() {
        var commentsRoot = document.getElementById('comments');
        var targets = queuedTargets.slice();

        flushTimer = null;
        queuedTargets = [];

        if (!commentsRoot || !targets.length || hasIgnoreClass(commentsRoot)) {
            return;
        }

        if (observer) {
            observer.disconnect();
        }

        Promise.resolve(window.ariaTypesetMathJax && window.ariaTypesetMathJax(targets))
            .then(function () {
                if (observer) {
                    observer.observe(commentsRoot, { childList: true, subtree: true });
                }
            }, function () {
                if (observer) {
                    observer.observe(commentsRoot, { childList: true, subtree: true });
                }
            });
    }

    function installObserver() {
        var commentsRoot = document.getElementById('comments');
        if (!commentsRoot || hasIgnoreClass(commentsRoot) || typeof MutationObserver === 'undefined') {
            return;
        }

        if (observer) {
            observer.disconnect();
        }

        observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                Array.prototype.forEach.call(mutation.addedNodes, function (node) {
                    queueTarget(node);
                });
            });
        });

        observer.observe(commentsRoot, { childList: true, subtree: true });
    }

    installObserver();

    if (window.jQuery) {
        window.jQuery(document).on('pjax:complete', function () {
            window.setTimeout(installObserver, 0);
        });
    }
})();
JS;
    }

    /**
     * 获取 MathJax 视图数据
     *
     * @return array
     */
    public static function getMathJaxViewData()
    {
        $enabled = self::isFeatureEnabled('enableMathJax', 'AriaConfig');

        return array(
            'enabled' => $enabled,
            'enabledInComments' => $enabled && self::isFeatureEnabled('enableMathJaxInComments', 'AriaConfig'),
            'configScript' => self::getMathJaxConfigScript(),
            'compatScript' => self::getMathJaxCompatScript(),
            'ensureScript' => self::getMathJaxEnsureScript(),
            'commentObserverScript' => $enabled
                && self::isFeatureEnabled('enableMathJaxInComments', 'AriaConfig')
                && self::isEnabled('enableAjaxComment', 'AriaConfig')
                ? self::getMathJaxCommentObserverScript()
                : '',
            'cdnUrl' => 'https://cdn.jsdelivr.net/npm/mathjax@4.1.2/tex-mml-chtml.js',
        );
    }

    /**
     * 获取搜索框占位文本
     *
     * @return string
     */
    public static function getSearchPlaceholder()
    {
        if (!self::hasOption('searchPlaceholder')) {
            return '要想搜索请输入关键词';
        }

        return self::getOptionStringValue('searchPlaceholder', '', false);
    }

    /**
     * 获取页头副标题
     *
     * @return string
     */
    public static function getHeroSubtitle()
    {
        if (!self::hasOption('heroSubtitle')) {
            return '越过喧嚣找到你';
        }

        $heroSubtitle = self::getOptionStringValue('heroSubtitle', '', false);
        if ($heroSubtitle !== '') {
            return $heroSubtitle;
        }

        return self::getOptionStringValue('description', '', false);
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
            return $archive->fields->thumbnail ? $archive->fields->thumbnail : self::getThumbnail();
        }

        if ($is404Page) {
            return self::get404BackgroundUrl();
        }

        return self::getBackgroundUrl();
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
     * 获取网页背景自定义 URL
     *
     * @return string
     */
    public static function getCustomPageBackgroundUrl()
    {
        $customPath = self::getOptionStringValue(
            'customPageBackgroundUrl',
            '/assets/img/background.webp'
        );

        return self::resolveThemeRelativeOrAbsoluteUrl($customPath, 'assets/img/background.webp');
    }

    /**
     * 获取网页背景自定义 body class
     *
     * @return string
     */
    private static function getBodyClassName()
    {
        return self::isOptionEnabled('customPageBackgroundEnabled', false)
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
        if (!self::isOptionEnabled('customPageBackgroundEnabled', false)) {
            return '';
        }

        return sprintf(
            "--aria-page-bg: url('%s');",
            str_replace(
                array('\\', "'"),
                array('\\\\', "\\'"),
                self::getCustomPageBackgroundUrl()
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
        // 添加Font Awesome图标
        $styles = array(
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        );

        if (self::isEnabled('enableFancybox', 'AriaConfig')) {
            $styles[] = self::getThemeAssetUrl('assets/css/jquery.fancybox.min.css');
        }

        return array_merge($styles, array(
            self::getThemeAssetUrl('assets/OwO/OwO.min.css'),
            self::getThemeAssetUrl('assets/css/animate.min.css'),
            self::getThemeAssetUrl('assets/css/iconfont.css'),
            self::getThemeAssetUrl('assets/css/restored/base.css'),
            self::getThemeAssetUrl('assets/css/restored/layout.css'),
            self::getThemeAssetUrl('assets/css/restored/post.css'),
            self::getThemeAssetUrl('assets/css/restored/comments.css'),
            self::getThemeAssetUrl('assets/css/restored/extras.css'),
            self::getThemeAssetUrl('assets/css/pages.css'),
        ));
    }

    /**
     * 获取页头脚本资源列表
     *
     * @return array
     */
    private static function getHeaderScriptUrls()
    {
        return array(
            self::getThemeAssetUrl('assets/js/jquery.min.js'),
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
        return self::hasOption('customHeader') ? (string) Helper::options()->customHeader : '';
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
        $isContentHeroPage = $archive->is('post') || $archive->is('page') || $archive->is('single') || $archive->is('archive');
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
                'slugs' => self::getPagesInfo(),
                'adminAvatarUrl' => self::getAdminAvatarUrl(50),
                'adminAvatarLargeUrl' => self::getAdminAvatarUrl(150),
                'siteUrl' => rtrim((string) Helper::options()->siteUrl, '/') . '/',
                'siteTitle' => trim((string) Helper::options()->title),
            ),
            'search' => array(
                'placeholder' => self::getSearchPlaceholder(),
                'buttonBackgroundUrl' => self::getThemeAssetUrl('assets/img/search.png'),
            ),
            'hero' => array(
                'className' => implode(' ', self::getHeaderClassNames($isContentHeroPage, $is404Page)),
                'backgroundCss' => self::getHeaderBackgroundCss($headerBackgroundUrl),
                'siteTitle' => trim((string) Helper::options()->title),
                'subtitle' => self::getHeroSubtitle(),
            ),
        );
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
                'text' => self::getOptionStringValue('footerSiteName', '网站名称'),
                'href' => self::getOptionStringValue('footerSiteUrl', 'https://example.com/'),
            ),
            array(
                'text' => 'Typecho',
                'href' => 'https://www.typecho.org',
                'title' => '念念不忘，必有回响。',
                'target' => '_blank',
            ),
        );

        $creditsMode = self::getOptionStringValue('footerCreditsMode', 'Continuo');
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
            $creditsText = self::getOptionStringValue('footerCreditsText', '用户自定义内容');
            $creditsLink = self::getOptionStringValue('footerCreditsLink', '', false);

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
     * 获取归一化后的页脚链接配置
     *
     * @return array
     */
    public static function getFooterLinkItems()
    {
        return array_merge(self::getFooterBaseLinkItems(), self::getFooterWidgetItems());
    }

    /**
     * 获取页脚脚本资源列表
     *
     * @return array
     */
    private static function getFooterScriptUrls()
    {
        $scripts = array();

        if (self::isEnabled('enablePjax', 'AriaConfig')) {
            $scripts[] = self::getThemeAssetUrl('assets/js/jquery.pjax.min.js');
        }

        if (self::isEnabled('enableFancybox', 'AriaConfig')) {
            $scripts[] = self::getThemeAssetUrl('assets/js/jquery.fancybox.min.js');
        }

        $scripts[] = self::getThemeAssetUrl('assets/js/highlight.min.js');

        if (self::isEnabled('enableLazyload', 'AriaConfig')) {
            $scripts[] = self::getThemeAssetUrl('assets/js/jquery.lazyload.min.js');
        }

        $scripts[] = self::getThemeAssetUrl('assets/OwO/OwO.min.js');

        return array_merge($scripts, array(
            self::getThemeAssetUrl('assets/js/functions.min.js'),
            self::getThemeAssetUrl('assets/js/modules/base.js'),
            self::getThemeAssetUrl('assets/js/modules/core.js'),
            self::getThemeAssetUrl('assets/js/modules/comment.js'),
            self::getThemeAssetUrl('assets/js/modules/action.js'),
            self::getThemeAssetUrl('assets/js/modules/toc.js'),
            self::getThemeAssetUrl('assets/js/modules/jquery-resize.js'),
        ));
    }

    /**
     * 获取主题主脚本地址
     *
     * @return string
     */
    private static function getMainScriptUrl()
    {
        return self::getThemeAssetUrl('assets/js/main.js');
    }

    /**
     * 获取底部自定义内容
     *
     * @return string
     */
    private static function getCustomFooterHtml()
    {
        return self::hasOption('customFooter') ? (string) Helper::options()->customFooter : '';
    }

    /**
     * 获取自定义脚本 HTML
     *
     * @return string
     */
    private static function getCustomScriptHtml()
    {
        $customScript = self::hasOption('customScript') ? trim((string) Helper::options()->customScript) : '';
        if ($customScript === '') {
            return '';
        }

        return "<script>{$customScript}</script>\n";
    }

    /**
     * 获取统计代码 HTML
     *
     * @return string
     */
    private static function getStatisticsHtml()
    {
        return self::hasOption('statistics') ? (string) Helper::options()->statistics : '';
    }

    /**
     * 获取页脚视图数据
     *
     * @return array
     */
    public static function getFooterViewData()
    {
        $recordsEnabled = self::isOptionEnabled('footerRecordsEnabled', true);

        return array(
            'showHitokoto' => self::isFeatureEnabled('showHitokoto', 'AriaConfig'),
            'widgetHtml' => self::getFooterWidgetHtml(),
            'recordsHtml' => $recordsEnabled ? self::getFooterRecordsHtml() : '',
            'customFooterHtml' => self::getCustomFooterHtml(),
            'goTopImageUrl' => self::getThemeAssetUrl('assets/img/goTop.png'),
            'scripts' => self::getFooterScriptUrls(),
            'customScriptHtml' => self::getCustomScriptHtml(),
            'mainScriptUrl' => self::getMainScriptUrl(),
            'statisticsHtml' => self::getStatisticsHtml(),
        );
    }

    /**
     * 获取评论展示视图数据
     *
     * @return array
     */
    public static function getCommentsViewData()
    {
        $mathJaxEnabled = self::isFeatureEnabled('enableMathJax', 'AriaConfig');
        $mathJaxEnabledInComments = self::isFeatureEnabled('enableMathJaxInComments', 'AriaConfig');
        $options = Helper::options();
        $commentsRequireMail = !empty($options->commentsRequireMail);
        $commentsRequireUrl = !empty($options->commentsRequireURL);
        $commentsMarkdown = !empty($options->commentsMarkdown);
        $allowedHtmlTags = isset($options->commentsHTMLTagAllowed)
            ? (string) $options->commentsHTMLTagAllowed
            : '';

        return array(
            'ignoreMathJax' => $mathJaxEnabled && !$mathJaxEnabledInComments,
            'waitingText' => self::getOptionStringValue(
                'commentWaitingText',
                '正在思考这条评论和不和谐.jpg（评论正在等待审核）',
                false
            ),
            'closedText' => self::getOptionStringValue('commentClosedText', '评论关闭了哟', false),
            'showUserAgent' => self::isEnabled('showCommentUA', 'AriaConfig'),
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
                'showCommentToMail' => self::isEnabled('enableCommentToMail', 'AriaConfig'),
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

        return array(
            'meta' => array(
                'showCategory' => $isPostContext,
                'categorySeparator' => ' • ',
                'viewsSuffix' => '次阅读',
            ),
            'showTags' => $isPostContext,
            'showNextPrev' => $isPostContext,
            'showToc' => !empty($archive->fields->showTOC),
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
            echo self::getThumbnail();
        }

        return trim(ob_get_clean());
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

        return array(
            'thumbnailUrl' => self::getPostCardThumbnailUrl($archive),
            'loadingImageUrl' => self::getThemeAssetUrl('assets/img/loading.svg'),
            'categorySeparator' => $isArchiveContext ? ' ' : ' • ',
            'useLazyload' => !$isArchiveContext && self::isEnabled('enableLazyload', 'AriaConfig'),
            'showLine' => !$isArchiveContext,
            'moreTitle' => $isArchiveContext ? '' : 'Read More',
        );
    }

    /**
     * 获取评论框背景图 URL
     *
     * @return string
     */
    public static function getCustomCommentBoxBackgroundUrl()
    {
        $customPath = self::getOptionStringValue('customCommentBoxBackgroundUrl', '', false);
        if ($customPath === '') {
            return '';
        }

        return self::resolveThemeRelativeOrAbsoluteUrl($customPath);
    }

    /**
     * 获取评论表单 class
     *
     * @return string
     */
    private static function getCommentFormClassName()
    {
        return self::isOptionEnabled('customCommentBoxBackgroundEnabled', false)
            && self::getCustomCommentBoxBackgroundUrl() !== ''
            ? 'comment-form--custom-background'
            : '';
    }

    /**
     * 获取评论表单样式
     *
     * @return string
     */
    private static function getCommentFormStyle()
    {
        $backgroundUrl = self::getCustomCommentBoxBackgroundUrl();
        if (!self::isOptionEnabled('customCommentBoxBackgroundEnabled', false) || $backgroundUrl === '') {
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
     * 输出博客以及主题部分配置信息为前端提供接口
     *
     * @return void
     */
    public static function AriaConfig()
    {
        $AriaConfig = Helper::options()->AriaConfig;
        $options = Helper::options();

        $showHitokoto = self::isFeatureEnabled('showHitokoto', 'AriaConfig');
        $showQRCode = self::isEnabled('showQRCode', 'AriaConfig');
        $showReward = count(self::getRewardConfigMap()) > 0;
        $enablePjax = self::isEnabled('enablePjax', 'AriaConfig');
        $enableAjaxComment = self::isEnabled('enableAjaxComment', 'AriaConfig');
        $enableFancybox = self::isEnabled('enableFancybox', 'AriaConfig');
        $enableLazyload = self::isEnabled('enableLazyload', 'AriaConfig');
        $enableMathJax = self::isFeatureEnabled('enableMathJax', 'AriaConfig');
        $OwOJson = $options->OwOJson ? $options->OwOJson : $options->themeUrl . "/assets/OwO/OwO.json";
        $hitokotoOrigin = $options->hitokotoOrigin ? $options->hitokotoOrigin : 'https://v1.hitokoto.cn/?c=a&encode=text';
        $gravatarPrefix = __TYPECHO_GRAVATAR_PREFIX__;

        $THEME_CONFIG = json_encode((object) array(
            "THEME_VERSION" => ARIA_VERSION,
            "SITE_URL" => rtrim($options->siteUrl, "/"),
            "THEME_URL" => $options->themeUrl,
            "SHOW_HITOKOTO" => $showHitokoto,
            "SHOW_QRCODE" => $showQRCode,
            "SHOW_REWARD" => $showReward,
            "ENABLE_PJAX" => $enablePjax,
            "ENABLE_AJAX_COMMENT" => $enableAjaxComment,
            "ENABLE_FANCYBOX" => $enableFancybox,
            "ENABLE_LAZYLOAD" => $enableLazyload,
            "ENABLE_MATHJAX" => $enableMathJax,
            "OWO_JSON" => $OwOJson,
            "HITOKOTO_ORIGIN" => $hitokotoOrigin,
            "GRAVATAR_PREFIX" => $gravatarPrefix,
        ));

        echo "<script>window.THEME_CONFIG = $THEME_CONFIG</script>\n";
    }

    /**
     * 获取第一管理员的头像
     *
     * @param int $size 尺寸
     * @return void
     */
    public static function getAdminAvatarUrl($size = 50)
    {
        static $adminMail = null;

        $options = Helper::options();
        $avatarUrl = trim((string) $options->avatarUrl);
        if ($avatarUrl !== '') {
            return $avatarUrl;
        }

        if ($adminMail === null) {
            $db = Typecho_Db::get();
            $admin = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', 1));
            $adminMail = is_array($admin) && array_key_exists('mail', $admin)
                ? trim((string) $admin['mail'])
                : '';
        }

        return __TYPECHO_GRAVATAR_PREFIX__
            . md5(strtolower($adminMail))
            . '?d=mp&r=g&s='
            . (int) $size;
    }

    /**
     * 获取第一管理员的头像
     *
     * @param int $size 尺寸
     * @return void
     */
    public static function getAdminAvatar($size = 50)
    {
        echo self::getAdminAvatarUrl($size);
    }

    /**
     * 获取所有页面的信息，根据slug构造键值对数组
     *
     * @return array|bool
     */
    public static function getPagesInfo()
    {
        static $pagesInfo = null;

        if ($pagesInfo !== null) {
            return $pagesInfo;
        }

        //$widget = ypecho_Widget::widget('Widget_Abstract_Contents');
        $db = Typecho_Db::get();
        static $contentsWidget = null;

        $query = $db->select()->from('table.contents')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', 'page')
            ->where('table.contents.password IS NULL');

        $_contents = $db->fetchAll($query);

        if ($_contents) {
            $contents = array();
            if ($contentsWidget === null) {
                $contentsWidget = Typecho_Widget::widget('Widget_Abstract_Contents');
            }

            foreach ($_contents as $val) {
                $val = $contentsWidget->push($val);
                $slug = $val['slug'];
                $title = $val['title'];
                $permalink = $val['permalink'];
                $contents[$slug] = array(
                    'title' => $title,
                    'permarlink' => $permalink,
                );
            }
            $pagesInfo = $contents;
            return $pagesInfo;
        } else {
            //查询失败
            $pagesInfo = false;
            return $pagesInfo;
        }
    }

    /**
     * 返回主题设置中某项开关的开启/关闭状态
     *
     * @param string $item 项目名
     * @param string $config 设置名
     *
     * @return bool
     */
    public static function isEnabled($item, $config)
    {
        $config = Helper::options()->$config;
        $status = !empty($config) && in_array($item, $config) ? true : false;
        return $status;
    }

    /**
     * 将部分主题配置中的string数据转换为array或键值对
     *
     * @param string $item 设置名
     * @param bool $mode 转换类型
     *
     * @return array|bool
     */
    public static function convertConfigData($item, $mode)
    {
        if (!self::hasOption($item)) {
            return false;
        }

        return self::decodeConfigFragment((string) Helper::options()->$item, $mode);
    }

    /**
     * 解析旧式 JSON 片段配置
     *
     * @param string $raw
     * @param bool $mode true 为列表，false 为对象
     *
     * @return array|bool
     */
    private static function decodeConfigFragment($raw, $mode)
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return false;
        }

        $json = $mode ? '[' . $raw . ']' : '{' . $raw . '}';
        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : false;
    }

    /**
     * 归一化链接类配置项
     *
     * @param array $item
     * @param bool $allowSlug
     *
     * @return array|null
     */
    private static function normalizeLinkConfigItem(array $item, $allowSlug = false)
    {
        $text = array_key_exists('text', $item) ? trim((string) $item['text']) : '';
        $href = array_key_exists('href', $item) ? trim((string) $item['href']) : '';
        $title = array_key_exists('title', $item) ? trim((string) $item['title']) : '';
        $target = array_key_exists('target', $item) ? trim((string) $item['target']) : '';
        $icon = array_key_exists('icon', $item) ? trim((string) $item['icon']) : '';
        $slug = $allowSlug && array_key_exists('slug', $item) ? trim((string) $item['slug']) : '';

        if ($text === '' && $slug === '') {
            return null;
        }

        return array(
            'text' => $text,
            'href' => $href,
            'title' => $title,
            'target' => $target,
            'icon' => $icon,
            'slug' => $slug,
        );
    }

    /**
     * 归一化导航配置项
     *
     * @param array $item
     *
     * @return array|null
     */
    private static function normalizeNavConfigItem(array $item)
    {
        $normalized = self::normalizeLinkConfigItem($item, true);
        if ($normalized === null) {
            return null;
        }

        $normalized['sub'] = array();
        if (array_key_exists('sub', $item) && is_array($item['sub'])) {
            foreach ($item['sub'] as $subItem) {
                if (!is_array($subItem)) {
                    continue;
                }

                $normalizedSubItem = self::normalizeNavConfigItem($subItem);
                if ($normalizedSubItem !== null) {
                    $normalized['sub'][] = $normalizedSubItem;
                }
            }
        }

        return $normalized;
    }

    /**
     * 默认导航配置
     *
     * @return array
     */
    private static function getDefaultNavConfigItems()
    {
        return array(
            array(
                'text' => '首页',
                'href' => Helper::options()->siteUrl,
                'icon' => 'iconfont icon-aria-home',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
            array(
                'text' => '归档',
                'href' => '#',
                'icon' => 'iconfont icon-aria-archives',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
            array(
                'text' => '留言',
                'href' => '#',
                'icon' => 'iconfont icon-aria-guestbook',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
            array(
                'text' => '朋友',
                'href' => '#',
                'icon' => 'iconfont icon-aria-friends',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
            array(
                'text' => '关于',
                'href' => '#',
                'icon' => 'iconfont icon-aria-about',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
        );
    }

    /**
     * 获取归一化后的导航配置
     *
     * @return array
     */
    public static function getNavConfigItems()
    {
        $data = self::convertConfigData('navConfig', true);
        if (!$data) {
            return self::getDefaultNavConfigItems();
        }

        $items = array();
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $normalized = self::normalizeNavConfigItem($item);
            if ($normalized !== null) {
                $items[] = $normalized;
            }
        }

        return !empty($items) ? $items : self::getDefaultNavConfigItems();
    }

    /**
     * 获取归一化后的页脚扩展链接配置
     *
     * @return array
     */
    public static function getFooterWidgetItems()
    {
        $data = self::convertConfigData('footerWidget', true);
        if (!$data) {
            return array();
        }

        $items = array();
        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $normalized = self::normalizeLinkConfigItem($item);
            if ($normalized !== null) {
                $items[] = $normalized;
            }
        }

        return $items;
    }

    /**
     * 默认页脚备案配置
     *
     * @return array
     */
    private static function getDefaultFooterRecords()
    {
        return array(
            array(
                'text' => 'ICP备00000000号-0',
                'url' => 'https://beian.miit.gov.cn/',
                'icon' => '',
                'title' => 'ICP备案信息',
            ),
            array(
                'text' => '公网安备 00000000000000号',
                'url' => 'http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=00000000000000',
                'icon' => '',
                'title' => '公网安备信息',
            ),
        );
    }

    /**
     * 获取归一化后的页脚备案配置
     *
     * @return array
     */
    public static function getFooterRecords()
    {
        $options = Helper::options();
        if (isset($options->footerRecords) && trim((string) $options->footerRecords) === '') {
            return array();
        }

        $records = self::convertConfigData('footerRecords', true);
        if (!$records) {
            $records = self::getDefaultFooterRecords();
        }

        $items = array();
        foreach ($records as $record) {
            if (!is_array($record)) {
                continue;
            }

            $text = array_key_exists('text', $record) ? trim((string) $record['text']) : '';
            if ($text === '') {
                continue;
            }

            $items[] = array(
                'text' => $text,
                'url' => array_key_exists('url', $record) ? trim((string) $record['url']) : '',
                'icon' => array_key_exists('icon', $record) ? trim((string) $record['icon']) : '',
                'title' => array_key_exists('title', $record) ? trim((string) $record['title']) : $text,
            );
        }

        return $items;
    }

    /**
     * 获取归一化后的打赏配置
     *
     * @return array
     */
    public static function getRewardConfigMap()
    {
        $data = self::convertConfigData('rewardConfig', false);
        if (!$data) {
            return array();
        }

        $items = array();
        foreach ($data as $label => $url) {
            $label = trim((string) $label);
            $url = trim((string) $url);

            if ($label === '' || $url === '') {
                continue;
            }

            $items[$label] = $url;
        }

        return $items;
    }

    /**
     * 获取背景图片
     *
     * @return void
     */
    public static function getBackground()
    {
        echo self::getBackgroundUrl();
    }

    /**
     * 获取背景图片 URL
     *
     * @return string
     */
    public static function getBackgroundUrl()
    {
        $options = Helper::options();

        if ($options->backgroundUrl) {
            $str = $options->backgroundUrl;
            $imgs = trim($str);
            $urls = explode("\r\n", $imgs);
            $n = mt_rand(0, count($urls) - 1);
            return $urls[$n];
        }

        return $options->themeUrl . '/assets/img/background.jpg';
    }

    /**
     * 获取 404 背景图片 URL
     *
     * @return string
     */
    public static function get404BackgroundUrl()
    {
        $options = Helper::options();
        $customUrl = trim((string) $options->notFoundBackgroundUrl);

        if ($customUrl !== '') {
            return $customUrl;
        }

        return $options->themeUrl . '/assets/img/404.jpg';
    }

    /**
     * 获取随机默认缩略图
     */
    public static function getThumbnail()
    {
        $options = Helper::options();
        if ($options->defaultThumbnail) {
            $str = $options->defaultThumbnail;
            $imgs = trim($str);
            $urls = explode("\r\n", $imgs);
            $n = mt_rand(0, count($urls) - 1);
            return $urls[$n];
        } else {
            return $options->themeUrl . '/assets/img/thumbnail.jpg';
        }
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
     * 输出底部组件
     *
     * @return void
     */
    public static function getFooterWidget()
    {
        echo self::getFooterWidgetHtml();
    }

    /**
     * 获取页脚备案信息 HTML
     *
     * @return string
     */
    public static function getFooterRecordsHtml()
    {
        $records = self::getFooterRecords();
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
                $content .= '<img src="' . self::escapeAttr($icon) . '" alt="" aria-hidden="true" style="width:1em; height:auto; vertical-align:middle; margin-right:0.3em">';
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
     * 获取页脚版权年份文本
     *
     * 支持在配置中使用 {Y}、{y}、{year} 作为当前年份占位符。
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
     * 根据配置的JSON数据输出导航栏
     * @param int $mode
     * @param string $slugs
     * 
     * @return void
     */
    public static function showNav($mode, $slugs)
    {
        $data = self::getNavConfigItems();
        if (empty($data)) {
            return;
        }

        $itemClass = $mode ? 'nav-right-item' : 'nav-vertical-item';
        $subListClass = $mode ? 'nav-sub' : 'nav-vertical-sub';
        $subItemClass = $mode ? 'sub-item' : 'vertical-sub-item';
        $labelPrefix = $mode ? '' : '  ';
        $html = '';

        foreach ($data as $item) {
            $resolvedItem = $item;
            if ($item['slug'] !== '' && $slugs && array_key_exists($item['slug'], $slugs)) {
                $resolvedItem['href'] = $slugs[$item['slug']]['permarlink'];
                $resolvedItem['text'] = $slugs[$item['slug']]['title'];
            }

            $href = $resolvedItem['href'] !== '' ? ' href="' . self::escapeAttr($resolvedItem['href']) . '"' : '';
            $target = $resolvedItem['target'] !== '' ? ' target="' . self::escapeAttr($resolvedItem['target']) . '"' : '';
            $iconHtml = $resolvedItem['icon'] !== '' ? '<i class="' . self::escapeAttr($resolvedItem['icon']) . '"></i>' : '';
            $textHtml = self::renderNavText($resolvedItem['text']);

            $html .= '<li class="' . $itemClass . '"><a' . $href . $target . '>' . $iconHtml . $labelPrefix . $textHtml . '</a>';

            if (!empty($item['sub'])) {
                $html .= '<ul class="' . $subListClass . '">';
                foreach ($item['sub'] as $subItem) {
                    $resolvedSubItem = $subItem;
                    if ($subItem['slug'] !== '' && $slugs && array_key_exists($subItem['slug'], $slugs)) {
                        $resolvedSubItem['href'] = $slugs[$subItem['slug']]['permarlink'];
                        $resolvedSubItem['text'] = $slugs[$subItem['slug']]['title'];
                    }

                    $subHref = $resolvedSubItem['href'] !== '' ? ' href="' . self::escapeAttr($resolvedSubItem['href']) . '"' : '';
                    $subTarget = $resolvedSubItem['target'] !== '' ? ' target="' . self::escapeAttr($resolvedSubItem['target']) . '"' : '';
                    $subIconHtml = $resolvedSubItem['icon'] !== '' ? '<i class="' . self::escapeAttr($resolvedSubItem['icon']) . '"></i>' : '';
                    $subTextHtml = self::renderNavText($resolvedSubItem['text']);

                    $html .= '<li class="' . $subItemClass . '"><a' . $subHref . $subTarget . '>' . $subIconHtml . $labelPrefix . $subTextHtml . '</a></li>';
                }
                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        echo $html;
    }
}
