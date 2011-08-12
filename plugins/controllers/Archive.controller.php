<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

# The archive plugin lists all blog entries by month and date.
require_once 'app/models/Blog.model.php';

final class Archive {

  public final function show() {
    $oModel = new Model_Blog();
    $aData = $oModel->getData('', false, PLUGIN_ARCHIVE_LIMIT);

    foreach ($aData as $aRow) {
      $m = strftime('%B %Y', $aRow['date_raw']);
      $id = $aRow['id'];
      $aMonth[$m][$id] = $aRow;
    }

    $oSmarty = new Smarty();
    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;

    $oSmarty->assign('data', $aMonth);

    $oSmarty->template_dir = Helper::getPluginTemplateDir('archive', 'show');
    return $oSmarty->fetch('show.tpl');
  }
}