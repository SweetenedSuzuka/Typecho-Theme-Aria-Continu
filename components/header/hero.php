<header id="header" class="clearfix animated fadeInDown">
    <div id="site-meta">
            <h1 id="site-name"><?php $this->options->title(); ?></h1>
            <?php if ($heroSubtitle !== ''): ?>
                <h2 id="site-description"><?php echo htmlspecialchars($heroSubtitle, ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php endif; ?>
    </div>
    <div id="background"></div>
</header><!-- end #header -->
