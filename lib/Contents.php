<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Contents.php
 * 文章/页面相关组件
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.16.0
 */

class Contents
{
    /**
     * 浏览量字段检查结果缓存
     *
     * @var bool|null
     */
    private static $hasViewsColumn = null;

    /**
     * 归档时间轴 HTML 请求内缓存
     *
     * @var string|null
     */
    private static $archiveTimelineHtml = null;

    /**
     * 上下篇结果请求内缓存
     *
     * @var array
     */
    private static $nextPrevCache = array();

    /**
     * 文章字段值请求内缓存
     *
     * @var array
     */
    private static $fieldValueCache = array();

    /**
     * 文章浏览量请求内缓存
     *
     * @var array
     */
    private static $postViewsCache = array();

    /**
     * 已记录浏览的文章 ID 请求内缓存
     *
     * @var array|null
     */
    private static $viewedContentIds = null;

    /**
     * 从数据库查询上/下篇文章内容信息
     * 返回内容包括文章缩略、标题、链接
     *
     * @param bool $mode 查询上或下篇
     * @param mixed $archive
     *
     * @return array|bool
     */

    public static function getNextPrev($mode, $archive)
    {
        $cacheKey = ($mode ? 'prev:' : 'next:') . (int) $archive->cid;
        if (array_key_exists($cacheKey, self::$nextPrevCache)) {
            return self::$nextPrevCache[$cacheKey];
        }

        $db = Typecho_Db::get();
        static $archiveWidgets = array();
        //数据准备
        $where = null;
        $sorted = null;
        //$mode为true查询上文，false查询下文
        if ($mode) {
            $where = 'table.contents.created < ?';
            $sorted = Typecho_Db::SORT_DESC;
        } else {
            $where = 'table.contents.created > ?';
            $sorted = Typecho_Db::SORT_ASC;
        }

        $query = $db->select()->from('table.contents')
            ->where($where, $archive->created)
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', $archive->type)
            ->where('table.contents.password IS NULL')
            ->order('table.contents.created', $sorted)
            ->limit(1);
        $content = $db->fetchRow($query);
        $result = null;
        if ($content) {
            // 原版Aria的方法，在Typecho1.3不生效
            // $content = $archive->filter($content);
            // $title = $content['title'];
            // $link = $content['permalink'];
            // 这个能用，但是链接会保留{xxx}的参数，虽然会被正确解析，但是html里面会留着，看着有点不舒服
            // $link = Typecho_Router::url(
            //     'post',
            //     $content,
            //     $options->index
            // );
            // 再换一个方法
            $archiveType = (string) $archive->type;
            if (!array_key_exists($archiveType, $archiveWidgets)) {
                $archiveWidgets[$archiveType] = Typecho_Widget::widget('Widget_Archive@nextprev', 'type=' . $archiveType, null);
            }
            $widget = $archiveWidgets[$archiveType];
            $widget->push($content);
            $link = $widget->permalink;
            $title = $widget->title;

            $thumbnail = self::getFieldStringValue($db, (int) $content['cid'], 'thumbnail');
            $img = $thumbnail !== '' ? $thumbnail : ThemeAssetHelper::getThumbnail();

            $result = array('img' => $img, 'title' => $title, 'link' => $link);
        } else {
            $result = false;
        }
        self::$nextPrevCache[$cacheKey] = $result;
        return $result;
    }

    /**
     * 获取字段字符串值，并在请求内缓存
     *
     * @param Typecho_Db $db
     * @param int $cid
     * @param string $name
     *
     * @return string
     */
    private static function getFieldStringValue($db, $cid, $name)
    {
        $cacheKey = $cid . ':' . $name;
        if (array_key_exists($cacheKey, self::$fieldValueCache)) {
            return self::$fieldValueCache[$cacheKey];
        }

        $row = $db->fetchRow(
            $db->select('str_value')
                ->from('table.fields')
                ->where('table.fields.cid = ?', $cid)
                ->where('table.fields.name = ?', $name)
                ->limit(1)
        );

        self::$fieldValueCache[$cacheKey] = is_array($row) && !empty($row['str_value'])
            ? (string) $row['str_value']
            : '';

        return self::$fieldValueCache[$cacheKey];
    }


    /**
     * 获取上下文内容 HTML，包括缩略图、标题、链接
     *
     * @param mixed $archive
     *
     * @return string
     */
    public static function getNextPrevHtml($archive)
    {
        $html = '';

        $prevResult = self::getNextPrev(true, $archive);
        $nextResult = self::getNextPrev(false, $archive);

        if (!$prevResult && !$nextResult) {
            return '';
        } else if (!$prevResult) {
            //没有上一篇了
            //只显示下一篇
            $html .= '<div class="post-footer-box half next" style="width:100%"><a href="' . $nextResult["link"] . '" rel="next"><div class="post-footer-thumbnail"><img src="' . $nextResult["img"] . '"></div><span class="post-footer-label">Next Post</span><div class="post-footer-title"><h3>' . $nextResult["title"] . '</h3></div></a></div>';
        } else if (!$nextResult) {
            //没有下一篇
            //只显示上一篇
            $html .= '<div class="post-footer-box half previous" style="width:100%"><a href="' . $prevResult["link"] . '" rel="prev"><div class="post-footer-thumbnail"><img src="' . $prevResult["img"] . '"></div><span class="post-footer-label">Previous Post</span><div class="post-footer-title"><h3>' . $prevResult["title"] . '</h3></div></a></div>';
        } else {
            $html .= '<div class="post-footer-box half previous"><a href="' . $prevResult["link"] . '" rel="prev"><div class="post-footer-thumbnail"><img src="' . $prevResult["img"] . '"></div><span class="post-footer-label">Previous Post</span><div class="post-footer-title"><h3>' . $prevResult["title"] . '</h3></div></a></div>';
            $html .= '<div class="post-footer-box half next"><a href="' . $nextResult["link"] . '" rel="next"><div class="post-footer-thumbnail"><img src="' . $nextResult["img"] . '"></div><span class="post-footer-label">Next Post</span><div class="post-footer-title"><h3>' . $nextResult["title"] . '</h3></div></a></div>';
        }

        return $html;
    }

    /**
     * 输出上下文内容，包括缩略图、标题、链接
     *
     * @param mixed $archive
     *
     * @return void
     */
    public static function theNextPrev($archive)
    {
        echo self::getNextPrevHtml($archive);
    }

    /**
     * 获取文章浏览次数
     *
     * @param mixed $archive
     *
     * @return int
     */
    public static function getPostViewCount($archive)
    {
        $cid = (int) $archive->cid;
        $db = Typecho_Db::get();

        if (!self::hasViewsColumn($db)) {
            return 0;
        }

        if (!array_key_exists($cid, self::$postViewsCache)) {
            $archiveViews = self::getArchiveViewsValue($archive);
            if ($archiveViews !== null) {
                self::$postViewsCache[$cid] = $archiveViews;
            } else {
                $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
                self::$postViewsCache[$cid] = isset($row['views']) ? (int) $row['views'] : 0;
            }
        }

        $viewsCount = self::$postViewsCache[$cid];

        if ($archive->is('single')) {
            $views = self::getViewedContentIds();
            if (!in_array($cid, $views, true)) {
                $viewsCount++;
                $db->query($db->update('table.contents')->rows(array('views' => $viewsCount))->where('cid = ?', $cid));
                self::$postViewsCache[$cid] = $viewsCount;
                $views[] = $cid;
                self::$viewedContentIds = $views;
                Typecho_Cookie::set('extend_contents_views', implode(',', $views)); //记录查看cookie
            }
        }

        return $viewsCount;
    }

    /**
     * 输出文章浏览次数
     *
     * @param mixed $archive
     *
     * @return void
     */
    public static function getPostView($archive)
    {
        echo self::getPostViewCount($archive);
    }

    /**
     * 尝试从当前文章对象中直接读取浏览量
     *
     * 某些列表和单篇上下文已经自带 `views` 字段，此时可避免再次查询数据库；
     * 如果当前上下文未提供该字段，则返回 null 并回退到数据库查询。
     *
     * @param mixed $archive
     *
     * @return int|null
     */
    private static function getArchiveViewsValue($archive)
    {
        if (is_array($archive) && array_key_exists('views', $archive) && is_numeric($archive['views'])) {
            return (int) $archive['views'];
        }

        if (!is_object($archive)) {
            return null;
        }

        if (isset($archive->row) && is_array($archive->row) && array_key_exists('views', $archive->row) && is_numeric($archive->row['views'])) {
            return (int) $archive->row['views'];
        }

        $views = null;
        if (method_exists($archive, '__get') || property_exists($archive, 'views')) {
            $views = $archive->views;
        }

        return is_numeric($views) ? (int) $views : null;
    }

    /**
     * 获取已记录浏览的文章 ID 列表
     *
     * @return array
     */
    private static function getViewedContentIds()
    {
        if (self::$viewedContentIds !== null) {
            return self::$viewedContentIds;
        }

        $views = Typecho_Cookie::get('extend_contents_views');
        if (empty($views)) {
            self::$viewedContentIds = array();
            return self::$viewedContentIds;
        }

        self::$viewedContentIds = array_values(array_filter(array_map('intval', explode(',', $views))));
        return self::$viewedContentIds;
    }

    /**
     * 检查 contents 表是否存在浏览量字段
     *
     * @param Typecho_Db $db
     *
     * @return bool
     */
    private static function hasViewsColumn($db)
    {
        if (self::$hasViewsColumn !== null) {
            return self::$hasViewsColumn;
        }

        $row = $db->fetchRow($db->select()->from('table.contents')->limit(1));
        self::$hasViewsColumn = is_array($row) && array_key_exists('views', $row);

        return self::$hasViewsColumn;
    }

    /**
     * 获取文章打赏二维码和本文链接二维码 HTML
     *
     * @param mixed $archive
     *
     * @return string
     */
    public static function getPostOtherHtml($archive)
    {
        $html = '<div class="post-other">';
        $rewardConfig = ThemeOptions::getRewardConfigMap();
        $showQRCode = ThemeOptions::isEnabled('showQRCode', 'AriaConfig');

        if ($rewardConfig) {
            $html .= '<div class="post-reward"><a href="javascript:void(0);" data-aria-action="toggle-post-other"><i class="iconfont icon-aria-reward"></i></a>
                <ul>';
            foreach ($rewardConfig as $key => $data) {
                $html .= '<li><img no-lazyload src="' . htmlspecialchars($data, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $html .= "</ul></div>";
        }
        if ($showQRCode) {
            $url = Helper::options()->themeUrl . '/lib/getQRCode.php?url=';
            $html .= '<div class="post-qrcode"><a href="javascript:void(0);" data-aria-action="toggle-post-other"><i class="iconfont icon-aria-qrcode"></i></a><div><span>手机上阅读<img no-lazyload src="' . $url . $archive->permalink . '"></span></div></div>';
        }
        $html .= "</div>";
        return $html;
    }

    /**
     * 输出文章打赏二维码和本文链接二维码
     *
     * @param mixed $archive
     *
     * @return void
     */
    public static function getPostOther($archive)
    {
        echo self::getPostOtherHtml($archive);
    }

    /**
     * 输出归档页时间轴
     *
     * @param mixed $_POST
     *
     * @return void
     */
    public static function pageArchives($post)
    {
        static $lastY = null,
        $lastM = null;
        $t = $post->created;
        $href = $post->permalink;
        $title = $post->title;
        $y = date('Y', $t) . ' 年';
        $m = date('m', $t) . ' 月';
        $d = date('d', $t) . ' 日';
        $t_href = Helper::options()->siteUrl . date('Y/m', $t);
        $html = '';
        if ($lastY == date('Y', $t) || $lastY == null) {
            if ($lastM != date('m', $t)) {
                $lastM = date('m', $t);
                $html .= "<div class=\"timeline-ym timeline-item\"><a href=\"$t_href\" target=\"_blank\">$y $m</a></div>";
            }
        } else {
            $lastY = date('Y', $t);
        }
        $html .= '<div class="timeline-box"><div class="timeline-post timeline-item">' . '<a href="' . $href . '" target="_blank">' . $title . '</a><span class="timeline-post-time">' . $d . '</span></div></div>';
        echo $html;
    }

    /**
     * 获取归档时间轴 HTML
     *
     * 使用更轻量的查询替代 `Widget_Contents_Post_Recent pageSize=10000`，
     * 只读取时间轴实际需要的字段，并在一次请求内复用结果。
     *
     * @return string
     */
    public static function getArchiveTimelineHtml()
    {
        if (self::$archiveTimelineHtml !== null) {
            return self::$archiveTimelineHtml;
        }

        $db = Typecho_Db::get();
        $rows = $db->fetchAll(
            $db->select('cid', 'title', 'slug', 'created', 'type', 'status', 'password')
                ->from('table.contents')
                ->where('table.contents.status = ?', 'publish')
                ->where('table.contents.type = ?', 'post')
                ->where('table.contents.password IS NULL')
                ->order('table.contents.created', Typecho_Db::SORT_DESC)
        );

        if (empty($rows)) {
            self::$archiveTimelineHtml = '';
            return self::$archiveTimelineHtml;
        }

        $widget = Typecho_Widget::widget('Widget_Archive@pageArchives', 'type=post', null);
        $lastMonthKey = null;
        $html = '';

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $widget->push($row);
            $monthKey = date('Y-m', $widget->created);
            if ($lastMonthKey !== $monthKey) {
                $lastMonthKey = $monthKey;
                $monthArchiveUrl = Helper::options()->siteUrl . date('Y/m', $widget->created);
                $html .= '<div class="timeline-ym timeline-item"><a href="'
                    . htmlspecialchars($monthArchiveUrl, ENT_QUOTES, 'UTF-8')
                    . '" target="_blank">'
                    . date('Y 年 m 月', $widget->created)
                    . '</a></div>';
            }

            $html .= '<div class="timeline-box"><div class="timeline-post timeline-item"><a href="'
                . htmlspecialchars($widget->permalink, ENT_QUOTES, 'UTF-8')
                . '" target="_blank">'
                . htmlspecialchars($widget->title, ENT_QUOTES, 'UTF-8')
                . '</a><span class="timeline-post-time">'
                . date('d 日', $widget->created)
                . '</span></div></div>';
        }

        self::$archiveTimelineHtml = $html;
        return self::$archiveTimelineHtml;
    }

    /**
     * 解析所有文章内容
     *
     * @param mixed $content
     * @param mixed $widget
     * @param mixed $lastResult
     *
     * @return mixed
     */

    public static function parse($content, $widget, $lastContent)
    {
        $content = empty($lastContent) ? $content : $lastContent;
        if ($widget instanceof Widget_Abstract) {
            add_shortcode('link-item', 'Contents::shortcode_linkitem');
            add_shortcode('link-box', 'Contents::shortcode_linkbox');
            $content = self::parseHljsWrap($content, $widget);

            $content = do_shortcode($content);
        }

        return $content;
    }

    /**
     * 给<pre>增加class
     *
     * @param mixed $content
     * @param mixed $widget
     *
     * @return mixed
     */

    public static function parseHljsWrap($content, $widget)
    {
        $preg = "/<pre.*?>/";
        $replace = '<pre class="highlight-wrap">';

        $content = preg_replace($preg, $replace, $content);

        return $content;
    }

    /**
     * [link-item]短代码
     *
     * @param $atts
     * @param $content
     *
     * @return mixed
     */

    public static function shortcode_linkitem($atts, $content = '')
    {
        $args = shortcode_atts(array(
            'href' => '',
            'title' => '',
            'img' => '',
            'name' => '',
        ), $atts);
        $href = $args['href'] ? 'href="' . $args['href'] . '"' : "";
        return '<a ' . $href . 'title="' . $args['title'] . '" target="_blank"><div class="link-item"><img class="link-avatar" src="' . $args['img'] . '"><span class="link-name">' . $args['name'] . '</span></div></a>';
    }

    /**
     * [link-box]短代码
     *
     * @param $atts
     * @param $content
     *
     * @return mixed
     */

    public static function shortcode_linkbox($atts, $content = '')
    {
        return '<div class="link-box">' . do_shortcode($content) . '</div>';
    }
    //[link-item]
}
