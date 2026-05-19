<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<div id="<?php $this->respondId(); ?>" class="respond<?php if ($commentsViewData['form']['className'] !== ''): ?> <?php echo htmlspecialchars($commentsViewData['form']['className'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>"<?php if ($commentsViewData['form']['style'] !== ''): ?> style="<?php echo htmlspecialchars($commentsViewData['form']['style'], ENT_QUOTES, 'UTF-8'); ?>"<?php endif; ?>>
    <div class="cancel-comment-reply">
        <a id="cancel-comment-reply-link" href="#comment-form" data-aria-action="cancel-comment-reply" style="display:none">
            <i class="iconfont icon-aria-cancel"></i>
        </a>
    </div>

    <span id="new-response">
        <i class="iconfont icon-aria-write"></i> <?php echo htmlspecialchars($commentsViewData['form']['newResponseText'], ENT_QUOTES, 'UTF-8'); ?> </span>
    <!-- New Comments begin -->
    <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form"
     role="form">
        <?php if($this->user->hasLogin()): ?>
        <p>
            <?php _e('登录身份: '); ?>
            <a href="<?php $this->options->profileUrl(); ?>">
                <?php $this->user->screenName(); ?>
            </a>.
            <a href="<?php $this->options->logoutUrl(); ?>" title="Logout">
                <?php _e('退出'); ?>&raquo;</a>
        </p>
        <?php else: ?>
        <div id="comment-info">
            <p>
                <img no-lazyload id="comment-avatar" src="<?php echo htmlspecialchars($commentsViewData['form']['guestAvatarPrefix'], ENT_QUOTES, 'UTF-8'); ?>">
            </p>
            <p class="comment-input">
                <label for="author" class="required">
                    <i class="iconfont icon-aria-username"></i>
                </label>
                <input placeholder="<?php echo htmlspecialchars($commentsViewData['form']['nicknamePlaceholder'], ENT_QUOTES, 'UTF-8'); ?>" type="text" name="author" id="author" class="text" value="<?php $this->remember('author'); ?>"
                 required />
            </p>
            <p class="comment-input">
                <label for="mail" <?php if (!empty($commentsViewData['form']['requireMail'])): ?> class="required"<?php endif; ?>>
                    <i class="iconfont icon-aria-email"></i>
                </label>
                <input placeholder="<?php echo htmlspecialchars($commentsViewData['form']['mailPlaceholder'], ENT_QUOTES, 'UTF-8'); ?>" type="email" name="mail" id="mail" class="text" value="<?php $this->remember('mail'); ?>"
                 <?php if (!empty($commentsViewData['form']['requireMail'])): ?> required<?php endif; ?>>
            </p>
            <p class="comment-input">
                <label for="url" <?php if (!empty($commentsViewData['form']['requireUrl'])): ?> class="required"<?php endif; ?>>
                    <i class="iconfont icon-aria-link"></i>
                </label>
                <input type="url" name="url" id="url" class="text" placeholder="<?php echo htmlspecialchars($commentsViewData['form']['urlPlaceholder'], ENT_QUOTES, 'UTF-8'); ?>"
                 value="<?php $this->remember('url'); ?>" <?php
                 if (!empty($commentsViewData['form']['requireUrl'])): ?> required
                <?php endif; ?>/>
            </p>
        </div>
        <?php endif; ?>
        <?php if (!empty($commentsViewData['form']['supportsMarkdown'])): ?>
            <div style="float:right">
                <a href="<?php echo htmlspecialchars($commentsViewData['form']['markdownGuideUrl'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">
                    <i class="iconfont icon-aria-markdown"></i><span style="font-size:13px;color:#444"> <?php echo htmlspecialchars($commentsViewData['form']['markdownHintText'], ENT_QUOTES, 'UTF-8'); ?> </span>
                    <!--取消斜体font-style:italic;并改为中文提示 -->
                </a>
                <!-- 加入超链接 -->
            </div>
        <?php endif; ?>
        <p>
            <label for="textarea" class="required"></label>
            <textarea rows="8" cols="50" name="text" id="textarea" class="textarea" placeholder="<?php echo htmlspecialchars($commentsViewData['form']['textPlaceholder'], ENT_QUOTES, 'UTF-8'); ?>"><?php $this->remember('text'); ?></textarea>
        </p>
        <div id="comment-footer">
            <div class="OwO">
            </div><!--end .OwO-->
            <?php if (!empty($commentsViewData['form']['supportsImageInsertion'])): ?>
            <div class="comment-image" data-aria-action="insert-comment-image">
                <span><i class="iconfont icon-aria-picture"></i><?php echo htmlspecialchars($commentsViewData['form']['imageInsertText'], ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <?php endif; ?>
            <?php if (!empty($commentsViewData['form']['showCommentToMail'])): ?>
            <div id="comment-ban-mail" class="ui toggle checkbox">
                <input name="banmail" type="checkbox" value="stop">
                <label for="comment-ban-mail">
                    <strong><?php echo htmlspecialchars($commentsViewData['form']['banMailStrongText'], ENT_QUOTES, 'UTF-8'); ?></strong><?php echo htmlspecialchars($commentsViewData['form']['banMailLabelText'], ENT_QUOTES, 'UTF-8'); ?></label>
            </div>
            <?php endif; ?>
        </div>
        <center>
            <button type="submit" class="submit"><i class="iconfont icon-aria-submit"></i> <?php echo htmlspecialchars($commentsViewData['form']['submitText'], ENT_QUOTES, 'UTF-8'); ?></button>
        </center>
    </form>
</div>
