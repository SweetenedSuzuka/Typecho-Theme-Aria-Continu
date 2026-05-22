<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeRuntimeConfig.php
 * 主题前端运行时配置辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.18.0
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
            'SHOW_HITOKOTO' => ThemeOptions::isHitokotoEnabled(),
            'SHOW_QRCODE' => ThemeOptions::isPostQrCodeEnabled(),
            'SHOW_REWARD' => count(ThemeOptions::getRewardConfigMap()) > 0,
            'ENABLE_AJAX_COMMENT' => ThemeOptions::isAjaxCommentEnabled(),
            'ENABLE_IMAGE_LIGHTBOX' => ThemeOptions::isImageLightboxEnabled(),
            'ENABLE_LAZYLOAD' => ThemeOptions::isLazyloadEnabled(),
            'ENABLE_LAZYLOAD_PLACEHOLDER' => ThemeOptions::isLazyloadPlaceholderEnabled(),
            'ENABLE_MATHJAX' => ThemeOptions::isMathJaxEnabled(),
            'ENABLE_NAV_HEADROOM' => ThemeOptions::getCheckboxOptionState('enableNavHeadroom', true),
            'OWO_JSON' => $options->OwOJson
                ? (string) $options->OwOJson
                : (string) $options->themeUrl . '/assets/OwO/OwO.json',
            'OWO_STYLE' => (string) $options->themeUrl . '/assets/OwO/OwO.min.css',
            'OWO_SCRIPT' => (string) $options->themeUrl . '/assets/OwO/OwO.min.js',
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
