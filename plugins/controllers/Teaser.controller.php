<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 * @todo fix redirection bug
 */

namespace CandyCMS\Plugin;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;

require_once 'app/controllers/Blog.controller.php';


# Show the last blog entry with teaser text
final class Teaser extends \CandyCMS\Controller\Blog {

  public final function show() {
    $aData = $this->_oModel->getData('', false, 1);

    $this->oSmarty->assign('data', $aData);
    $this->oSmarty->template_dir = Helper::getPluginTemplateDir('teaser', 'show');
    return $this->oSmarty->fetch('show.tpl');
  }
}