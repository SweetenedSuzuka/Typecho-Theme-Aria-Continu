<?php
// Typecho content/comment hook wiring.

Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('Contents', 'parse');
Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('Contents', 'parse');
Typecho_Plugin::factory('Widget_Abstract_Comments')->contentEx = array('Comments', 'parse');
