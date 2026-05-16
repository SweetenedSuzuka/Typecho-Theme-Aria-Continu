<?php if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}

/**
 * ThemeViewData.php
 * 主题视图数据辅助
 *
 * Based on original work by Siphils
 * @author     SweetenedSuzuka
 * @version    since 1.13.1
 */
class ThemeViewData
{
    /**
     * 获取评论展示视图数据
     *
     * @return array
     */
    public static function getCommentsViewData()
    {
        $mathJaxEnabled = ThemeOptions::isFeatureEnabled('enableMathJax', 'AriaConfig');
        $mathJaxEnabledInComments = ThemeOptions::isFeatureEnabled('enableMathJaxInComments', 'AriaConfig');
        $options = Helper::options();
        $commentsRequireMail = !empty($options->commentsRequireMail);
        $commentsRequireUrl = !empty($options->commentsRequireURL);
        $commentsMarkdown = !empty($options->commentsMarkdown);
        $allowedHtmlTags = isset($options->commentsHTMLTagAllowed)
            ? (string) $options->commentsHTMLTagAllowed
            : '';

        return array(
            'ignoreMathJax' => $mathJaxEnabled && !$mathJaxEnabledInComments,
            'waitingText' => ThemeOptions::getOptionStringValue(
                'commentWaitingText',
                '正在思考这条评论和不和谐.jpg（评论正在等待审核）',
                false
            ),
            'closedText' => ThemeOptions::getOptionStringValue('commentClosedText', '评论关闭了哟', false),
            'showUserAgent' => ThemeOptions::isEnabled('showCommentUA', 'AriaConfig'),
            'form' => array(
                'className' => self::getCommentFormClassName(),
                'style' => self::getCommentFormStyle(),
                'newResponseText' => '添加新评论',
                'requireMail' => $commentsRequireMail,
                'requireUrl' => $commentsRequireUrl,
                'supportsMarkdown' => $commentsMarkdown,
                'markdownGuideUrl' => 'https://guides.github.com/features/mastering-markdown/',
                'markdownHintText' => '评论可以使用 Markdown 语法',
                'supportsImageInsertion' => $commentsMarkdown
                    && $allowedHtmlTags !== ''
                    && strpos($allowedHtmlTags, 'img') !== false,
                'imageInsertText' => '图片',
                'showCommentToMail' => ThemeOptions::isEnabled('enableCommentToMail', 'AriaConfig'),
                'banMailStrongText' => '不接收',
                'banMailLabelText' => '回复邮件通知',
                'guestAvatarPrefix' => __TYPECHO_GRAVATAR_PREFIX__,
                'nicknamePlaceholder' => '（必填）昵称',
                'mailPlaceholder' => ($commentsRequireMail ? '（必填）' : '（选填）') . '邮箱',
                'urlPlaceholder' => ($commentsRequireUrl ? '（必填）' : '（选填）') . '网站',
                'textPlaceholder' => isset($options->placeholder)
                    ? (string) $options->placeholder
                    : '',
                'submitText' => '投送',
            ),
        );
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
        $isPostContext = $context === 'post';

        return array(
            'meta' => array(
                'showCategory' => $isPostContext,
                'categorySeparator' => ' • ',
                'viewsSuffix' => '次阅读',
            ),
            'showTags' => $isPostContext,
            'showNextPrev' => $isPostContext,
            'showToc' => !empty($archive->fields->showTOC),
        );
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
        $isArchiveContext = $context === 'archive';

        return array(
            'thumbnailUrl' => self::getPostCardThumbnailUrl($archive),
            'loadingImageUrl' => ThemeAssetHelper::getThemeAssetUrl('assets/img/loading.svg'),
            'categorySeparator' => $isArchiveContext ? ' ' : ' • ',
            'useLazyload' => !$isArchiveContext && ThemeOptions::isEnabled('enableLazyload', 'AriaConfig'),
            'showLine' => !$isArchiveContext,
            'moreTitle' => $isArchiveContext ? '' : 'Read More',
        );
    }

    /**
     * 获取评论表单 class
     *
     * @return string
     */
    private static function getCommentFormClassName()
    {
        return ThemeOptions::isOptionEnabled('customCommentBoxBackgroundEnabled', false)
            && ThemeAssetHelper::getCustomCommentBoxBackgroundUrl() !== ''
            ? 'comment-form--custom-background'
            : '';
    }

    /**
     * 获取评论表单样式
     *
     * @return string
     */
    private static function getCommentFormStyle()
    {
        $backgroundUrl = ThemeAssetHelper::getCustomCommentBoxBackgroundUrl();
        if (!ThemeOptions::isOptionEnabled('customCommentBoxBackgroundEnabled', false) || $backgroundUrl === '') {
            return '';
        }

        return sprintf(
            "--aria-comment-box-bg: url('%s');",
            str_replace(
                array('\\', "'"),
                array('\\\\', "\\'"),
                $backgroundUrl
            )
        );
    }

    /**
     * 获取文章卡片缩略图 URL
     *
     * @param Widget_Archive $archive
     *
     * @return string
     */
    private static function getPostCardThumbnailUrl($archive)
    {
        ob_start();
        if ($archive->fields->thumbnail) {
            $archive->fields->thumbnail();
        } else {
            echo ThemeAssetHelper::getThumbnail();
        }

        return trim(ob_get_clean());
    }
}
