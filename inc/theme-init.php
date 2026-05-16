<?php
// Frontend init hooks and lightweight AJAX endpoints.

/**
 * 返回评论 Markdown 基础可用的标签白名单。
 *
 * 这里故意不默认放开 h1-h6 和 img：
 * - 标题标签会明显改变评论区层级结构；
 * - 图片是否允许继续尊重站点当前配置。
 *
 * @return array<int, string>
 */
function themeGetMarkdownCommentTagDefaults()
{
    return array(
        '<a href="" title="">',
        '<blockquote>',
        '<code>',
        '<pre>',
        '<strong>',
        '<em>',
        '<del>',
        '<ul>',
        '<ol>',
        '<li>',
    );
}

/**
 * 在不覆盖用户自定义配置的前提下，为评论 Markdown 补齐基础标签。
 *
 * Typecho 会在评论 Markdown 解析后再次按 commentsHTMLTagAllowed 过滤标签。
 * 若这里只保留极少数标签，Markdown 结构会被剃成接近纯文本的段落输出。
 *
 * @param string $allowed
 * @return string
 */
function themeMergeMarkdownCommentTags($allowed)
{
    $allowed = trim((string) $allowed);
    $segments = array();
    $seen = array();

    if ($allowed !== '' && preg_match_all('/<[^>]+>/', $allowed, $matches)) {
        foreach ($matches[0] as $segment) {
            $segment = trim((string) $segment);
            if ($segment === '') {
                continue;
            }

            $tagName = strtolower((string) preg_replace('/^<\s*([a-z0-9]+).*$/i', '$1', $segment));
            if ($tagName === '') {
                continue;
            }

            if (!isset($seen[$tagName])) {
                $segments[] = $segment;
                $seen[$tagName] = true;
            }
        }
    }

    foreach (themeGetMarkdownCommentTagDefaults() as $segment) {
        $tagName = strtolower((string) preg_replace('/^<\s*([a-z0-9]+).*$/i', '$1', $segment));
        if ($tagName === '' || isset($seen[$tagName])) {
            continue;
        }

        $segments[] = $segment;
        $seen[$tagName] = true;
    }

    return implode('', $segments);
}

/**
 * 当评论 Markdown 已启用时，自动补齐主题所需的基础标签白名单。
 *
 * 这是一层请求内兼容修正：
 * - 不改数据库中的系统设置；
 * - 不覆盖用户已手动允许的标签；
 * - 只补齐评论 Markdown 正常工作所需的最小集合。
 */
function themeNormalizeCommentMarkdownOptions()
{
    $options = Helper::options();
    if (empty($options->commentsMarkdown)) {
        return;
    }

    $options->commentsHTMLTagAllowed = themeMergeMarkdownCommentTags(
        isset($options->commentsHTMLTagAllowed) ? (string) $options->commentsHTMLTagAllowed : ''
    );
}

function themeInit($archive)
{
    $options = Helper::options();
    $options->commentsMaxNestingLevels = 999;
    $options->commentsOrder = 'DESC';
    themeNormalizeCommentMarkdownOptions();

    if (Utils::isEnabled('enablePjax', 'AriaConfig')) {
        $options->commentsAntiSpam = false;
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
