<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Utils.php
 * 部分工具
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.17.1
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
        return ThemeOptions::hasOption($name);
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
        return ThemeOptions::getOptionStringValue($name, $default, $useDefaultWhenEmpty);
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
        return ThemeOptions::isOptionEnabled($name, $default);
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
        return ThemeOptions::splitOptionList($value);
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
        return ThemeAssetHelper::getThemeAssetUrl($relativePath);
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
        return ThemeAssetHelper::getThemeStaticUrl($relativePath);
    }

    /**
     * 获取归一化后的 MathJax 配置脚本
     *
     * @return string
     */
    public static function getMathJaxConfigScript()
    {
        return ThemeMathJax::getConfigScript();
    }

    /**
     * 获取 MathJax 视图数据
     *
     * @return array
     */
    public static function getMathJaxViewData()
    {
        return ThemeMathJax::getViewData();
    }

    /**
     * 获取搜索框占位文本
     *
     * @return string
     */
    public static function getSearchPlaceholder()
    {
        return ThemeViewData::getSearchPlaceholder();
    }

    /**
     * 获取页头副标题
     *
     * @return string
     */
    public static function getHeroSubtitle()
    {
        return ThemeViewData::getHeroSubtitle();
    }

    /**
     * 获取网页背景自定义 URL
     *
     * @return string
     */
    public static function getCustomPageBackgroundUrl()
    {
        return ThemeAssetHelper::getCustomPageBackgroundUrl();
    }

    /**
     * 获取页头视图数据
     *
     * @param Widget_Archive $archive
     * @param bool $is404Page
     *
     * @return array
     */
    public static function getHeaderViewData($archive, $is404Page = false)
    {
        return ThemeViewData::getHeaderViewData($archive, $is404Page);
    }

    /**
     * 获取归一化后的页脚链接配置
     *
     * @return array
     */
    public static function getFooterLinkItems()
    {
        return ThemeViewData::getFooterLinkItems();
    }

    /**
     * 获取页脚视图数据
     *
     * @return array
     */
    public static function getFooterViewData()
    {
        return ThemeViewData::getFooterViewData();
    }

    /**
     * 获取评论展示视图数据
     *
     * @return array
     */
    public static function getCommentsViewData()
    {
        return ThemeViewData::getCommentsViewData();
    }

    /**
     * 获取文章页与页面页的视图数据
     *
     * @param Widget_Archive $archive
     * @param string $context
     *
     * @return array
     */
    public static function getPostViewData($archive, $context = 'post')
    {
        return ThemeViewData::getPostViewData($archive, $context);
    }

    /**
     * 获取文章卡片视图数据
     *
     * @param Widget_Archive $archive
     * @param string $context
     *
     * @return array
     */
    public static function getPostCardViewData($archive, $context = 'index')
    {
        return ThemeViewData::getPostCardViewData($archive, $context);
    }

    /**
     * 获取评论框背景图 URL
     *
     * @return string
     */
    public static function getCustomCommentBoxBackgroundUrl()
    {
        return ThemeAssetHelper::getCustomCommentBoxBackgroundUrl();
    }

    /**
     * 输出博客以及主题部分配置信息为前端提供接口
     *
     * @return void
     */
    public static function AriaConfig()
    {
        echo ThemeRuntimeConfig::getThemeConfigScriptHtml();
    }

    /**
     * 获取第一管理员的头像
     *
     * @param int $size 尺寸
     * @return void
     */
    public static function getAdminAvatarUrl($size = 50)
    {
        return ThemeSiteLookup::getAdminAvatarUrl($size);
    }

    /**
     * 获取第一管理员的头像
     *
     * @param int $size 尺寸
     * @return void
     */
    public static function getAdminAvatar($size = 50)
    {
        echo self::getAdminAvatarUrl($size);
    }

    /**
     * 获取所有页面的信息，根据slug构造键值对数组
     *
     * @return array|bool
     */
    public static function getPagesInfo()
    {
        return ThemeSiteLookup::getPagesInfo();
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
        return ThemeOptions::convertConfigData($item, $mode);
    }

    /**
     * 获取归一化后的导航配置
     *
     * @return array
     */
    public static function getNavConfigItems()
    {
        return ThemeOptions::getNavConfigItems();
    }

    /**
     * 获取归一化后的页脚扩展链接配置
     *
     * @return array
     */
    public static function getFooterWidgetItems()
    {
        return ThemeOptions::getFooterWidgetItems();
    }

    /**
     * 获取归一化后的页脚备案配置
     *
     * @return array
     */
    public static function getFooterRecords()
    {
        return ThemeOptions::getFooterRecords();
    }

    /**
     * 获取归一化后的打赏配置
     *
     * @return array
     */
    public static function getRewardConfigMap()
    {
        return ThemeOptions::getRewardConfigMap();
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
        return ThemeAssetHelper::getBackgroundUrl();
    }

    /**
     * 获取 404 背景图片 URL
     *
     * @return string
     */
    public static function get404BackgroundUrl()
    {
        return ThemeAssetHelper::get404BackgroundUrl();
    }

    /**
     * 获取随机默认缩略图
     */
    public static function getThumbnail()
    {
        return ThemeAssetHelper::getThumbnail();
    }

    /**
     * 获取页脚链接 HTML
     *
     * @return string
     */
    public static function getFooterWidgetHtml()
    {
        return ThemeViewData::getFooterWidgetHtml();
    }

    /**
     * 输出底部组件
     *
     * @return void
     */
    public static function getFooterWidget()
    {
        echo ThemeViewData::getFooterWidgetHtml();
    }

    /**
     * 获取页脚备案信息 HTML
     *
     * @return string
     */
    public static function getFooterRecordsHtml()
    {
        return ThemeViewData::getFooterRecordsHtml();
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
        return ThemeViewData::getCopyrightYears();
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
        ThemeNavRenderer::showNav($mode, $slugs);
    }
}
