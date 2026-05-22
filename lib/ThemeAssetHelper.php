<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeAssetHelper.php
 * 主题资源与 URL 辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.17.1
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
     * 获取首页封面图片 URL
     *
     * @return string
     */
    public static function getCoverUrl()
    {
        $coverConfig = ThemeOptions::getCoverConfigValue();
        if ($coverConfig !== '') {
            $imgs = trim($coverConfig);
            $urls = preg_split('/\r\n|\r|\n/', $imgs);
            if (!is_array($urls)) {
                $urls = array($imgs);
            }
            $urls = array_values(array_filter(array_map('trim', $urls), function ($url) {
                return $url !== '';
            }));
            if (count($urls) > 0) {
                $n = mt_rand(0, count($urls) - 1);
                return $urls[$n];
            }
        }

        return Helper::options()->themeUrl . '/assets/img/cover.webp';
    }

    /**
     * 获取首页封面图片 URL（兼容旧命名）
     *
     * @return string
     */
    public static function getBackgroundUrl()
    {
        return self::getCoverUrl();
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

        return $options->themeUrl . '/assets/img/404.webp';
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

        return $options->themeUrl . '/assets/img/thumbnail.webp';
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

        $relativePath = ltrim($value, '/');
        $assetPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        if (file_exists($assetPath)) {
            return self::getThemeStaticUrl($relativePath);
        }

        if ($defaultRelativePath !== '') {
            return self::getThemeStaticUrl($defaultRelativePath);
        }

        return self::getThemeStaticUrl($relativePath);
    }
}
