<?php

/*
 * @link http://github.com/marcoraddatz/candyCMS
 * @author Marco Raddatz <http://marcoraddatz.com>
 */

namespace CandyCMS\Plugin;

use CandyCMS\Helper\Helper as Helper;
use CandyCMS\Helper\I18n as I18n;
use Smarty;

require_once PATH_STANDARD . '/app/controllers/Blog.controller.php';

# Show the last six headlines of your blog entries.

final class Headlines extends \CandyCMS\Controller\Blog {

  /**
   * @todo
   * @return type
   */
  public final function show() {
    $sTemplateDir   = Helper::getPluginTemplateDir('headlines', 'show');
    $sTemplateFile  = Helper::getTemplateType($sTemplateDir, 'show');

    $this->oSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
    $this->oSmarty->setCacheLifetime(300);

    if (!$this->oSmarty->isCached($sTemplateFile, UNIQUE_ID))
      $this->oSmarty->assign('data', $this->_oModel->getData('', false, PLUGIN_HEADLINES_LIMIT));

    $this->oSmarty->setTemplateDir($sTemplateDir);
    return $this->oSmarty->fetch($sTemplateFile, UNIQUE_ID);
  }
}