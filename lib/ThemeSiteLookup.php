<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeSiteLookup.php
 * 主题站点查询辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.17.1
 */
class ThemeSiteLookup
{
    /**
     * 获取第一管理员的头像 URL
     *
     * @param int $size
     *
     * @return string
     */
    public static function getAdminAvatarUrl($size = 50)
    {
        static $adminMail = null;

        $options = Helper::options();
        $avatarUrl = trim((string) $options->avatarUrl);
        if ($avatarUrl !== '') {
            return $avatarUrl;
        }

        if ($adminMail === null) {
            $db = Typecho_Db::get();
            $admin = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', 1));
            $adminMail = is_array($admin) && array_key_exists('mail', $admin)
                ? trim((string) $admin['mail'])
                : '';
        }

        return __TYPECHO_GRAVATAR_PREFIX__
            . md5(strtolower($adminMail))
            . '?d=mp&r=g&s='
            . (int) $size;
    }

    /**
     * 获取所有页面的信息，根据 slug 构造键值对数组
     *
     * @return array|bool
     */
    public static function getPagesInfo()
    {
        static $pagesInfo = null;
        static $contentsWidget = null;

        if ($pagesInfo !== null) {
            return $pagesInfo;
        }

        $db = Typecho_Db::get();
        $query = $db->select()->from('table.contents')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', 'page')
            ->where('table.contents.password IS NULL');

        $_contents = $db->fetchAll($query);
        if (!$_contents) {
            $pagesInfo = false;
            return $pagesInfo;
        }

        $contents = array();
        if ($contentsWidget === null) {
            $contentsWidget = Typecho_Widget::widget('Widget_Abstract_Contents');
        }

        foreach ($_contents as $val) {
            $val = $contentsWidget->push($val);
            $contents[$val['slug']] = array(
                'title' => $val['title'],
                'permarlink' => $val['permalink'],
            );
        }

        $pagesInfo = $contents;
        return $pagesInfo;
    }
}
