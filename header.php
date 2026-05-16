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

<?php $headerViewData = Utils::getHeaderViewData($this, !empty($GLOBALS['ARIA_IS_404_PAGE'])); ?>

	<!-- 使用url函数转换相关路径 -->

    <link rel="icon" type="image/ico" href="/favicon.ico">
    <?php foreach ($headerViewData['head']['styles'] as $styleUrl): ?>
    <link href="<?php echo htmlspecialchars($styleUrl, ENT_QUOTES, 'UTF-8'); ?>" rel="stylesheet">
    <?php endforeach; ?>
    <?php foreach ($headerViewData['head']['scripts'] as $scriptUrl): ?>
    <script src="<?php echo htmlspecialchars($scriptUrl, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <?php endforeach; ?>
    <?php echo $headerViewData['head']['customHtml']; ?>
	<!--[if lt IE 9]>
    <?php foreach ($headerViewData['head']['legacyScripts'] as $legacyScriptUrl): ?>
    <script src="<?php echo htmlspecialchars($legacyScriptUrl, ENT_QUOTES, 'UTF-8'); ?>"></script>
    <?php endforeach; ?>
    <![endif]-->
</head>
<body<?php if ($headerViewData['body']['className'] !== ''): ?> class="<?php echo htmlspecialchars($headerViewData['body']['className'], ENT_QUOTES, 'UTF-8'); ?>"<?php endif; ?><?php if ($headerViewData['body']['style'] !== ''): ?> style="<?php echo htmlspecialchars($headerViewData['body']['style'], ENT_QUOTES, 'UTF-8'); ?>"<?php endif; ?>>
<?php Utils::AriaConfig(); ?>
<?php include __DIR__ . '/components/header/navigation.php'; ?>
<?php include __DIR__ . '/components/header/search-box.php'; ?>
<div id="pjax-container">
<?php include __DIR__ . '/components/header/hero.php'; ?>
<div id="body" class="animated fadeIn">
    <div class="container">
        <div class="row">
