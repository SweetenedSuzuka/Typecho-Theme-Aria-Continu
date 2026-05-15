<?php
// Theme custom fields registration.

function themeFields($layout)
{
    $thumbnail = new Typecho_Widget_Helper_Form_Element_Text('thumbnail', null, null, _t('文章/页面缩略图Url'), _t('需要带上http(s)://'));
    $previewContent = new Typecho_Widget_Helper_Form_Element_Text('previewContent', null, null, _t('文章预览内容'), _t('设置文章的预览内容，留空自动截取文章前50个字。'));
    $showTOC = new Typecho_Widget_Helper_Form_Element_Radio('showTOC', array(true => _t('开启'), false => _t('关闭')), false, _t('文章目录'), _t('仅会解析h2和h3标题，最多解析两层'));

    $layout->addItem($thumbnail);
    $layout->addItem($previewContent);
    $layout->addItem($showTOC);
}
