<?php
// Frontend init hooks and lightweight AJAX endpoints.

function themeInit($archive)
{
    Helper::options()->commentsMaxNestingLevels = 999;
    Helper::options()->commentsOrder = 'DESC';
    if (Utils::isEnabled('enablePjax', 'AriaConfig')) {
        Helper::options()->commentsAntiSpam = false;
    }

    // AJAX 获取评论头像，仅在明确请求该动作时响应。
    if (
        $_SERVER['REQUEST_METHOD'] === 'GET'
        && isset($_GET['action'])
        && $_GET['action'] === 'ajax_avatar_get'
    ) {
        $email = isset($_GET['email']) ? trim((string) $_GET['email']) : '';
        $hash = md5(strtolower($email));
        $avatar = __TYPECHO_GRAVATAR_PREFIX__ . $hash . '?s=64&r=G';

        echo $avatar;
        exit;
    }
}
