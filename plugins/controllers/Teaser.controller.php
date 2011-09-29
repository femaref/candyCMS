<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

namespace CandyCMS\Plugin;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Model\Blog as Model;

require_once 'app/models/Blog.model.php';

# Show the last blog entry with teaser text
final class Teaser {

  public final function show() {
    $oModel = new Model();
    $aData = $oModel->getData('', false, 1);

    $oSmarty = new \Smarty();
    $oSmarty->cache_dir = CACHE_DIR;
    $oSmarty->compile_dir = COMPILE_DIR;

    $oSmarty->assign('data', $aData);
    $oSmarty->template_dir = Helper::getPluginTemplateDir('teaser', 'show');
    return $oSmarty->fetch('show.tpl');
  }
}