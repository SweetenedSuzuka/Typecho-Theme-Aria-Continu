<?php
// Theme admin configuration form registration.

require_once __DIR__ . '/theme-config-ui.php';

function ariaThemeToggleValue($enabled)
{
    return $enabled ? array('1') : array();
}

function themeConfig($form)
{
    ariaRenderThemeConfigAssets();
    ariaRenderThemeConfigIntro();

    // Site identity and hero.
    $avatarUrl = new Typecho_Widget_Helper_Form_Element_Text('avatarUrl', null, null, _t('站点头像'), _t('在这里填入一个图片URL地址, 以在网站标题前加上一个头像,需要带上http(s)://'));
    $form->addInput($avatarUrl);

    $backgroundUrl = new Typecho_Widget_Helper_Form_Element_Textarea('backgroundUrl', null, null, _t('首页背景图片'), _t('需要输入http(s)://，每一行写一个URL，随机展示'));
    $form->addInput($backgroundUrl);

    $heroSubtitle = new Typecho_Widget_Helper_Form_Element_Text('heroSubtitle', null, '越过喧嚣找到你', _t('首页副标题'), _t('显示在首页标题下方；优先级：主题设置 > Typecho 站点描述；留空则回退为站点描述（两者都为空则不输出）'));
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
        '{
            "text":"首页",
            "href":"' . Helper::options()->siteUrl . '",
            "icon":"iconfont icon-aria-home"
        },
        {
            "text":"归档",
            "href":"#",
            "icon":"iconfont icon-aria-archives"
        },
        {
            "text":"留言",
            "href":"#",
            "icon":"iconfont icon-aria-guestbook"
        },
        {
            "text":"朋友",
            "href":"#",
            "icon":"iconfont icon-aria-friends"
        },
        {
            "text":"关于",
            "href":"#",
            "icon":"iconfont icon-aria-about"
        }',
        _t('导航栏配置'),
        _t('输入导航栏的配置信息；如需在 text 中换行，请使用 `[[br]]` 作为换行标记。该标记仅在导航栏文本中生效，不支持任意 HTML。')
    );
    $form->addInput($navConfig);

    $homeExcludeCategoriesEnabled = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'homeExcludeCategoriesEnabled',
        array('1' => _t('开启')),
        ariaThemeToggleValue(Utils::isOptionEnabled('homeExcludeCategoriesEnabled', true)),
        _t('启用首页分类排除'),
        _t('控制“首页排除分类（slug）”是否生效；关闭时仍会保留配置内容但不会执行过滤')
    );
    $form->addInput($homeExcludeCategoriesEnabled->multiMode());

    $homeExcludeCategories = new Typecho_Widget_Helper_Form_Element_Textarea(
        'homeExcludeCategories',
        null,
        'negawakubakonotenikoufukuwo',
        _t('首页排除分类（slug）'),
        _t('首页文章列表中需要排除的分类 slug；每行一个，或用空格/逗号分隔；留空则不排除任何分类')
    );
    $form->addInput($homeExcludeCategories);

    // Content and page enhancement.
    $rewardConfig = new Typecho_Widget_Helper_Form_Element_Textarea('rewardConfig', null, null
        , _t('打赏功能配置'), _t('按照格式填写,留空关闭打赏功能'));
    $form->addInput($rewardConfig);

    // MathJax settings.
    $enableMathJax = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableMathJax',
        array('1' => _t('开启')),
        ariaThemeToggleValue(Utils::isFeatureEnabled('enableMathJax', 'AriaConfig')),
        _t('启用 MathJax'),
        _t('控制是否加载 MathJax；关闭后将同时隐藏评论区解析开关和 MathJax 配置')
    );
    $form->addInput($enableMathJax->multiMode());

    $enableMathJaxInComments = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'enableMathJaxInComments',
        array('1' => _t('开启')),
        ariaThemeToggleValue(Utils::isFeatureEnabled('enableMathJaxInComments', 'AriaConfig')),
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
        ariaThemeToggleValue(Utils::isFeatureEnabled('showHitokoto', 'AriaConfig')),
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
        ariaThemeToggleValue(Utils::isOptionEnabled('footerRecordsEnabled', true)),
        _t('显示页脚备案信息'),
        _t('控制页脚备案信息是否显示；关闭时仅保存不显示')
    );
    $form->addInput($footerRecordsEnabled->multiMode());

    $footerRecords = new Typecho_Widget_Helper_Form_Element_Textarea(
        'footerRecords',
        null,
        '{
            "text":"ICP备00000000号-0",
            "url":"https://beian.miit.gov.cn/",
            "icon":"",
            "title":"ICP备案信息"
        },
        {
            "text":"公网安备 00000000000000号",
            "url":"http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=00000000000000",
            "icon":"",
            "title":"公网安备信息"
        }',
        _t('页脚备案信息'),
        _t('按原有 JSON 片段格式填写，每项可包含 text、url、icon、title')
    );
    $form->addInput($footerRecords);

    $footerWidget = new Typecho_Widget_Helper_Form_Element_Textarea('footerWidget', null, null, _t('底部额外链接组件'), _t('填入 JSON 片段，不需要最外层包裹，忘记格式可以全部删掉并保存，然后会恢复默认，照着填即可；至少填一个，可以无限增加；如果一个都不想填，请直接关闭开关'));
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
    $statistics = new Typecho_Widget_Helper_Form_Element_Textarea('statistics', null, null, _t('统计代码'), _t('在此填入统计的代码(目前统计代码支持谷歌统计和百度统计的重载，若使用其他统计请关闭PJAX否则得到的统计数据不准确)'));
    $form->addInput($statistics);

    $customHeader = new Typecho_Widget_Helper_Form_Element_Textarea('customHeader', null, null, _t('顶部自定义内容'), _t('会加载在<strong>head</strong>结束标签之前'));
    $form->addInput($customHeader);

    $customFooter = new Typecho_Widget_Helper_Form_Element_Textarea('customFooter', null, null, _t('底部自定义内容'), _t('会加载在<strong>copyright</strong>之前'));
    $form->addInput($customFooter);

    $customScript = new Typecho_Widget_Helper_Form_Element_Textarea('customScript', null, null, _t('自定义JS'), _t('会加载在主题主脚本 `main.js` 文件加载之前'));
    $form->addInput($customScript);

    // Generic frontend switches.
    $AriaConfig = new Typecho_Widget_Helper_Form_Element_Checkbox('AriaConfig',
        array(
            'enablePjax' => '开启PJAX（启用后会强制关闭评论反垃圾保护）',
            'enableAjaxComment' => '开启AJAX评论',
            'enableFancybox' => '文章/评论图片使用<a href="http://fancyapps.com" target="_blank">fancybox</a>',
            'enableLazyload' => '开启图片懒加载<a href="https://appelsiini.net/projects/lazyload" target="_blank">lazyload</a>',
            'enableCommentToMail' => '是否接收评论邮件回复按钮，需要配合<a href="https://9sb.org/58">CommentToMail</a>（文章中最后一个插件)使用',
            'showQRCode' => '文章底部显示本文链接二维码',
            'showCommentUA' => '评论显示UserAgent（显示操作系统和浏览器信息）',
        ),
        null,
        '开关设置'
    );
    $form->addInput($AriaConfig->multiMode());

}
