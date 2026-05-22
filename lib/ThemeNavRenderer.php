<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeNavRenderer.php
 * 主题导航渲染辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.18.0
 */
class ThemeNavRenderer
{
    /**
     * 输出导航 HTML
     *
     * @param int $mode
     * @param array|bool $slugs
     *
     * @return void
     */
    public static function showNav($mode, $slugs)
    {
        echo self::getNavHtml($mode, $slugs);
    }

    /**
     * 获取导航 HTML
     *
     * @param int $mode
     * @param array|bool $slugs
     *
     * @return string
     */
    public static function getNavHtml($mode, $slugs)
    {
        $data = ThemeOptions::getNavConfigItems();
        if (empty($data)) {
            return '';
        }

        $itemClass = $mode ? 'nav-right-item' : 'nav-vertical-item';
        $subListClass = $mode ? 'nav-sub' : 'nav-vertical-sub';
        $subItemClass = $mode ? 'sub-item' : 'vertical-sub-item';
        $labelPrefix = $mode ? '' : '  ';
        $html = '';

        foreach ($data as $item) {
            $resolvedItem = self::resolveNavItem($item, $slugs);
            $href = $resolvedItem['href'] !== '' ? ' href="' . self::escapeAttr($resolvedItem['href']) . '"' : '';
            $target = $resolvedItem['target'] !== '' ? ' target="' . self::escapeAttr($resolvedItem['target']) . '"' : '';
            $iconHtml = $resolvedItem['icon'] !== '' ? '<i class="' . self::escapeAttr($resolvedItem['icon']) . '"></i>' : '';
            $textHtml = self::renderNavText($resolvedItem['text']);

            $html .= '<li class="' . $itemClass . '"><a' . $href . $target . '>' . $iconHtml . $labelPrefix . $textHtml . '</a>';

            if (!empty($item['sub'])) {
                $html .= '<ul class="' . $subListClass . '">';
                foreach ($item['sub'] as $subItem) {
                    $resolvedSubItem = self::resolveNavItem($subItem, $slugs);
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

        return $html;
    }

    /**
     * 根据页面 slug 解析导航项
     *
     * @param array $item
     * @param array|bool $slugs
     *
     * @return array
     */
    private static function resolveNavItem(array $item, $slugs)
    {
        $resolvedItem = $item;

        if ($item['slug'] !== '' && $slugs && array_key_exists($item['slug'], $slugs)) {
            $resolvedItem['href'] = $slugs[$item['slug']]['permarlink'];
            $resolvedItem['text'] = $slugs[$item['slug']]['title'];
        }

        return $resolvedItem;
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
}
