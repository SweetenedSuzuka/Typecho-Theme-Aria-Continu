<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * Comments.php
 * 评论相关组件
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.13.0
 */

class Comments
{
    /**
     * 已审核评论信息请求内缓存
     *
     * @var array
     */
    private static $approvedCommentRowCache = array();

    /**
     * 评论父级 ID 请求内缓存
     *
     * @var array
     */
    private static $commentParentCache = array();

    /**
     * 评论作者名请求内缓存
     *
     * @var array
     */
    private static $commentAuthorCache = array();

    /**
     * 评论楼层关系请求内缓存
     *
     * @var array
     */
    private static $commentThreadMapCache = array();

    /**
     * 文章永久链接请求内缓存
     *
     * @var array
     */
    private static $contentPermalinkCache = array();

    /**
     * UA 解析结果请求内缓存
     *
     * @var array
     */
    private static $parsedUserAgentCache = array();

    /**
     * 获取已审核评论行
     *
     * @param int $coid
     *
     * @return array|false
     */
    private static function getApprovedCommentRow($coid)
    {
        $cacheKey = (int) $coid;
        if (array_key_exists($cacheKey, self::$approvedCommentRowCache)) {
            return self::$approvedCommentRowCache[$cacheKey];
        }

        $db = Typecho_Db::get();
        $row = $db->fetchRow(
            $db->select('coid', 'cid', 'parent', 'type', 'author', 'text')
                ->from('table.comments')
                ->where('coid = ? AND status = ?', $cacheKey, 'approved')
        );

        self::$approvedCommentRowCache[$cacheKey] = empty($row) ? false : $row;
        return self::$approvedCommentRowCache[$cacheKey];
    }

    /**
     * 获取评论父级 ID
     *
     * @param int $coid
     *
     * @return int
     */
    private static function getCommentParentId($coid)
    {
        $cacheKey = (int) $coid;
        if (array_key_exists($cacheKey, self::$commentParentCache)) {
            return self::$commentParentCache[$cacheKey];
        }

        $approvedRow = self::getApprovedCommentRow($cacheKey);
        if (is_array($approvedRow) && array_key_exists('parent', $approvedRow)) {
            self::$commentParentCache[$cacheKey] = (int) $approvedRow['parent'];
            return self::$commentParentCache[$cacheKey];
        }

        $db = Typecho_Db::get();
        $row = $db->fetchRow(
            $db->select('parent')
                ->from('table.comments')
                ->where('coid = ?', $cacheKey)
        );

        self::$commentParentCache[$cacheKey] = is_array($row) && array_key_exists('parent', $row)
            ? (int) $row['parent']
            : 0;

        return self::$commentParentCache[$cacheKey];
    }

    /**
     * 获取评论作者名
     *
     * @param int $coid
     *
     * @return string
     */
    private static function getCommentAuthor($coid)
    {
        $cacheKey = (int) $coid;
        if (array_key_exists($cacheKey, self::$commentAuthorCache)) {
            return self::$commentAuthorCache[$cacheKey];
        }

        $approvedRow = self::getApprovedCommentRow($cacheKey);
        if (is_array($approvedRow) && array_key_exists('author', $approvedRow)) {
            self::$commentAuthorCache[$cacheKey] = (string) $approvedRow['author'];
            return self::$commentAuthorCache[$cacheKey];
        }

        $db = Typecho_Db::get();
        $row = $db->fetchRow(
            $db->select('author')
                ->from('table.comments')
                ->where('coid = ?', $cacheKey)
        );

        self::$commentAuthorCache[$cacheKey] = is_array($row) && array_key_exists('author', $row)
            ? (string) $row['author']
            : '';

        return self::$commentAuthorCache[$cacheKey];
    }

    /**
     * 获取文章永久链接
     *
     * @param int $cid
     *
     * @return string
     */
    private static function getContentPermalink($cid)
    {
        $cacheKey = (int) $cid;
        if (array_key_exists($cacheKey, self::$contentPermalinkCache)) {
            return self::$contentPermalinkCache[$cacheKey];
        }

        static $contentsWidget = null;
        if ($contentsWidget === null) {
            $contentsWidget = Typecho_Widget::widget('Widget_Abstract_Contents');
        }

        $db = Typecho_Db::get();
        $row = $db->fetchRow(
            $contentsWidget->select()->where('table.contents.cid = ?', $cacheKey)
        );
        if (!is_array($row)) {
            self::$contentPermalinkCache[$cacheKey] = '';
            return self::$contentPermalinkCache[$cacheKey];
        }

        $content = $contentsWidget->push($row);
        self::$contentPermalinkCache[$cacheKey] = isset($content['permalink'])
            ? rtrim((string) $content['permalink'], '/')
            : '';

        return self::$contentPermalinkCache[$cacheKey];
    }

    /**
     * 获取评论楼层映射
     *
     * @param int $cid
     * @param bool $commentOnly
     * @param string $order
     *
     * @return array
     */
    private static function getCommentThreadMap($cid, $commentOnly, $order)
    {
        $cacheKey = (int) $cid . ':' . ($commentOnly ? 'comment' : 'all') . ':' . strtoupper((string) $order);
        if (array_key_exists($cacheKey, self::$commentThreadMapCache)) {
            return self::$commentThreadMapCache[$cacheKey];
        }

        $db = Typecho_Db::get();
        $select = $db->select('coid', 'parent')
            ->from('table.comments')
            ->where('cid = ? AND status = ?', (int) $cid, 'approved')
            ->order('coid');
        if ($commentOnly) {
            $select->where('type = ?', 'comment');
        }

        $comments = $db->fetchAll($select);
        if (strtoupper((string) $order) === 'DESC') {
            $comments = array_reverse($comments);
        }

        $threadMap = array();
        foreach ($comments as $comment) {
            if (!is_array($comment) || !isset($comment['coid'])) {
                continue;
            }
            $threadMap[(int) $comment['coid']] = isset($comment['parent']) ? (int) $comment['parent'] : 0;
        }

        self::$commentThreadMapCache[$cacheKey] = $threadMap;
        return self::$commentThreadMapCache[$cacheKey];
    }

    /**
     * 由$coid查询评论相关内容
     * 返回未解析评论内容以及链接
     *
     * @param int $coid
     *
     * @return array
     */

    public static function getInfo($coid)
    {
        $options = Helper::options();
        $row = self::getApprovedCommentRow($coid);
        if (empty($row)) {
            return 'Comment not found!';
        }

        $cid = (int) $row['cid'];
        $threadMap = self::getCommentThreadMap($cid, (bool) $options->commentsShowCommentOnly, (string) $options->commentsOrder);

        $i = $coid;
        $break = $i;
        while ($i != 0) {
            $break = $i;
            $i = array_key_exists($i, $threadMap) ? $threadMap[$i] : 0;
        }
        $count = 0;
        foreach ($threadMap as $key => $val) {
            if ($val == 0) {
                $count++;
            }

            if ($key == $break) {
                break;
            }

        }
        $permalink = self::getContentPermalink($cid);
        $page = ($options->commentsPageBreak) ? '/comment-page-' . ceil($count / $options->commentsPageSize) : (substr($permalink, -5, 5) == '.html' ? '' : '/');
        return array("author" => $row['author'], "text" => $row['text'], "href" => "{$permalink}{$page}#{$row['type']}-{$coid}");
    }

    /**
     * 评论内容解析
     *
     * @param mixed $content
     * @param mixed $widget
     * @param mixed $lastContent
     *
     * @return mixed
     */

    public static function parse($content, $widget, $lastContent)
    {
        $content = empty($lastContent) ? $content : $lastContent;
        if ($widget instanceof Widget_Abstract) {
            $content = self::commentAt($content, $widget);
            $content = self::parseHljsWrap($content, $widget);
        }

        return $content;
    }

    /**
     * 评论回复加上@
     *
     * @param mixed $content
     * @param mixed $widget
     *
     * @return mixed
     */

    public static function commentAt($content, $widget)
    {
        $coid = $widget->coid;
        $parent = self::getCommentParentId($coid);
        if ($parent != "0") {
            $author = self::getCommentAuthor($parent);
            $tag = '<a href="#comment-' . $parent . '">@' . $author . '</a><br>';
            return $tag . $content;
        } else {
            return $content;
        }
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
        $preg = "/<pre>/";
        $replace = '<pre class="highlight-wrap">';

        $content = preg_replace($preg, $replace, $content);
        
        return $content;
    }

    /**
     * 评论回复/取消回复按钮JS代码
     *
     * @param mixed $archive
     *
     * @return void
     */

    public static function commentReply($archive)
    {
        if ($archive->allow('comment')) {
            echo "<script type=\"text/javascript\">(function(){window.TypechoComment={dom:function(id){return document.getElementById(id)},create:function(tag,attr){var el=document.createElement(tag);for(var key in attr){el.setAttribute(key,attr[key])}return el},reply:function(cid,coid){var comment=this.dom(cid),parent=comment.parentNode,response=this.dom('$archive->respondId'),input=this.dom('comment-parent'),form='form'==response.tagName?response:response.getElementsByTagName('form')[0],textarea=response.getElementsByTagName('textarea')[0];if(null==input){input=this.create('input',{'type':'hidden','name':'parent','id':'comment-parent'});form.appendChild(input)}input.setAttribute('value',coid);if(null==this.dom('comment-form-place-holder')){var holder=this.create('div',{'id':'comment-form-place-holder'});response.parentNode.insertBefore(holder,response)}comment.appendChild(response);this.dom('cancel-comment-reply-link').style.display='';if(null!=textarea&&'text'==textarea.name){textarea.focus()}return false},cancelReply:function(){var response=this.dom('$archive->respondId'),holder=this.dom('comment-form-place-holder'),input=this.dom('comment-parent');if(null!=input){input.parentNode.removeChild(input)}if(null==holder){return true}this.dom('cancel-comment-reply-link').style.display='none';holder.parentNode.insertBefore(response,holder);return false}}})();</script>";
        }
    }

    /**
     * 解析评论user-agent
     *
     * @param string $ua
     *
     * @return string
     */
//新增osName和browserName用来输出系统和浏览器名，顺便缩进代码（怎么会有不缩进的沙雕）
    public static function parseUseragent($ua)
    {
        $cacheKey = (string) $ua;
        if (array_key_exists($cacheKey, self::$parsedUserAgentCache)) {
            return self::$parsedUserAgentCache[$cacheKey];
        }

        // 解析操作系统
        $htmlTag = "";
        $os = null;
        $fontClass = null;
        $osName = null;
        if (preg_match('/Windows NT 6.0/i', $ua)) {
            $os = "Windows Vista";
            $fontClass = "windows";
        } elseif (preg_match('/Windows NT 6.1/i', $ua)) {
            $os = "Windows 7";
            $osName = "Windows 7";
            $fontClass = "windows";
        } elseif (preg_match('/Windows NT 6.2/i', $ua)) {
            $os = "Windows 8";
            $osName = "Windows 8";
            $fontClass = "windows";
        } elseif (preg_match('/Windows NT 6.3/i', $ua)) {
            $os = "Windows 8.1";
            $osName = "Windows 8.1";
            $fontClass = "windows";
        } elseif (preg_match('/Windows NT 10.0/i', $ua)) {
            $os = "Windows 10";
            $osName = "Windows 10/11";
            $fontClass = "windows";
        } elseif (preg_match('/Windows NT 5.1/i', $ua)) {
            $os = "Windows XP";
            $osName = "Windows XP";
            $fontClass = "windows";
        } elseif (preg_match('/Windows NT 5.2/i', $ua) && preg_match('/Win64/i', $ua)) {
            $os = "Windows XP 64 bit";
            $osName = "Windows XP x64";
            $fontClass = "windows";
        } elseif (preg_match('/OpenHarmony ([0-9.]+)/i', $ua, $matches)) {
            $os = 'OpenHarmony ' . $matches[1];
            $osName = "OpenHarmony " . $matches[1];
            if (stripos($ua, 'Mobile') !== false) {
                $fontClass = 'iphone';
            } else {
                $fontClass = 'os';
            }
        } elseif (preg_match('/HarmonyOS ([0-9.]+)/i', $ua, $matches)) {
            // 目前（2025）华为的5.0用的还是OpenHarmony，所以这样写没什么用，但姑且先加上吧。
            $os = 'HarmonyOS' . $matches[1];
            $osName = "HarmonyOS" . $matches[1];
            if (stripos($ua, 'Mobile') !== false) {
                $fontClass = 'iphone';
            } else {
                $fontClass = 'os';
            }
        } elseif (preg_match('/HarmonyOS/i', $ua, $matches)) {
            // 华为在5.0之前的UA同时包含Android 10/12和HarmonyOS两项，而且没有具体版本号。
            $os = 'HarmonyOS';
            $osName = "HarmonyOS";
            $fontClass = "iphone";
        } elseif (preg_match('/Android ([0-9.]+)/i', $ua, $matches)) {
            $os = "Android " . $matches[1];
            $osName = "Android " . $matches[1];
            $fontClass = "android";
        } elseif (preg_match('/iPhone OS ([_0-9]+)/i', $ua, $matches)) {
            $version = str_replace('_', '.', $matches[1]);
            $os = 'iPhoneOS ' . $version;
            $osName = 'iPhoneOS ' . $version;
            // $os = 'iPhone ' . $matches[1];
            // $osName = 'iPhone ' . $matches[1];
            $fontClass = "iphone";
        } elseif (preg_match('/iPad/i', $ua)) {
            $os = "iPad";
            $osName = "iPad OS";
            $fontClass = "ipad";
        } elseif (preg_match('/Mac OS X ([_0-9]+)/i', $ua, $matches)) {
            $version = str_replace('_', '.', $matches[1]);
            $os = 'Mac OS X ' . $version;
            $osName = 'Mac OS X ' . $version;
            // $os = 'Mac OS X ' . $matches[1];
            // $osName = 'Mac OS X ' . $matches[1];
            $fontClass = "mac";
        } elseif (preg_match('/Gentoo/i', $ua)) {
            $os = 'Gentoo Linux';
            $osName = "Gentoo";
            $fontClass = "gentoo";
        } elseif (preg_match('/Ubuntu/i', $ua)) {
            $os = 'Ubuntu Linux';
            $osName = "Ubuntu";
            $fontClass = "ubuntu";
        } elseif (preg_match('/Debian/i', $ua)) {
            $os = 'Debian Linux';
            $osName = "Debian";
            $fontClass = "debian";
        } elseif (preg_match('/X11; FreeBSD/i', $ua)) {
            $os = 'FreeBSD';
            $osName = "FreeBSD";
            $fontClass = "freebsd";
        } elseif (preg_match('/X11; Linux/i', $ua)) {
            $os = 'Linux';
            $osName = "Linux";
            $fontClass = "linux";
        } else { 
            $os = 'unknown os';
            $osName = "未知操作系统";
            $fontClass = "os";
        }

        $htmlTag = "<i class=\"iconfont icon-aria-$fontClass\"></i><a class=\"comment-meta\"><span>$osName</span></a>";

        $browser = null;
        $browserName = null;
        //解析浏览器
        //华为浏览器
        //华为浏览器不管是不是Chrome内核，都会因为UA包含Chrome被识别成Chrome浏览器，所以提到最前识别。
        if (preg_match('#HuaweiBrowser/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'HuaweiBrowser ' . $matches[1];
            //以目前（2025）的逻辑来说，HarmonyOS的UA只代表5.0以下的HarmonyOS版本，上面的华为浏览器还是安卓应用，因此算安卓版。
            if ($osName === 'HarmonyOS' || strpos($osName, 'Android') === 0) {
                $prefix = ' (Android)';
            // } elseif (strpos($osName, 'OpenHarmony') === 0) {
            //     $prefix = ' (OpenHarmony)';
            } else {
                $prefix = '';
            }
            // $browserName = '华为浏览器' . $prefix . ' ' . $matches[1];
            $browserName = 'HuaweiBrowser';
            $fontClass = "opera";
        //如果没有自定义UA，OpenHarmony上的第三方浏览器应该会包含ArkWeb版本。
        } elseif (preg_match('#ArkWeb/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'ArkWeb ' . $matches[1];
            // $browserName = '华为Ark ' . $matches[1];
            $browserName = '华为Ark ';
            $fontClass = "opera";

        //基于Chromium内核的浏览器
        } elseif (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Sogou browser';
            $browserName = '搜狗浏览器';
            $fontClass = "sogou";
        } elseif (preg_match('#360([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = '360 browser ';
            $browserName = '360浏览器';
            $fontClass = "360";
        } elseif (preg_match('#Maxthon( |\/)([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Maxthon ';
            $browserName = '遨游浏览器';
            $fontClass = "maxthon";
        } elseif (preg_match('#Edge( |\/)([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Edge ';
            $browserName = 'Microsoft Edge';
            $fontClass = "edge";
        } elseif (preg_match('#MicroMessenger/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Wechat ';
            $browserName = '微信内置浏览器';
            $fontClass = "wechat";
        } elseif (preg_match('#QQ/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'QQ Mobile ';
            $browserName = 'QQ内置浏览器';
            $fontClass = "qq";
        } elseif (preg_match('#QQBrowser ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'QQ browser ';
            $browserName = 'QQ浏览器';
            $fontClass = "qqbrowser";
        } elseif (preg_match('#UCWEB([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'UCWEB ';
            $browserName = 'UC浏览器';
            $fontClass = "uc";

        //Chrome浏览器
        } elseif (preg_match('#Chrome/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Chrome ' . $matches[1];
            // $browserName = 'Chrome ' . $matches[1];
            $browserName = 'Chrome';
            $fontClass = "chrome";
        } elseif (preg_match('#CriOS/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Chrome ' . $matches[1];
            // $browserName = 'Chrome(iOS) ' . $matches[1];
            $browserName = 'Chrome(iOS)';
            $fontClass = "chrome";
        } elseif (preg_match('#Chromium/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Chromium ' . $matches[1];
            // $browserName = 'Chromium ' . $matches[1];
            $browserName = 'Chromium';
            $fontClass = "chrome";

        //Safari浏览器
        } elseif (preg_match('#Safari/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Safari ' . $matches[1];
            // $browserName = 'Safari ' . $matches[1];
            $browserName = 'Safari';
            $fontClass = "safari";

        //Firefox浏览器
        } elseif (preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Firefox ' . $matches[2];
            // $browserName = 'Firefox ' . $matches[2];
            $browserName = 'Firefox';
            $fontClass = "firefox";

        //Opera浏览器（真的有人用吗？）
        } elseif (preg_match('#opera mini#i', $ua)) {
            preg_match('#Opera/([a-zA-Z0-9.]+)#i', $ua, $matches);
            $browser = 'Opera Mini ';
            $browserName = 'Opera Mini';
            $fontClass = "opera";
        } elseif (preg_match('#Opera.([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Opera ';
            $browserName = 'Opera';
            $fontClass = "opera";
        
        //IE浏览器
        } elseif (preg_match('#MSIE ([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Internet Explorer ';
            $browserName = 'Internet Explorer(1-10)';
            $fontClass = "ie";
        } elseif (preg_match('#Trident/([a-zA-Z0-9.]+)#i', $ua, $matches)) {
            $browser = 'Internet Explorer 11';
            $browserName = 'Internet Explorer 11';
            $fontClass = "ie";
        } else { 
            $browser = 'unknown br';
            $browserName = '未知浏览器';
            $fontClass = 'browser';
        }

        $htmlTag .= "&nbsp;";
        $htmlTag .= "<i class=\"iconfont icon-aria-$fontClass\"></i><a class=\"comment-meta\"><span>$browserName</span></a>";
        self::$parsedUserAgentCache[$cacheKey] = $htmlTag;
        return self::$parsedUserAgentCache[$cacheKey];
    }

}
