<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeAssetHelper.php
 * 主题资源与 URL 辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.16.0
 */
class ThemeAssetHelper
{
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
     * 获取网页背景自定义 URL
     *
     * @return string
     */
    public static function getCustomPageBackgroundUrl()
    {
        $customPath = ThemeOptions::getOptionStringValue(
            'customPageBackgroundUrl',
            '/assets/img/background.webp'
        );

        return self::resolveThemeRelativeOrAbsoluteUrl($customPath, 'assets/img/background.webp');
    }

    /**
     * 获取评论框背景图 URL
     *
     * @return string
     */
    public static function getCustomCommentBoxBackgroundUrl()
    {
        $customPath = ThemeOptions::getOptionStringValue('customCommentBoxBackgroundUrl', '', false);
        if ($customPath === '') {
            return '';
        }

        return self::resolveThemeRelativeOrAbsoluteUrl($customPath);
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
     *
     * @return string
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
        }

        return $options->themeUrl . '/assets/img/thumbnail.jpg';
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
}
