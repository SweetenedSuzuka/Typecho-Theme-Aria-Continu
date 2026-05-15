<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html class="no-js">

<head>
	<meta charset="<?php $this->options->charset(); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
	<meta name="renderer" content="webkit">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="robots" content="noarchive">
    <!-- 声明禁止爬虫进行快照 -->
    <meta name="ia_archiver" content="noindex,nofollow">
    <!--Internet Archive爬虫进行快照 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
    <!-- 添加Font Awesome图标 -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="white">
    <meta name="apple-mobile-web-app-title" content="<?php $this->options->title() ?>">
	<!-- 通过自有函数输出HTML头部信息 -->
	<?php $this->header("commentReply="); ?>
	<title><?php $this->archiveTitle(array(
            'category'  =>  _t('分类 %s 下的文章'),
            'search'    =>  _t('包含关键字 %s 的文章'),
            'tag'       =>  _t('标签 %s 下的文章'),
            'author'    =>  _t('%s 发布的文章')
        ), '', ' - '); ?><?php $this->options->title(); ?> - <?php $this->options->description(); ?></title>

	<!-- 使用url函数转换相关路径 -->

    <link rel="icon" type="image/ico" href="/favicon.ico">
	<?php if(Utils::isEnabled('enableFancybox','AriaConfig')): ?>
	<link href="<?php $this->options->themeUrl('assets/css/jquery.fancybox.min.css'); ?>" rel="stylesheet">
    <?php endif; ?>
	<link href="<?php $this->options->themeUrl('assets/OwO/OwO.min.css'); ?>" rel="stylesheet">
	<link href="<?php $this->options->themeUrl('assets/css/animate.min.css'); ?>" rel="stylesheet">
    <link href="<?php $this->options->themeUrl('assets/css/iconfont.css'); ?>" rel="stylesheet" >
    <link href="<?php echo Utils::getThemeAssetUrl('assets/css/restored/base.css'); ?>" rel="stylesheet">
    <link href="<?php echo Utils::getThemeAssetUrl('assets/css/restored/layout.css'); ?>" rel="stylesheet">
    <link href="<?php echo Utils::getThemeAssetUrl('assets/css/restored/post.css'); ?>" rel="stylesheet">
    <link href="<?php echo Utils::getThemeAssetUrl('assets/css/restored/comments.css'); ?>" rel="stylesheet">
    <link href="<?php echo Utils::getThemeAssetUrl('assets/css/restored/extras.css'); ?>" rel="stylesheet">
    <link href="<?php echo Utils::getThemeAssetUrl('assets/css/pages.css'); ?>" rel="stylesheet">
    <script src="<?php $this->options->themeUrl('assets/js/jquery.min.js'); ?>"></script>
    <?php if($this->options->customHeader) $this->options->customHeader(); ?>
	<!--[if lt IE 9]>
    <script src="http://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="http://cdn.staticfile.org/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<?php Utils::AriaConfig(); ?>
<?php
$slugs = Utils::getPagesInfo();
$searchPlaceholder = Utils::hasOption('searchPlaceholder')
    ? Utils::getOptionStringValue('searchPlaceholder', '', false)
    : '要想搜索请输入关键词';
include __DIR__ . '/components/header/navigation.php';
include __DIR__ . '/components/header/search-box.php';
?>
<div id="pjax-container">
<?php
$is404Page = !empty($GLOBALS['ARIA_IS_404_PAGE']);
$isContentHeroPage = $this->is('post') || $this->is('page') || $this->is('single') || $this->is('archive');

if ($this->is('post') || $this->is('page') || $this->is('single')) {
    $headerBackgroundUrl = $this->fields->thumbnail ? $this->fields->thumbnail : Utils::getThumbnail();
} elseif ($is404Page) {
    $headerBackgroundUrl = Utils::get404BackgroundUrl();
} else {
    $headerBackgroundUrl = Utils::getBackgroundUrl();
}

if (Utils::hasOption('heroSubtitle')) {
    $heroSubtitle = Utils::getOptionStringValue('heroSubtitle', '', false);
    if ($heroSubtitle === '') {
        $heroSubtitle = Utils::getOptionStringValue('description', '', false);
    }
} else {
    $heroSubtitle = '越过喧嚣找到你';
}

$headerClassNames = array('clearfix', 'animated', 'fadeInDown');

if ($isContentHeroPage || $is404Page) {
    $headerClassNames[] = 'header--compact';
    $headerClassNames[] = 'header--hide-meta';
}

if ($isContentHeroPage) {
    $headerClassNames[] = 'header--compact-mobile';
}

$headerBackgroundCss = sprintf(
    "--aria-header-bg: url('%s');",
    str_replace(
        array('\\', "'"),
        array('\\\\', "\\'"),
        $headerBackgroundUrl
    )
);
?>
<?php include __DIR__ . '/components/header/hero.php'; ?>
<div id="body" class="animated fadeIn">
    <div class="container">
        <div class="row">
