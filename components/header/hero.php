<header
    id="header"
    class="<?php echo htmlspecialchars($headerViewData['hero']['className'], ENT_QUOTES, 'UTF-8'); ?>"
    style="<?php echo htmlspecialchars($headerViewData['hero']['backgroundCss'], ENT_QUOTES, 'UTF-8'); ?>"
>
    <div id="site-meta">
            <h1 id="site-name"><?php $this->options->title(); ?></h1>
            <?php if ($headerViewData['hero']['subtitle'] !== ''): ?>
                <h2 id="site-description"><?php echo htmlspecialchars($headerViewData['hero']['subtitle'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php endif; ?>
    </div>
    <div id="background"></div>
</header><!-- end #header -->
