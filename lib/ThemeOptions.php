<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeOptions.php
 * 主题配置读取与归一化
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.15.0
 */
class ThemeOptions
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
        return !empty($config) && in_array($item, $config);
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
