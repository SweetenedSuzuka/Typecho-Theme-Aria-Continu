<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Utils.php
 * 部分工具
 *
 * @author     Siphils
 * @version    since 1.11.0
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
    public static function getAdminAvatar($size = 50)
    {
        $options = Helper::options();
        $db = Typecho_Db::get();
        $mail = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', 1))['mail'];
        $avatarUrl = $options->avatarUrl;
        $param = __TYPECHO_GRAVATAR_PREFIX__ . md5(strtolower(trim($mail))) . '?d=mp&r=g&s=' . $size;
        echo $avatarUrl ? $avatarUrl : $param;
    }

    /**
     * 获取所有页面的信息，根据slug构造键值对数组
     *
     * @return array|bool
     */
    public static function getPagesInfo()
    {
        //$widget = ypecho_Widget::widget('Widget_Abstract_Contents');
        $db = Typecho_Db::get();

        $query = $db->select()->from('table.contents')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', 'page')
            ->where('table.contents.password IS NULL');

        $_contents = $db->fetchAll($query);

        if ($_contents) {
            $contents = array();

            foreach ($_contents as $val) {
                $val = Typecho_Widget::widget('Widget_Abstract_Contents')->push($val);
                $slug = $val['slug'];
                $title = $val['title'];
                $permalink = $val['permalink'];
                $contents[$slug] = array(
                    'title' => $title,
                    'permarlink' => $permalink,
                );
            }
            return $contents;
        } else {
            //查询失败
            return false;
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
        $items = array(
            array(
                'text' => self::getOptionString('footerSiteName', '网站名称'),
                'href' => self::getOptionString('footerSiteUrl', 'https://example.com/'),
            ),
            array(
                'text' => 'Typecho',
                'href' => 'https://www.typecho.org',
                'title' => '念念不忘，必有回响。',
                'target' => '_blank',
            ),
        );

        $creditsMode = self::getOptionString('footerCreditsMode', 'Continuo');
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
            $options = Helper::options();
            $creditsText = isset($options->footerCreditsText) ? trim((string) $options->footerCreditsText) : '用户自定义内容';
            $creditsLink = isset($options->footerCreditsLink) ? trim((string) $options->footerCreditsLink) : '';
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

        foreach (self::getFooterWidgetItems() as $item) {
            $items[] = $item;
        }

        $html = '';
        foreach ($items as $item) {
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
