<?php
// Theme admin configuration form registration.

require_once __DIR__ . '/theme-config-ui.php';
require_once __DIR__ . '/theme-config-transfer.php';

function ariaThemeToggleValue($enabled)
{
    return $enabled ? array('1') : array();
}

function ariaGetThemeConfigStoredIssues()
{
    $definitions = array(
        'navConfig' => array(
            'label' => '导航栏配置',
            'validator' => 'validateNavConfigInput',
            'message' => '当前已保存的内容格式无效，请改为完整 JSON 数组后重新保存。',
        ),
        'rewardConfig' => array(
            'label' => '打赏功能配置',
            'validator' => 'validateRewardConfigInput',
            'message' => '当前已保存的内容格式无效，请改为合法 JSON 对象或旧格式片段后重新保存。',
        ),
        'footerRecords' => array(
            'label' => '页脚备案信息',
            'validator' => 'validateFooterRecordsInput',
            'message' => '当前已保存的内容格式无效，请改为完整 JSON 数组后重新保存。',
        ),
        'footerWidget' => array(
            'label' => '底部额外链接组件',
            'validator' => 'validateFooterWidgetInput',
            'message' => '当前已保存的内容格式无效，请改为完整 JSON 数组后重新保存。',
        ),
    );

    $issues = array();
    $options = Helper::options();

    foreach ($definitions as $name => $definition) {
        $rawValue = isset($options->$name) ? trim((string) $options->$name) : '';
        if ($rawValue === '') {
            continue;
        }

        $validator = array('ThemeOptions', $definition['validator']);
        if (!is_callable($validator) || call_user_func($validator, $rawValue)) {
            continue;
        }

        $issues[$name] = array(
            'label' => $definition['label'],
            'message' => $definition['message'],
        );
    }

    return $issues;
}

function ariaAttachStoredIssueMessage($element, $name, array $issues)
{
    if (!isset($issues[$name]['message'])) {
        return $element;
    }

    $element->setAttribute('data-aria-stored-issue', (string) $issues[$name]['message']);

    return $element;
}

function themeConfig($form)
{
    $storedIssues = ariaGetThemeConfigStoredIssues();
    ariaRenderThemeConfigAssets();
    ariaRenderThemeConfigIssuesPanel($storedIssues);
    ariaRenderThemeConfigIntro();

    $themeConfigSchemaVersion = new Typecho_Widget_Helper_Form_Element_Text(
        'themeConfigSchemaVersion',
        null,
        ThemeOptions::getThemeConfigSchemaVersion(),
        '',
        ''
    );
    $themeConfigSchemaVersion->input->setAttribute('type', 'hidden');
    $form->addInput($themeConfigSchemaVersion);

    // Site identity and hero.
    $avatarUrl = new Typecho_Widget_Helper_Form_Element_Text('avatarUrl', null, null, _t('站点头像'), _t('在这里填入一个图片URL地址, 以在网站标题前加上一个头像,需要带上http(s)://'));
    $form->addInput($avatarUrl);

    $coverUrl = new Typecho_Widget_Helper_Form_Element_Textarea(
        'coverUrl',
        null,
        ThemeOptions::getCoverConfigValue(),
        _t('首页背景图片'),
        _t('用于首页与归档页头封面；支持填写 http(s):// 地址或站点绝对路径，每行一个 URL，随机展示；留空时使用主题自带封面图')
    );
    $form->addInput($coverUrl);

    $customPageBackgroundEnabled = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'customPageBackgroundEnabled',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::getCheckboxOptionState('customPageBackgroundEnabled', false)),
        _t('启用网页背景自定义'),
        _t('设置整个网页的背景图，关闭后将会使用Aria默认的纯色样式')
    );
    $form->addInput($customPageBackgroundEnabled->multiMode());

    $customPageBackgroundUrl = new Typecho_Widget_Helper_Form_Element_Text(
        'customPageBackgroundUrl',
        null,
        '/assets/img/background.webp',
        _t('网页背景图地址'),
        _t('支持完整 URL，或相对于当前主题目录的路径，例如 /assets/img/background.webp；关闭网页背景图开关时不会生效')
    );
    $form->addInput($customPageBackgroundUrl);

    $customCommentBoxBackgroundEnabled = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'customCommentBoxBackgroundEnabled',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::getCheckboxOptionState('customCommentBoxBackgroundEnabled', false)),
        _t('启用评论框背景自定义'),
        _t('控制是否为评论输入框右下角显示自定义背景图；关闭时保持原版样式')
    );
    $form->addInput($customCommentBoxBackgroundEnabled->multiMode());

    $customCommentBoxBackgroundUrl = new Typecho_Widget_Helper_Form_Element_Text(
        'customCommentBoxBackgroundUrl',
        null,
        '',
        _t('评论框背景图地址'),
        _t('支持完整 URL，或相对于当前主题目录的路径；图片会显示在评论输入框右下角，建议使用透明背景图片；留空则不显示背景图')
    );
    $form->addInput($customCommentBoxBackgroundUrl);

    $heroSubtitle = new Typecho_Widget_Helper_Form_Element_Text('heroSubtitle', null, '越过喧嚣找到你', _t('首页副标题'), _t('显示在首页标题下方；优先级：主题设置 > Typecho 站点描述；留空则回退为站点描述（两者都为空则不输出）；站点描述就是网页标题的“网站名 - 站点描述”的后半部分'));
    $form->addInput($heroSubtitle);

    $searchPlaceholder = new Typecho_Widget_Helper_Form_Element_Text('searchPlaceholder', null, '要看书架吗？请吧', _t('搜索框 placeholder'), _t('搜索框输入提示文本；留空可不显示 placeholder'));
    $form->addInput($searchPlaceholder);

    $defaultThumbnail = new Typecho_Widget_Helper_Form_Element_Textarea('defaultThumbnail', null, null, _t('默认文章缩略图'), _t('填入默认的缩略图地址，未设置缩略图字段时调用此地址，需要带http(s)://，每一行写一个URL，随机展示'));
    $form->addInput($defaultThumbnail);

    $notFoundBackgroundUrl = new Typecho_Widget_Helper_Form_Element_Text(
        'notFoundBackgroundUrl',
        null,
        null,
        _t('404 背景图 URL'),
        _t('留空时使用主题自带的 404 图片；如需自定义，请填写完整的 http(s):// 地址')
    );
    $form->addInput($notFoundBackgroundUrl);

    // Navigation and list pages.
    $navConfig = new Typecho_Widget_Helper_Form_Element_Textarea('navConfig', null,
        ThemeOptions::getDefaultNavConfigExample(),
        _t('导航栏配置'),
        _t('输入导航栏的配置信息；只支持完整 JSON 数组格式。`text` 中如需换行，请使用 `[[br]]` 作为换行标记。该标记仅在导航栏文本中生效，不支持任意 HTML。默认使用主题自带 `iconfont icon-aria-*`；如果已启用附加图标包，也可以直接填写 `ri-*` 或 `bi bi-*` 类名。')
    );
    ariaAttachStoredIssueMessage($navConfig, 'navConfig', $storedIssues);
    $form->addInput($navConfig);

    $iconPacks = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'iconPacks',
        array(
            'remixicon' => _t('Remix Icon（v4.9.1）'),
            'bootstrap-icons' => _t('Bootstrap Icons（v1.13.1）'),
            'font-awesome' => _t('Font Awesome Free（v7.2.0；可兼容旧版 fa 写法）'),
        ),
        ThemeOptions::getOptionArrayValues('iconPacks'),
        _t('附加图标包'),
        _t('默认仅加载主题自带 `iconfont`。按需勾选需要额外启用的图标包；未勾选的图标包不会进入前台装载链。当前内置并本地化提供 `Remix Icon`、`Bootstrap Icons` 与 `Font Awesome Free`。你可以在导航栏配置等需要填写 icon class 的地方使用，也可以在任意可自定义 HTML 的位置使用。')
    );
    $form->addInput($iconPacks->multiMode());

    $enableNavHeadroom = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableNavHeadroom',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::getCheckboxOptionState('enableNavHeadroom', true)),
        _t('启用导航栏吸顶隐藏'),
        _t('控制导航栏是否在向下滚动时自动收起、向上滚动时重新显示；关闭后导航栏将始终保持显示')
    );
    $form->addInput($enableNavHeadroom->multiMode());

    $homeExcludeCategoriesEnabled = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'homeExcludeCategoriesEnabled',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::getCheckboxOptionState('homeExcludeCategoriesEnabled', true)),
        _t('启用首页分类排除'),
        _t('控制“首页排除分类（slug）”是否生效；关闭时仍会保留配置内容但不会执行过滤')
    );
    $form->addInput($homeExcludeCategoriesEnabled->multiMode());

    $homeExcludeCategories = new Typecho_Widget_Helper_Form_Element_Textarea(
        'homeExcludeCategories',
        null,
        '',
        _t('首页排除分类（slug）'),
        _t('首页文章列表中需要排除的分类 slug；每行一个，或用空格/逗号分隔；留空则不排除任何分类')
    );
    $form->addInput($homeExcludeCategories);

    // Content and page enhancement.
    $rewardConfig = new Typecho_Widget_Helper_Form_Element_Textarea('rewardConfig', null, null
        , _t('打赏功能配置'), _t('支持旧的 JSON 片段格式，也支持完整 JSON 对象格式；留空关闭打赏功能'));
    ariaAttachStoredIssueMessage($rewardConfig, 'rewardConfig', $storedIssues);
    $form->addInput($rewardConfig);

    $showQRCode = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'showQRCode',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isPostQrCodeEnabled()),
        _t('文章底部显示本文链接二维码'),
        _t('控制文章底部是否显示当前文章链接二维码；关闭后仅保留打赏入口')
    );
    $form->addInput($showQRCode->multiMode());

    $enableFancybox = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableFancybox',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isFancyboxEnabled()),
        _t('文章/评论图片启用 Fancybox'),
        _t('控制文章与评论中的图片是否启用 Fancybox 查看器')
    );
    $form->addInput($enableFancybox->multiMode());

    // Lazyload settings.
    $enableLazyload = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableLazyload',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isLazyloadEnabled()),
        _t('开启图片懒加载'),
        _t('控制主题图片是否使用懒加载')
    );
    $form->addInput($enableLazyload->multiMode());

    $lazyloadPlaceholderEnabled = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'lazyloadPlaceholderEnabled',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::getCheckboxOptionState('lazyloadPlaceholderEnabled', false)),
        _t('懒加载完成前显示占位图'),
        _t('开启后，在懒加载完成前会显示占位用的加载图，这个样式来自Aria原版，不开启时默认是空白填充；无论开启与否，如果加载失败都会显示占位图')
    );
    $form->addInput($lazyloadPlaceholderEnabled->multiMode());

    // MathJax settings.
    $enableMathJax = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableMathJax',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isMathJaxEnabled()),
        _t('启用 MathJax'),
        _t('控制是否加载 MathJax；关闭后将同时隐藏评论区解析开关和 MathJax 配置')
    );
    $form->addInput($enableMathJax->multiMode());

    $enableMathJaxInComments = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableMathJaxInComments',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isMathJaxInCommentsEnabled()),
        _t('评论区启用 MathJax 解析'),
        _t('仅在启用 MathJax 时生效；关闭后评论区会跳过公式解析')
    );
    $form->addInput($enableMathJaxInComments->multiMode());

    $MathJaxConfig = new Typecho_Widget_Helper_Form_Element_Textarea('MathJaxConfig', null, "MathJax = MathJax || {};
MathJax.tex = MathJax.tex || {};
MathJax.tex.inlineMath = [['$', '$'], ['\\\\(', '\\\\)']];
MathJax.tex.displayMath = [['$$', '$$'], ['\\\\[', '\\\\]']];
MathJax.tex.processEscapes = true;", _t('MathJax配置信息'), _t('在此输入 MathJax 配置 JS（不需要 script 标签）。推荐使用对 MathJax 对象的增量配置；也兼容旧写法 MathJax.Hub.Config({...})。'));
    $form->addInput($MathJaxConfig);

    // Hitokoto settings.
    $showHitokoto = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'showHitokoto',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isHitokotoEnabled()),
        _t('显示一言'),
        _t('控制页脚是否显示一言；开启后才会显示自定义接口地址设置')
    );
    $form->addInput($showHitokoto->multiMode());

    $hitokotoOrgin = new Typecho_Widget_Helper_Form_Element_Text('hitokotoOrigin', null, null, _t('自定义「一言」接口地址'), _t('填入接口地址，注意接口需要是只返回一条句子的。如需使用请在开关设置内开启「一言」的显示。留空使用默认接口。如果不知道什么意思留空即可。'));
    $form->addInput($hitokotoOrgin);

    // Comment experience.
    $placeholder = new Typecho_Widget_Helper_Form_Element_Text('placeholder', null, null, _t('评论框placeholder'), _t('这里的内容会提前显示在评论框里'));
    $form->addInput($placeholder);

    $commentWaitingText = new Typecho_Widget_Helper_Form_Element_Text(
        'commentWaitingText',
        null,
        '正在思考这条评论和不和谐.jpg（评论正在等待审核）',
        _t('评论审核提示文案'),
        _t('评论处于等待审核状态时显示；留空可不显示提示')
    );
    $form->addInput($commentWaitingText);

    $commentClosedText = new Typecho_Widget_Helper_Form_Element_Text(
        'commentClosedText',
        null,
        '评论关闭了哟',
        _t('评论关闭提示文案'),
        _t('文章/页面关闭评论时显示；留空可不显示提示')
    );
    $form->addInput($commentClosedText);

    $OwOJson = new Typecho_Widget_Helper_Form_Element_Text('OwOJson', null, null, _t('OwO'), _t('OwO表情JSON文件的URL'));
    $form->addInput($OwOJson);

    $gravatarPrefix = new Typecho_Widget_Helper_Form_Element_Text('gravatarPrefix', null, null, _t('Gravatar头像源'), _t('留空为https://cn.gravatar.com/avatar/，按照前面的url地址格式填写'));
    $form->addInput($gravatarPrefix);

    $enableAjaxComment = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableAjaxComment',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isAjaxCommentEnabled()),
        _t('开启 AJAX 评论'),
        _t('控制评论提交是否使用前台异步提交；关闭后回退为普通表单提交')
    );
    $form->addInput($enableAjaxComment->multiMode());

    $enableCommentToMail = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableCommentToMail',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isCommentToMailEnabled()),
        _t('显示评论邮件通知选项'),
        _t('Aria遗留功能：控制评论表单中是否显示“回复邮件通知”勾选项；需要站点端已正确接入相关邮件通知能力；需要CommentToMail插件（插件下载地址已失效）')
    );
    $form->addInput($enableCommentToMail->multiMode());

    $showCommentUA = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'showCommentUA',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isCommentUserAgentEnabled()),
        _t('评论显示 UserAgent'),
        _t('控制评论区是否显示访客使用的操作系统与浏览器信息')
    );
    $form->addInput($showCommentUA->multiMode());

    // Footer and copyright.
    $footerSiteName = new Typecho_Widget_Helper_Form_Element_Text('footerSiteName', null, '网站名称', _t('页脚站点名称'), _t('页脚版权行中显示的站点名称'));
    $form->addInput($footerSiteName);

    $footerSiteUrl = new Typecho_Widget_Helper_Form_Element_Text('footerSiteUrl', null, 'https://example.com/', _t('页脚站点链接'), _t('页脚版权行中站点名称对应的链接地址，需要带上http(s)://'));
    $form->addInput($footerSiteUrl);

    $footerCreditsMode = new Typecho_Widget_Helper_Form_Element_Select(
        'footerCreditsMode',
        array(
            'original' => _t('Aria署名'),
            'Continuo' => _t('Aria Continuo署名'),
            'custom' => _t('自定义署名'),
            'hidden' => _t('隐藏署名'),
        ),
        'Continuo',
        _t('页脚署名模式'),
        _t('控制页脚署名信息的显示方式')
    );
    $form->addInput($footerCreditsMode);

    $footerCreditsText = new Typecho_Widget_Helper_Form_Element_Text('footerCreditsText', null, '用户自定义内容', _t('页脚署名文本'), _t('当署名模式为“自定义署名”时显示；前缀会固定显示为 Aria Continuo'));
    $form->addInput($footerCreditsText);

    $footerCreditsLink = new Typecho_Widget_Helper_Form_Element_Text('footerCreditsLink', null, null, _t('页脚署名链接'), _t('当署名模式为“自定义署名”时使用，需要带上http(s)://；留空则仅显示文本'));
    $form->addInput($footerCreditsLink);

    $footerRecordsEnabled = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'footerRecordsEnabled',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::getCheckboxOptionState('footerRecordsEnabled', true)),
        _t('显示页脚备案信息'),
        _t('控制页脚备案信息是否显示')
    );
    $form->addInput($footerRecordsEnabled->multiMode());

    $footerRecords = new Typecho_Widget_Helper_Form_Element_Textarea(
        'footerRecords',
        null,
        ThemeOptions::getDefaultFooterRecordsExample(),
        _t('页脚备案信息'),
        _t('支持完整 JSON 数组格式；每项可包含 text、url、icon、title；留空则不显示备案信息')
    );
    ariaAttachStoredIssueMessage($footerRecords, 'footerRecords', $storedIssues);
    $form->addInput($footerRecords);

    $footerWidget = new Typecho_Widget_Helper_Form_Element_Textarea('footerWidget', null, null, _t('底部额外链接组件'), _t('支持完整 JSON 数组格式；每项可包含 text、href、title、target、icon；留空则不显示额外链接'));
    ariaAttachStoredIssueMessage($footerWidget, 'footerWidget', $storedIssues);
    $form->addInput($footerWidget);

    $cpr = new Typecho_Widget_Helper_Form_Element_Text('cpr', null, '2022-{Y}', _t('Copyright年份'), _t('支持静态文本，也支持 {Y} / {y} / {year} 动态年份占位符，例如 2022-{Y}；留空时默认使用 2022-{Y}。<del>当然你想填什么都可以</del>'));
    $form->addInput($cpr);

    // Special pages.
    $notFoundTitle = new Typecho_Widget_Helper_Form_Element_Text(
        'notFoundTitle',
        null,
        '404:没有找到界面呢，是书架摆错了吗？',
        _t('404 标题文案'),
        _t('404 页面标题（H2）；留空则使用默认标题')
    );
    $form->addInput($notFoundTitle);

    $notFoundDescription = new Typecho_Widget_Helper_Form_Element_Textarea(
        'notFoundDescription',
        null,
        '这个页面不存在或者被删除，你可以尝试搜索你想要的内容。',
        _t('404 描述文案'),
        _t('404 页面描述文本；留空则使用默认描述')
    );
    $form->addInput($notFoundDescription);

    // Advanced injection and analytics.
    $statistics = new Typecho_Widget_Helper_Form_Element_Textarea('statistics', null, null, _t('统计代码'), _t('在此填入统计代码；若使用依赖前端局部刷新的统计方案，请自行确认其在当前主题中的计数行为是否符合预期'));
    $form->addInput($statistics);

    $enableAdvancedCustomCode = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableAdvancedCustomCode',
        array('1' => _t('开启')),
        ariaThemeToggleValue(ThemeOptions::isAdvancedCustomCodeEnabled()),
        _t('启用高级自定义代码'),
        _t('控制“顶部自定义内容”“底部自定义内容”“自定义JS”是否参与输出与执行。仅在你明确知道自己在做什么时再开启，因为这里填写的 HTML / JS 会按原样输出或执行；关闭后会保留已填写内容，但不会继续生效。')
    );
    $form->addInput($enableAdvancedCustomCode->multiMode());

    $customHeader = new Typecho_Widget_Helper_Form_Element_Textarea('customHeader', null, null, _t('顶部自定义内容'), _t('会加载在<strong>head</strong>结束标签之前'));
    $customHeader->setAttribute('data-aria-advanced-custom-code', '1');
    $form->addInput($customHeader);

    $customFooter = new Typecho_Widget_Helper_Form_Element_Textarea('customFooter', null, null, _t('底部自定义内容'), _t('会加载在页脚内容区域的最前面'));
    $customFooter->setAttribute('data-aria-advanced-custom-code', '1');
    $form->addInput($customFooter);

    $customScript = new Typecho_Widget_Helper_Form_Element_Textarea('customScript', null, null, _t('自定义JS'), _t('会加载在主题主脚本 `main.js` 文件加载之前'));
    $customScript->setAttribute('data-aria-advanced-custom-code', '1');
    $form->addInput($customScript);

    ariaRenderThemeConfigTransferScript(ariaGetThemeConfigTransferSchema());

}
