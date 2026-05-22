<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeScriptAssets.php
 * 主题脚本资源辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.17.1
 */
class ThemeScriptAssets
{
    /**
     * 获取页脚脚本资源列表
     *
     * @return array
     */
    public static function getFooterScriptUrls()
    {
        $scripts = array();

        if (ThemeOptions::isFancyboxEnabled()) {
            $scripts[] = ThemeAssetHelper::getThemeAssetUrl('assets/js/jquery.fancybox.min.js');
        }

        $scripts[] = ThemeAssetHelper::getThemeAssetUrl('assets/js/highlight.min.js');

        return array_merge($scripts, array(
            ThemeAssetHelper::getThemeAssetUrl('assets/js/modules/base.js'),
            ThemeAssetHelper::getThemeAssetUrl('assets/js/modules/core.js'),
            ThemeAssetHelper::getThemeAssetUrl('assets/js/modules/comment.js'),
            ThemeAssetHelper::getThemeAssetUrl('assets/js/modules/action.js'),
            ThemeAssetHelper::getThemeAssetUrl('assets/js/modules/toc.js'),
        ));
    }

    /**
     * 获取主题主脚本地址
     *
     * @return string
     */
    public static function getMainScriptUrl()
    {
        return ThemeAssetHelper::getThemeAssetUrl('assets/js/main.js');
    }

    /**
     * 获取自定义脚本 HTML
     *
     * @return string
     */
    public static function getCustomScriptHtml()
    {
        if (!ThemeOptions::isAdvancedCustomCodeEnabled()) {
            return '';
        }

        $customScript = ThemeOptions::hasOption('customScript')
            ? trim((string) Helper::options()->customScript)
            : '';
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
    public static function getStatisticsHtml()
    {
        return ThemeOptions::hasOption('statistics') ? (string) Helper::options()->statistics : '';
    }
}
