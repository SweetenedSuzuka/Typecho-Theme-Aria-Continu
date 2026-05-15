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
        $showReward = $options->rewardConfig ? true : false;
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
        $options = Helper::options();
        //根据$item获取对应的设置中的string数据
        $data = $options->$item ? $options->$item : false;
        $content = null;
        if (!$data) {
            //不存在对应的设置名或内容为空
            $content = false;
        } else {
            if ($mode) {
                //转换为数组
                $content = json_decode("[" . $data . "]", true);
            } else {
                //转换为键值对
                $content = json_decode(trim("{" . $data . "}"), true);
            }
        }
        return $content;
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

        $data = self::convertConfigData('footerWidget', true);
        if ($data) {
            foreach ($data as $val) {
                if (!is_array($val)) {
                    continue;
                }

                $items[] = array(
                    'text' => array_key_exists('text', $val) ? $val['text'] : '',
                    'href' => array_key_exists('href', $val) ? $val['href'] : '',
                    'title' => array_key_exists('title', $val) ? $val['title'] : '',
                    'target' => array_key_exists('target', $val) ? $val['target'] : '',
                );
            }
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
        $options = Helper::options();
        if (isset($options->footerRecords) && trim((string) $options->footerRecords) === '') {
            return '';
        }

        $records = self::convertConfigData('footerRecords', true);
        if (!$records) {
            $records = array(
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
        $data = self::convertConfigData('navConfig', true);
        if (!$data) {
            return;
        }

        $text = null;
        $href = null;
        $icon = null;
        $target = null;
        $sub = null;

        if ($data) {
            $html = '';
            if ($mode) {
                foreach ($data as $v) {
                    $text = array_key_exists('text', $v) ? $v['text'] : "";
                    $href = array_key_exists('href', $v) ? 'href="' . $v['href'] . '"' : "";
                    $icon = array_key_exists('icon', $v) ? 'class="' . $v['icon'] . '"' : "";
                    $target = array_key_exists('target', $v) ? 'target="' . $v['target'] . '"' : "";
                    $slug = (array_key_exists('slug', $v) && $slugs && array_key_exists($v['slug'], $slugs)) ? $slugs[$v['slug']] : false;
                    if ($slug) {
                        $href = 'href="' . $slug['permarlink'] . '"';
                        $text = $slug['title'];
                    }
                    $html .= "<li class=\"nav-right-item\"><a $href $target><i $icon></i>$text</a>";
                    if (array_key_exists('sub', $v)) {
                        $html .= '<ul class="nav-sub">';
                        foreach ($v['sub'] as $_k => $_v) {
                            $text = array_key_exists('text', $_v) ? $_v['text'] : "";
                            $href = array_key_exists('href', $_v) ? 'href="' . $_v['href'] . '"' : "";
                            $icon = array_key_exists('icon', $_v) ? 'class="' . $_v['icon'] . '"' : "";
                            $target = array_key_exists('target', $_v) ? 'target="' . $_v['target'] . '"' : "";
                            $slug = (array_key_exists('slug', $_v) && $slugs && array_key_exists($_v['slug'], $slugs)) ? $slugs[$_v['slug']] : false;
                            if ($slug) {
                                $href = 'href="' . $slug['permarlink'] . '"';
                                $text = $slug['title'];
                            }
                            $html .= "<li class=\"sub-item\"><a $href $target><i $icon></i>$text</a></li>";
                        }
                        $html .= "</ul>";
                    }
                    $html .= "</li>";
                }
            } else {
                foreach ($data as $v) {
                    $text = array_key_exists('text', $v) ? $v['text'] : "";
                    $href = array_key_exists('href', $v) ? 'href="' . $v['href'] . '"' : "";
                    $icon = array_key_exists('icon', $v) ? 'class="' . $v['icon'] . '"' : "";
                    $target = array_key_exists('target', $v) ? 'target="' . $v['target'] . '"' : "";
                    $slug = (array_key_exists('slug', $v) && $slugs && array_key_exists($v['slug'], $slugs)) ? $slugs[$v['slug']] : false;
                    if ($slug) {
                        $href = 'href="' . $slug['permarlink'] . '"';
                        $text = $slug['title'];
                    }
                    $html .= "<li class=\"nav-vertical-item\"><a $href $target><i $icon></i>  $text</a>";
                    if (array_key_exists('sub', $v)) {
                        $html .= '<ul class="nav-vertical-sub">';
                        foreach ($v['sub'] as $_k => $_v) {
                            $text = array_key_exists('text', $_v) ? $_v['text'] : "";
                            $href = array_key_exists('href', $_v) ? 'href="' . $_v['href'] . '"' : "";
                            $icon = array_key_exists('icon', $_v) ? 'class="' . $_v['icon'] . '"' : "";
                            $target = array_key_exists('target', $_v) ? 'target="' . $_v['target'] . '"' : "";
                            $slug = (array_key_exists('slug', $_v) && $slugs && array_key_exists($_v['slug'], $slugs)) ? $slugs[$_v['slug']] : false;
                            if ($slug) {
                                $href = 'href="' . $slug['permarlink'] . '"';
                                $text = $slug['title'];
                            }
                            $html .= "<li class=\"vertical-sub-item\"><a $href $target><i $icon></i>  $text</a></li>";
                        }
                        $html .= "</ul>";
                    }
                    $html .= "</li>";
                }
            }

            echo $html;
        }
        //转换失败
        echo false;
    }
}
