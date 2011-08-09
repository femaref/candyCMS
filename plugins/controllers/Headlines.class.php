<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

require_once 'app/models/Blog.model.php';

# Show the last six headlines of your blog entries.
final class Headlines {

  public final function show() {

    $oModel = new Model_Blog();
    $aData = $oModel->getData('', false, PLUGIN_HEADLINES_LIMIT);

    $oSmarty = new Smarty();
    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;

    $oSmarty->assign('data', $aData);
    $oSmarty->template_dir = Helper::getPluginTemplateDir('headlines', 'show');
    return $oSmarty->fetch('show.tpl');
  }
}