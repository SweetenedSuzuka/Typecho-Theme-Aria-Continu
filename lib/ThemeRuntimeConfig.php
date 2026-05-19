<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeRuntimeConfig.php
 * 主题前端运行时配置辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.13.1
 */
class ThemeRuntimeConfig
{
    /**
     * 获取前端运行时配置数组
     *
     * @return array
     */
    public static function getThemeConfigMap()
    {
        $options = Helper::options();

        return array(
            'THEME_VERSION' => ARIA_VERSION,
            'SITE_URL' => rtrim((string) $options->siteUrl, '/'),
            'THEME_URL' => (string) $options->themeUrl,
            'SHOW_HITOKOTO' => ThemeOptions::isFeatureEnabled('showHitokoto', 'AriaConfig'),
            'SHOW_QRCODE' => ThemeOptions::isEnabled('showQRCode', 'AriaConfig'),
            'SHOW_REWARD' => count(ThemeOptions::getRewardConfigMap()) > 0,
            'ENABLE_PJAX' => ThemeOptions::isEnabled('enablePjax', 'AriaConfig'),
            'ENABLE_AJAX_COMMENT' => ThemeOptions::isEnabled('enableAjaxComment', 'AriaConfig'),
            'ENABLE_FANCYBOX' => ThemeOptions::isEnabled('enableFancybox', 'AriaConfig'),
            'ENABLE_LAZYLOAD' => ThemeOptions::isEnabled('enableLazyload', 'AriaConfig'),
            'ENABLE_MATHJAX' => ThemeOptions::isFeatureEnabled('enableMathJax', 'AriaConfig'),
            'OWO_JSON' => $options->OwOJson
                ? (string) $options->OwOJson
                : (string) $options->themeUrl . '/assets/OwO/OwO.json',
            'HITOKOTO_ORIGIN' => $options->hitokotoOrigin
                ? (string) $options->hitokotoOrigin
                : 'https://v1.hitokoto.cn/?c=a&encode=text',
            'GRAVATAR_PREFIX' => __TYPECHO_GRAVATAR_PREFIX__,
        );
    }

    /**
     * 获取前端运行时配置脚本 HTML
     *
     * @return string
     */
    public static function getThemeConfigScriptHtml()
    {
        $themeConfig = json_encode((object) self::getThemeConfigMap());
        return "<script>window.THEME_CONFIG = {$themeConfig}</script>\n";
    }
}
