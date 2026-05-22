<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeOptions.php
 * 主题配置读取与归一化
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.17.1
 */
class ThemeOptions
{
    /**
     * 主题配置结构版本
     */
    private const THEME_CONFIG_SCHEMA_VERSION = '1';

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
     * 获取独立复选框配置项状态
     *
     * 一旦当前主题配置结构已经保存过，缺失字段就视为明确关闭，
     * 避免 Typecho 在未勾选 checkbox 时不提交字段而导致回弹到默认值。
     *
     * @param string $name
     * @param bool $default
     *
     * @return bool
     */
    public static function getCheckboxOptionState($name, $default = false)
    {
        if (self::hasOption($name)) {
            return self::isOptionEnabled($name, false);
        }

        if (self::hasThemeConfigSchemaVersion()) {
            return false;
        }

        return $default;
    }

    /**
     * 获取懒加载开关状态
     *
     * @return bool
     */
    public static function isLazyloadEnabled()
    {
        return self::getCheckboxOptionState('enableLazyload', false);
    }

    /**
     * 获取懒加载占位图开关状态
     *
     * @return bool
     */
    public static function isLazyloadPlaceholderEnabled()
    {
        return self::getCheckboxOptionState('lazyloadPlaceholderEnabled', false);
    }

    /**
     * 获取 MathJax 开关状态
     *
     * @return bool
     */
    public static function isMathJaxEnabled()
    {
        return self::getCheckboxOptionState('enableMathJax', false);
    }

    /**
     * 获取评论区 MathJax 开关状态
     *
     * @return bool
     */
    public static function isMathJaxInCommentsEnabled()
    {
        return self::getCheckboxOptionState('enableMathJaxInComments', false);
    }

    /**
     * 获取一言开关状态
     *
     * @return bool
     */
    public static function isHitokotoEnabled()
    {
        return self::getCheckboxOptionState('showHitokoto', false);
    }

    /**
     * 获取 AJAX 评论开关状态
     *
     * @return bool
     */
    public static function isAjaxCommentEnabled()
    {
        return self::getCheckboxOptionState('enableAjaxComment', false);
    }

    /**
     * 获取 Fancybox 开关状态
     *
     * @return bool
     */
    public static function isFancyboxEnabled()
    {
        return self::getCheckboxOptionState('enableFancybox', false);
    }

    /**
     * 获取评论邮件通知开关状态
     *
     * @return bool
     */
    public static function isCommentToMailEnabled()
    {
        return self::getCheckboxOptionState('enableCommentToMail', false);
    }

    /**
     * 获取文章二维码开关状态
     *
     * @return bool
     */
    public static function isPostQrCodeEnabled()
    {
        return self::getCheckboxOptionState('showQRCode', false);
    }

    /**
     * 获取评论 UserAgent 显示开关状态
     *
     * @return bool
     */
    public static function isCommentUserAgentEnabled()
    {
        return self::getCheckboxOptionState('showCommentUA', false);
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
     * 获取首页需要排除的分类缩略名列表
     *
     * @return array
     */
    public static function getHomeExcludeCategorySlugs()
    {
        if (!self::getCheckboxOptionState('homeExcludeCategoriesEnabled', true)) {
            return array();
        }

        $rawValue = self::getOptionStringValue('homeExcludeCategories', '', false);

        return self::splitOptionList($rawValue);
    }

    /**
     * 获取多选配置项的字符串数组
     *
     * @param string $name
     *
     * @return array
     */
    public static function getOptionArrayValues($name)
    {
        if (!self::hasOption($name)) {
            return array();
        }

        $value = Helper::options()->$name;
        if (is_array($value)) {
            $items = array();
            foreach ($value as $item) {
                $item = trim((string) $item);
                if ($item !== '') {
                    $items[] = $item;
                }
            }

            return array_values(array_unique($items));
        }

        $value = trim((string) $value);
        if ($value === '') {
            return array();
        }

        return self::splitOptionList($value);
    }

    /**
     * 判断主题配置结构是否已完成持久化切换
     *
     * @return bool
     */
    public static function hasThemeConfigSchemaVersion()
    {
        return self::getOptionStringValue('themeConfigSchemaVersion', '', false)
            === self::THEME_CONFIG_SCHEMA_VERSION;
    }

    /**
     * 获取主题配置结构版本号
     *
     * @return string
     */
    public static function getThemeConfigSchemaVersion()
    {
        return self::THEME_CONFIG_SCHEMA_VERSION;
    }

    /**
     * 将部分主题配置中的 string 数据转换为数组或键值对
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
     * 获取归一化后的导航配置
     *
     * @return array
     */
    public static function getNavConfigItems()
    {
        $data = self::decodeConfigArray(self::getOptionStringValue('navConfig', '', false));
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
     * 获取后台导航配置默认示例
     *
     * @return string
     */
    public static function getDefaultNavConfigExample()
    {
        $json = json_encode(
            self::buildDefaultNavConfigItems('https://example.com/'),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        return is_string($json) ? $json : '[]';
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

        $decoded = self::decodeJsonLike($raw);
        if (is_array($decoded)) {
            return $decoded;
        }

        $json = $mode ? '[' . $raw . ']' : '{' . $raw . '}';
        $decoded = self::decodeJsonLike($json);

        return is_array($decoded) ? $decoded : false;
    }

    /**
     * 解析完整 JSON 数组配置
     *
     * 导航配置从现在开始只接受完整 JSON 数组，不再兼容旧片段写法，
     * 以避免继续出现“示例能写但保存后不生效”的歧义。
     *
     * @param string $raw
     *
     * @return array|bool
     */
    private static function decodeConfigArray($raw)
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return false;
        }

        $decoded = self::decodeJsonLike($raw);
        if (!is_array($decoded)) {
            return false;
        }

        return array_is_list($decoded) ? $decoded : false;
    }

    /**
     * 解析 JSON 或近似 JSON 字符串
     *
     * @param string $raw
     *
     * @return mixed
     */
    private static function decodeJsonLike($raw)
    {
        $sanitized = preg_replace('/,\s*([\]}])/m', '$1', (string) $raw);
        if (!is_string($sanitized) || trim($sanitized) === '') {
            return false;
        }

        return json_decode($sanitized, true);
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
        $siteUrl = rtrim((string) Helper::options()->siteUrl, '/') . '/';

        return self::buildDefaultNavConfigItems($siteUrl);
    }

    /**
     * 构建默认导航配置
     *
     * @param string $siteUrl
     *
     * @return array
     */
    private static function buildDefaultNavConfigItems($siteUrl)
    {
        $siteUrl = rtrim((string) $siteUrl, '/') . '/';

        return array(
            array(
                'text' => '首页',
                'href' => $siteUrl,
                'icon' => 'iconfont icon-aria-home',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
            array(
                'text' => '归档',
                'href' => $siteUrl . 'archives/',
                'icon' => 'iconfont icon-aria-archives',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(
                    array(
                        'text' => '日记',
                        'href' => $siteUrl . '1',
                        'icon' => 'iconfont icon-aria-book',
                        'title' => '',
                        'target' => '',
                        'slug' => '',
                        'sub' => array(),
                    ),
                    array(
                        'text' => '项目',
                        'href' => $siteUrl . '2',
                        'icon' => 'iconfont icon-aria-code',
                        'title' => '',
                        'target' => '',
                        'slug' => '',
                        'sub' => array(),
                    ),
                ),
            ),
            array(
                'text' => '留言版',
                'href' => $siteUrl . 'messageboard/',
                'icon' => 'iconfont icon-aria-guestbook',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
            array(
                'text' => '朋友',
                'href' => $siteUrl . 'friends/',
                'icon' => 'iconfont icon-aria-friends',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
            array(
                'text' => '关于',
                'href' => $siteUrl . 'about/',
                'icon' => 'iconfont icon-aria-about',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(
                    array(
                        'text' => '关于本站',
                        'href' => $siteUrl . 'about/',
                        'icon' => 'iconfont icon-aria-home',
                        'title' => '',
                        'target' => '',
                        'slug' => '',
                        'sub' => array(),
                    ),
                    array(
                        'text' => '关于我',
                        'href' => $siteUrl . 'aboutme/',
                        'icon' => 'iconfont icon-aria-like',
                        'title' => '',
                        'target' => '',
                        'slug' => '',
                        'sub' => array(),
                    ),
                ),
            ),
            array(
                'text' => '管理员',
                'href' => $siteUrl . 'admin/',
                'icon' => 'iconfont icon-aria-write',
                'title' => '',
                'target' => '',
                'slug' => '',
                'sub' => array(),
            ),
        );
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
                'url' => 'https://www.beian.gov.cn/portal/registerSystemInfo?recordcode=00000000000000',
                'icon' => '',
                'title' => '公网安备信息',
            ),
        );
    }
}
